<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\MovementAmount;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class TropiPayController extends Controller
{
    public static function payWithTropiPay(
        $mode,
        $amount,
        $saveToken,
        $provider,
        $currency,
        $concept,
        $description,
        $reference,
        $urlSuccess,
        $urlFailed,
        $urlNotification,
        $directPayment,
        $serviceDate,
        $singleUse,
        $reasonId,
        $expirationDays,
        $lang,
        $client,
        $clientTermsAndConditions
    ) {

        try {

            if($mode == 'sandbox'){
              $url =  'https://pproxy.rentalho.com/pay/do-sync?env=dev';
            }else{
               $url =  'https://pproxy.rentalho.com/pay/do-sync?env=prod';
            }

            $data = [
                'amount' => $amount,
                'saveToken' => $saveToken,
                'provider' => $provider,
                'currency' => $currency,
                'concept' => $concept,
                'description' => $description,
                'reference' => $reference,
                'urlSuccess' => 'http://'.$urlSuccess,
                'urlFailed' => 'http://'.$urlFailed,
                'urlNotification' => $urlNotification,
                'directPayment' => $directPayment,
                'serviceDate' => $serviceDate,
                'singleUse' => $singleUse,
                'reasonId' => $reasonId,
                'expirationDays' => $expirationDays,
                'lang' => $lang,
                'clientName' => $client['clientName'],
                'clientLastName' =>  $client['clientLastName'],
                'clientEmail' =>  $client['clientEmail'],
                'clientCountryId' =>  $client['clientCountry'],
                'clientPhone' =>  $client['clientPhone'],
                'clientAddress' =>  $client['clientAddress'],
                'clientTermsAndConditions' => $clientTermsAndConditions,
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
                    'Content-Type: application/json'
                ),
            ));

            $result = curl_exec($curl);

            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($http_status == 500) {

                return ['error' => 500,'result' => $result,'data'=>json_encode($data)];
            } else {
                $json = json_decode($result);

                return [
                    'error' => 0,
                    'url' =>  $json->{'paymentUrl'},
                ];
            }
        } catch (\Throwable $th) {
            return [
                'error' => 1,
                'message' => $th->getMessage(),

            ];
        }
    }


    public static function getCountries(){

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

    public function paymentCompleteURL(Request $request){

        $order = ShopOrder::with('shop')->find(explode('-', $request->reference)[0]);

        if ($order) {
            return view('order-completed', compact(['order', $order]));
        } else {
            return redirect('https://crecexdiez.com');
        }
    }


    public function paymentCompleteURLError(Request $request){
        $order = ShopOrder::with('shop')->find(explode('-', $request->reference)[0]);

        if ($order) {
            $orderOld = $order;
            $order->delete();
            return view('order-error', compact(['order', $orderOld]));
        } else {
            return redirect('https://crecexdiez.com');
        }
    }


    public function responseNotification(Request $request){

        $request = json_decode($request->getContent(), true);

//        Log::debug('Request data', [$request]);

        $movement = MovementAmount::find($request['reference']);

        if ($movement) {

            $movement->fee = ( $movement->amount + $movement->fee ) - ($request['destinationAmount'] / 100) ;
            $movement->status = 'completed';
            $movement->update();

            if ($movement->model == 'order') {

                $order = Order::find($movement->model_id);

                if ($request['status'] == 'OK') {
                    $order->payload_response = json_encode($request);
                    $order->update();
                    OrderController::orderPaid($order);

                }
                else {

                    $order->payment_status = 'failed';
                    $order->status = 3;
                    $order->update();

                    // todo Falta enviar notificacion al cliente de que el pago ha sido rechazado

                }
            }
            else {

                $ordersIds = json_decode($movement->orders_id);

                Log::debug('Orders ID', [$ordersIds]);

                foreach ($ordersIds as $id_order){

                    Log::debug('Order ID ', [$id_order]);

                    $order = Order::find($id_order);

                    if ($request['status'] == 'OK') {
                        $order->payload_response =  json_encode($request);
                        $order->update();
                        OrderController::orderPaid($order);

                    }
                    else{

                        $order->payment_status = 'failed';
                        $order->status = 3;
                        $order->update();

                        // todo Falta enviar notificacion al cliente de que el pago ha sido rechazado

                    }
                }

            }

        }

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
