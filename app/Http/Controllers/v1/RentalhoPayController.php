<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\MovementAmount;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class RentalhoPayController extends Controller
{
    public static function payWithRentalhoPay(
        $amount,
        $concept,
        $reference,
        $urlSuccess,
        $urlFailed,
        $urlNotification,
        $token
    ) {

        try {
            $url =  'https://ptest.rentalho.com/api/pay-with-rentalho';

            $data = [
                'reference' => $reference,
                'concept' => $concept,
                'amount' => $amount,
                'urlSuccess' => 'http://'.$urlSuccess,
                'urlFailed' => 'http://'.$urlFailed,
                'urlNotification' => $urlNotification,
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
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$token
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
                    'data' =>  $json->{'data'},
                ];
            }
        } catch (\Throwable $th) {
            return [
                'error' => 1,
                'message' => $th->getMessage(),

            ];
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

                    //Falta enviar notificacion al cliente de que el pago ha sido rechazado

                }
            }
            else {

                $ordersIds = json_decode($movement->orders_id);

                foreach ($ordersIds as $id_order){
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

                        //Falta enviar notificacion al cliente de que el pago ha sido rechazado

                    }
                }

            }

        }

        return 'ok';
    }

}
