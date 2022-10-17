<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class CurrencyController extends Controller
{

    //section Get_Currency
    public function getCurrencies(){

        $currencies = Currency::all();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Currencies',
                'currencies' => $currencies
            ]
        );
    }

    //section Get_Currency
    public function getCurrencyById(Request $request){

        $currency = Currency::whereId($request->currencyId)->first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Currency',
                'currency' => $currency
            ]
        );
    }

    //section New_Currency
    public function newCurrency(Request $request){

        try{
            DB::beginTransaction();

            $currency = new Currency();

            $currency->name = $request->currencyName;
            $currency->rate = Str::slug($request->currencyRate);
            $currency->main = Str::slug($request->currencyMain);

            $currency->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Currency created successfully',
                    'currency' => $currency
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Currency
    public function updateCurrency(Request $request){

        try{
            DB::beginTransaction();

            $currency = Currency::whereId($request->currencyId)->first();

            $currency->name = $request->currencyName;
            $currency->rate = Str::slug($request->currencyRate);
            $currency->main = Str::slug($request->currencyMain);

            $currency->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Currency updated successfully',
                    'currency' => $currency
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_Currency
    public function deleteCurrency(Request $request){
        try {
            DB::beginTransaction();

            $result = Currency::whereId($request->currencyId)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Currency deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Currency not found'
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
