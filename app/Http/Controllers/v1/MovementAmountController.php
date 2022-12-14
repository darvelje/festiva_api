<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\Models\MovementAmount;
use Illuminate\Http\Request;

class MovementAmountController extends Controller
{

    public static function newMovement( $model, $model_id,$ordersIds, $amount, $method, $detail, $currency_id, $only_register, $status, $type){

        $movement = new MovementAmount();

        $movement->model = $model;
        $movement->model_id = $model_id;
        $movement->amount = $amount;
        $movement->method = $method;
        $movement->detail = $detail;
        $movement->currency_id = $currency_id;
        $movement->only_register = $only_register;
        $movement->status = $status;
        $movement->type = $type;

        $movement->save();

        return $movement;

    }
}
