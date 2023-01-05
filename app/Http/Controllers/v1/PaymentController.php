<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\ShopCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Collection;

class PaymentController extends Controller
{
    public function newPayment(Request $request){

        DB::beginTransaction();

        //$urlRequest = $request->host();

        $userDb = $request->user();

        $generalData = collect([
           // 'currencyId' => $request->order['currencyId'],
            'deliveryCost' => $request->order['deliveryCost'],
            'methodDelivery' => $request->order['methodDelivery'],
            'discountCost' => $request->order['discountCost'],
            'commissionCost' => $request->order['commissionCost'],
            'methodPayment' => $request->order['methodPayment'],
            'tokenAuth' => $request->order['tokenAuth'],

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
                $receiver,
                $client
            );

            $ordersIds->add($order);

            $orderTotalPrice += $order->total_price;

        }

        $commissionCost = 0;
        if($generalData['methodPayment'] == 'tropipay'){
            foreach ($request->order['commissionCost'] as $commission){
                if($commission['currency_code'] === 'EUR'){
                    $commissionCost += $commission['price'];
                }
            }
        }
        else if($generalData['methodPayment'] == 'rentalho'){
            foreach ($request->order['commissionCost'] as $commission){
                if($commission['currency_code'] === 'USD'){
                    $commissionCost += $commission['price'];
                }
            }
        }

        if ($ordersIds->count() > 0) {

            $order = $ordersIds->first();
            $movementPending = collect();

            if ($generalData['methodPayment'] == 'tropipay') {
                //code here tropipay
                if($ordersIds->count()==1 ){
                    $movementPending = MovementAmountController::newMovement('order', $order->id,null, $orderTotalPrice,
                        'tropipay', 'Pago del pedido: ' . $order->id,  $order->currency_id, true,
                        'pending', 'earning', $commissionCost);
                }elseif($ordersIds->count()>1){
                    $ordersIds = $ordersIds->pluck('id')->toArray();
                    $movementPending = MovementAmountController::newMovement('orders', null,json_encode($ordersIds,true), $orderTotalPrice,
                        'tropipay', 'Pago de los pedidos: ' . json_encode($ordersIds,true),  $order->currency_id, true,
                        'pending', 'earning', $commissionCost);
                }

               return $this->newPaymentWithTropiPay($movementPending,$client);
            }
            else if($generalData['methodPayment'] == 'rentalho'){

                if($ordersIds->count()==1 ){
                    $movementPending = MovementAmountController::newMovement('order', $order->id,null, $orderTotalPrice,
                        'rentalho', 'Pago del pedido: ' . $order->id,  $order->currency_id, true,
                        'pending', 'earning', $commissionCost);
                }elseif($ordersIds->count()>1){
                    $ordersIds = $ordersIds->pluck('id')->toArray();
                    $movementPending = MovementAmountController::newMovement('orders', null,json_encode($ordersIds,true), $orderTotalPrice,
                        'rentalho', 'Pago de los pedidos: ' . json_encode($ordersIds,true),  $order->currency_id, true,
                        'pending', 'earning', $commissionCost);
                }

                return $this->newPaymentWithRentalho($movementPending,$generalData);
            }
        }

        return response()->json([
            'code' => 'error',
            'message' => 'Order or Shop not found',
        ]);

    }

    public function newPaymentWithTropiPay($movementPending, $client){

        $payment = PaymentMethod::where('name','Tropipay')->first();

        $currency = Currency::find($movementPending->currency_id);

        if(!$currency){
            //return error
        }

        $mode = $payment->mode;

        $result = TropiPayController::payWithTropiPay(
            $mode,
            round(($movementPending->amount + $movementPending->fee) * 100, 2),
            false,
            'TPP',
            $currency->code,
            $movementPending->detail,
            $movementPending->detail,
            $movementPending->id,
            '127.0.0.1:5173'.'/pagocompletado',
            '127.0.0.1:5173'.'/errorenpago',
            'https://stylla.app/api/v1/tropipay/api/notification',
            true,
            now()->timezone('Europe/Madrid')->format('Y-m-d'),
            true,
            2,
            0,
            'en',
            $client,
            true
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
            'url' => $result['url'],
            'result'=>$result
        ]);
    }

    public function newPaymentWithRentalho($movementPending, $data){

        $currency = Currency::find($movementPending->currency_id);

        if(!$currency){
            //return error
        }

        $result = RentalhoPayController::payWithRentalhoPay(
            round(($movementPending->amount + $movementPending->fee), 2),
            $movementPending->detail,
            $movementPending->id,
            '127.0.0.1:5173'.'/pagocompletado',
            '127.0.0.1:5173'.'/errorenpago',
            'https://stylla.app/api/v1/rentalho/api/notification',
            $data['tokenAuth'],
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

        $movementPending->url = $result['data']['payURL'];
      //  $movementPending->transation_uuid = $result['id'];
        $movementPending->update();


        DB::commit();

        return response()->json([
            'code' => 'ok',
            'message' => 'Payment created',
            'url' => $result['data']['payURL'],
            'result'=>$result
        ]);
    }
}
