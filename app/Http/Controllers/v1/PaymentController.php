<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function newPayment(Request $request)
    {

        DB::beginTransaction();

        $userDb = $request->user();

        $order = OrderController::newOrder(
            $request->data,
            $userDb->id,
        );


        return response()->json([
            'code' => 'test order',
            'data' => $request->all(),
        ]);


        if ($order == '501' || $order == '500' || $order == '499' || $order == '498') {
            switch ($order) {
                case '501':
                    return response()->json([
                        'code' => 'error',
                        'message' => 'Error de autentificación en el procesaror de pago',
                    ]);
                    break;
                case '500':
                    return response()->json([
                        'code' => 'error',
                        'message' => 'Error con el procesaror de pago',
                    ]);
                    break;

                case '499':
                    return response()->json([
                        'code' => 'error',
                        'message' => 'Tropipay is disabled',
                    ]);
                    break;
                case '498':
                    return response()->json([
                        'code' => 'error',
                        'message' => 'Este pedido no lleva productos',
                    ]);
                    break;
                case '496':
                    return response()->json([
                        'code' => 'error',
                        'message' => 'Tienda no encontrada',
                    ]);
                    break;
            }
        }


        $restaurant = Restaurant::find($restaurantId);

        //Update the zone
        $zone = $restaurant->zones->where('municipalitie_id', $request->shopLocation)->first();

        if($zone){
            $order->zone_id = $zone->id;
            $order->update();
        }

        if ($order && $restaurant) {

            if ($request->method_payment == 'tropipay' || $request->method_payment == 'tropipayCrece') {
                $movementPending = MovementsBalanceController::new_movement_pending('order', 'order', $order->id, $order->total, 'tropipay', 'Pago del pedido: ' . $order->id, 'USD', $order->user_id);

                return $this->newPaymentWithTropiPay($request->method_payment, $order, $movementPending, $request->client, $request->currency_code);
            }
        } else {

            return response()->json([
                'code' => 'error',
                'message' => 'Order or Shop not found'
            ], 404);
        }
    }

    public function newPaymentWithTropiPay( $mode, $order, $movementPending, $client, $currencyCode)
    {


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
