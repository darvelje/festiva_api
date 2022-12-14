<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Api\v1\AdminShopController;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\HouseBooking;
use App\Models\MovementAmount;
use App\Models\MovementsBalancePending;
use App\Models\Order;
use App\Models\Setting;
use App\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Str;

class TropiPayController extends Controller
{
    public static function payWithTropiPay(
        $mode,
        $reference,
        $concept,
        $favorite,
        $description,
        $amount,
        $currency,
        $singleUse,
        $reasonId,
        $expirationDays,
        $lang,
        $urlSuccess,
        $urlFailed,
        $urlNotification,
        $serviceDate,
        $client,
        $directPayment,
        $paymentMethods,
        $clientId,
        $clientSecret
    ) {

        try {
            $token = self::auth($mode, $clientId, $clientSecret);

            if ($token['error'] == 501) {
                return $token;
            }

            if($mode == 'sandbox'){
              $url =  'https://tropipay-dev.herokuapp.com/api/v2/paymentcards';
            }else{
               $url =  'https://www.tropipay.com/api/v2/paymentcards';
            }

            $data = [
                'reference' => $reference,
                'concept' => $concept,
                'favorite' => $favorite,
                'description' => $description,
                'amount' => intval($amount),
                'currency' => $currency,
                'singleUse' => $singleUse,
                'reasonId' => $reasonId,
                'expirationDays' => $expirationDays,
                'lang' => $lang,
                'urlSuccess' => $urlSuccess,
                'urlFailed' => $urlFailed,
                'urlNotification' => $urlNotification,
                'serviceDate' => $serviceDate,
                'client' => $client,
                'directPayment' => $directPayment,
                'paymentMethods' => $paymentMethods,
            ];


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token['token'],
                    'Content-Type: application/json'
                ),
            ));




            $result = curl_exec($curl);

            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);


            if ($http_status != 200) {

                return ['error' => 500,'result' => $result,'data'=>json_encode($data)];
            } else {
                $json = json_decode($result);


                $id = $json->{'id'};
                $userId = $json->{'userId'};
                $state = $json->{'state'};
                $qrImage = $json->{'qrImage'};
                $shortUrl = $json->{'shortUrl'};
                //save movement

                return [
                    'error' => 0,
                    'id' => $id,
                    'url' => $shortUrl,
                    'qrImage' => $qrImage,
                    'state' => $state,
                    'userId' => $userId,
                    'result' => $result
                ];
            }
        } catch (\Throwable $th) {
            NotificaController::NotificaAdmin('error', Str::limit($th->getMessage(), 350));
        }
    }


    public static function getCountries()
    {

        $setting = Setting::first();

        $clientId = $setting->tropipay_client;
        $clientSecret =  $setting->tropipay_secret;
        $mode = $setting->tropipay_mode;

        $token = self::auth($mode, $clientId, $clientSecret);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://tropipay-dev.herokuapp.com/api/v2/countries',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token['token'],
                'Content-Type: application/json'
            ),
        ));


        $result = curl_exec($curl);

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);



        if ($http_status != 200) {
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Error al obtener los paises'
                ]
            );
        } else {
            $json = json_decode($result, true);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Success',
                    'data' => $json
                ]
            );
        }
    }

    public static function auth($mode, $clientId, $clientSecret)
    {

        if($mode == 'sandbox'){
            $url =  'https://tropipay-dev.herokuapp.com/api/v2/paymentcards';
        }else{
            $url =  'https://www.tropipay.com/api/v2/paymentcards';
        }



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id=' . $clientId . '&client_secret=' . $clientSecret . '&scope=ALLOW_EXTERNAL_CHARGE%20BLOCKED_MONEY_OUT',
            // CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id=$' . $clientId . '&client_secret=' . $clientSecret . '&scope=ALLOW_EXTERNAL_CHARGE%20BLOCKED_MONEY_OUT',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        return response()->json([
            'error'=>'error',
            'tropipayError' =>$response
        ]);

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);


        if ($http_status != 200) {
            return ['message' => 'Problema al conectarse con la pasarela - 501', 'error' => 501];
        } else {
            $json = json_decode($response);
            $token = $json->access_token;
            return ['error' => 0, 'token' => $token, 'code' => $http_status];
        }
    }

    public function paymentCompleteURL(Request $request)
    {

        $order = ShopOrder::with('shop')->find(explode('-', $request->reference)[0]);



        if ($order) {
            return view('order-completed', compact(['order', $order]));
        } else {
            return redirect('https://crecexdiez.com');
        }
    }


    public function paymentCompleteURLError(Request $request)
    {
        $order = ShopOrder::with('shop')->find(explode('-', $request->reference)[0]);

        if ($order) {
            $orderOld = $order;
            $order->delete();
            return view('order-error', compact(['order', $orderOld]));
        } else {
            return redirect('https://crecexdiez.com');
        }
    }

//    public function responseNotification(Request $request){
//        // $data = json_decode($request->getContent(), true);
//
//        DB::beginTransaction();
//        $request = json_decode($request->getContent(), true);
//        $data =  $request['data'];
//        $movementID = explode($data['reference']);
//
//        $movement = MovementAmount::find($movementID);
//
//        if ($movement) {
//
//            $currency = Currency::whereCode($data['destinationCurrency'])->first();
//
//            if($currency){
//                self::processOrder($movement, $data, $currency);
//            }
//            else{
//
//                $currency = new Currency();
//                $currency->name = $data['destinationCurrency'];
//                $currency->code = $data['destinationCurrency'];
//                $currency->main = false;
//                $currency->rate = 1;
//                $currency->save();
//
//                self::processOrder($movement, $data, $currency);
//            }
//
//        }
//
//        $movement->status = 'completed';
//        $movement->update();
//
//        DB::commit();
//
//        return 'ok';
//    }

    public function responseNotification(Request $request)
    {
        // DB::beginTransaction();

        $request = json_decode($request->getContent(), true);
        $data =  $request['data'];

        $movement = MovementsBalancePending::find($movementID);

        if ($movement) {

            if ($movement->type == 'order') {

                $order = Order::find($movement->model_id);
                // NotificaController::NotificaAdmin('error', 'Tiene orden: ' . $order->ref);

                if ($request['status'] == 'OK') {

                    //$requestSignature =  $data['signature'];
                    // $ourSignature = hash('sha256', $order->ref . $email . sha1($password) . $order->total * 100);

                    //  NotificaController::NotificaAdmin('error', 'Nuestra firma: ' . $ourSignature . ' Firma de la notificación: ' . $requestSignature);

                    //    if ($requestSignature != $ourSignature) {
                    //   NotificaController::NotificaAdmin('error', 'Error en la firma de la notificación en el pedido con referencia: ' . $order->ref);
                    //  return;
                    //  }

                    $order->payload_response =  json_encode($data);
                    $order->total = $data['destinationAmount'] / 100;
                    $order->currency_code = $data['destinationCurrency'];
                    // $order->status_payment_external = $data['state'];
                    $order->update();


                    OrderController::orderPaid($order->id);
                } elseif ($data['state'] == 4) {

                    $order->payment_status = 'failed';
                    $order->status = 'canceled';
                    $order->update();

                    //Falta enviar notificacion al cliente de que el pago ha sido rechazado

                    NotificaController::NotificaAdmin('error', 'El pago de la orden #' . $order->red . ' ha sido rechazado por la pasarela de pagos');
                }
            } elseif ($movement->type == 'booking') {

                if ($data['state'] == 5 &&  $request['status'] == 'OK') {

                    $booking = Reservation::find($movement->model_id);
                    $booking->price = $data['destinationAmount'] / 100;
                    $booking->currency_code = $data['destinationCurrency'];
                    $booking->update();

                    BookingController::bookingPaid($booking->id);
                } elseif ($data['state'] == 4) {

                    $booking = Reservation::find($movement->model_id);
                    $booking->payment_status = 'failed';
                    $booking->status = 'canceled';
                    $booking->update();

                    NotificaController::NotificaAdmin('error', 'El pago de la reserva #' . $booking->red . ' ha sido rechazado por la pasarela de pagos');

                    //Falta enviar notificacion al cliente

                }
            }


            $movement->delete();
        }

        //   DB::commit();

        return 'ok';
    }

    public static function processOrder($movement, $data, $currency){
        if ($movement->model == 'order') {
            $order = Order::find($movement->model_id);
            $order->total = $data['destinationAmount'] / 100;
            $order->currency_id = $currency->id;
            $order->status_payment = 'completed';
            $order->update();
            OrderController::changeStatusOrderPayed($order, 2);
        }

    }
}
