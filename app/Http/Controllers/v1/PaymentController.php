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



        $ordersIdsCollectionNew = $request->order['products'];

        $newProduct = collect($ordersIdsCollectionNew);

        $products = $newProduct->groupBy('idShop');

       // $ordersIds = $ordersIdsCollectionNew->products->pluck('idShop')->unique()->toArray();

        return response()->json([
            'code' => 'test',
            'return' => $products
        ]);



        $order = OrderController::newOrder(
            $request->order,
            $userDb->id,
        );

        if ($order) {
            if ($request->methodPayment == 'tropipay') {
                $movementPending = MovementAmountController::newMovement('order', $order->id, $order->total,
                    'tropipay', 'Pago del pedido: ' . $order->id,  $order->currency_id, true,
                    'pending', 'earning');

                return $this->newPaymentWithTropiPay($order, $movementPending, $request->order->client);
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
