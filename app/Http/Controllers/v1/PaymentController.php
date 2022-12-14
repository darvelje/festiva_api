<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ShopCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function newPayment(Request $request){

        DB::beginTransaction();

        $userDb = $request->user();

        $generalData = collect([
            'currencyId' => $request->order['currencyId'],
            'deliveryCost' => $request->order['deliveryCost'],
            'methodDelivery' => $request->order['methodDelivery'],
            'discountCost' => $request->order['discountCost'],
            'methodPayment' => $request->order['methodPayment'],

        ]);

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
                        'tropipay', 'Pago del pedido: ' . $order->id,  $generalData['currency_id'], true,
                        'pending', 'earning');
                }elseif($ordersIds->count()>1){
                    $ordersIds = $ordersIds->pluck('id')->toArray();
                    $movementPending = MovementAmountController::newMovement('orders', null,json_encode($ordersIds,true), $orderTotalPrice,
                        'tropipay', 'Pago de los pedidos: ' . $ordersIds,  $generalData['currency_id'], true,
                        'pending', 'earning');
                }

                return response()->json([
                    'code' => 'movements',
                    'message' => $movementPending
                ]);

               // return $this->newPaymentWithTropiPay($order, $movementPending, $request->order->client);
            }
        } else {

            return response()->json([
                'code' => 'error',
                'message' => 'Order or Shop not found'
            ], 404);
        }
    }

    public function newPaymentWithTropiPay($order, $movementPending, $client){


        $setting = Setting::first();

        $clientId = $setting->tropipay_client;
        $clientSecret =  $setting->tropipay_secret;
        $order->method_payment = 'tropipay';

        $client = $client;



        // return response()->json([
        //     'code' => 'order',
        //     'message' => 'Ocurrió un error con la pasarela de pagos - Error Interno del Servidor',
        //     'order' => $order,

        // ]);

        $result = TropiPayController::payWithTropiPay(
            'live',
            $order->id . '-' . $movementPending->id,
            'Pago del pedido: ' . $order->id,
            false,
            'Pago del pedido: ' . $order->id,
            $order->total * 100,
            $currencyCode,
            true,
            4,
            1,
            'es',
            'https://yavoycuba.com/pagocompletado',
            'https://yavoycuba.com/errorenpago',
            'https://app.yavoycuba.com/api/tropipay/api/notification',
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
        $movementPending->transation_uuid = $result['id'];
        $movementPending->update();


        $order->update();

        DB::commit();

        return response()->json([
            'code' => 'ok',
            'message' => 'Payment created',
            'url' => $result['url']
        ]);
    }
}
