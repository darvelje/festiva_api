<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\Models\MovementAmount;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodsController extends Controller
{

    public static function getPaymentMethod(){

        $paymentMethod = PaymentMethod::all();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Payment methods',
                'payment_method' => $paymentMethod
            ]
        );


    }

    public static function newPaymentMethod( Request $request){

        try{
            DB::beginTransaction();

            $paymentMethod = new PaymentMethod();

            $paymentMethod->name = $request->methodName;
            $paymentMethod->mode = $request->methodMode;
            $paymentMethod->client_id = $request->methodClientId;
            $paymentMethod->client_secret = $request->methodClientSecret;
            $paymentMethod->status = $request->methodStatus;

            $paymentMethod->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Payment methods created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }

    }
}
