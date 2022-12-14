<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\ShopCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function newPayment(Request $request){

        DB::beginTransaction();

        $urlRequest = $request->host();

        $userDb = $request->user();

        $generalData = collect([
            'currencyId' => $request->order['currencyId'],
            'deliveryCost' => $request->order['deliveryCost'],
            'methodDelivery' => $request->order['methodDelivery'],
            'discountCost' => $request->order['discountCost'],
            'methodPayment' => $request->order['methodPayment'],

        ]);

        $client = $request->order['client'];

        $receiver = collect([
            'userName' => $request->order['userName'],
            'userEmail' => $request->order['userEmail'],
            'userAddress' => $request->order['userAddress'],
            'userPhone' => $request->order['userPhone'],
        ]);

        $ordersIds = collect();
        $orderTotalPrice = 0;

        foreach ($request->order['orders'] as $order){

            $order = OrderController::newOrder(
                $order,
                $userDb->id,
                $generalData,
                $receiver
            );

            $ordersIds->add($order);
            $orderTotalPrice += $order->total_price;

        }


        if ($ordersIds->count()>0) {
            if ($generalData['methodPayment'] == 'tropipay') {

                if($ordersIds->count()==1 ){
                    $order = $ordersIds->first();
                    $movementPending = MovementAmountController::newMovement('order', $order->id,null, $order->total_price,
                        'tropipay', 'Pago del pedido: ' . $order->id,  $generalData['currencyId'], true,
                        'pending', 'earning');
                }elseif($ordersIds->count()>1){
                    $ordersIds = $ordersIds->pluck('id')->toArray();
                    $movementPending = MovementAmountController::newMovement('orders', null,json_encode($ordersIds,true), $orderTotalPrice,
                        'tropipay', 'Pago de los pedidos: ' . json_encode($ordersIds,true),  $generalData['currencyId'], true,
                        'pending', 'earning');
                }

               return $this->newPaymentWithTropiPay($movementPending,$client,$generalData,$receiver,$urlRequest);
            }
        } else {

            return response()->json([
                'code' => 'error',
                'message' => 'Order or Shop not found'
            ], 404);
        }
    }

    public function newPaymentWithTropiPay($movementPending, $client,$data,$receiver,$urlRequest){

        $payment = PaymentMethod::where('name','Tropipay')->first();
        $currency = Currency::find($movementPending->currency_id);


        if(!$payment || !$currency){
            //return error
        }

        $clientId = $payment->client_id;
        $clientSecret =  $payment->client_secret;
        $mode = $payment->mode;

        $result = TropiPayController::payWithTropiPay(
            $mode,
            $movementPending->id,
            $movementPending->detail,
            false,
             $movementPending->detail,
            $movementPending->amount * 100,
            $currency->code,
            true,
            4,
            1,
            'es',
            $urlRequest.'/pagocompletado',
            $urlRequest.'/errorenpago',
            env('APP_URL').'/api/v1/tropipay/api/notification',
                    now()->timezone('Europe/Madrid')->format('Y-m-d'),
            $client,
            true,
            ["EXT", "TPP"],
            $clientId,
            $clientSecret
        );

        if ($result['error'] == '500') {
            return response()->json([
                'code' => 'error',
                'message' => 'Ocurrió un error con la pasarela de pagos - Error Interno del Servidor',
                'result' => $result['result'],
                'data' => $result['data']
            ]);
        }

        if ($result['error'] == '501') {
            return response()->json([
                'code' => 'error',
                'message' => 'Ocurrió un error con la pasarela de pagos - Autentificación'
            ]);
        }

        $movementPending->url = $result['url'];
      //  $movementPending->transation_uuid = $result['id'];
        $movementPending->update();


        DB::commit();

        return response()->json([
            'code' => 'ok',
            'message' => 'Payment created',
            'url' => $result['url']
        ]);
    }
}
