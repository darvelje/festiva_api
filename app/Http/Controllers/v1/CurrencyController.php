<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ShopCurrency;
use App\Models\ShopProduct;
use App\Models\ShopProductsPricesrate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

        if($currency){
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Currency',
                    'currency' => $currency
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

    //section New_Currency
    public function newCurrency(Request $request){

        try{
            DB::beginTransaction();

            $validateRequest = Validator::make($request->all(), [
                'currencyName' => 'required|min:3|max:255|string',
                'currencyCode' => 'required|min:3|max:5|string|unique:currencies,code',
                'currencyRate' => 'required',
                'currencyMain' => 'required',
            ]);

            if($validateRequest->fails()){
                return response()->json(
                    [
                        'code' => 'error',
                        'errors' => $validateRequest->errors()
                    ]);
            }

            $currency = new Currency();

            $currency->name = $request->currencyName;
            $currency->code = $request->currencyCode;
            $currency->main = $request->currencyMain;
            $currency->rate = $request->currencyRate;

            $currency->save();

            $array_shop_ids = ShopCurrency::all()->pluck('shop_id')->unique()->values();

            foreach ($array_shop_ids as $idShop){

                $shopCurrency = new ShopCurrency();

                $shopCurrency->shop_id = $idShop;
                $shopCurrency->currency_id = $currency->id;
                $shopCurrency->rate = $currency->rate;
                $shopCurrency->main = false;

                $shopCurrency->save();

                $shopProducts = ShopProduct::where('shop_id', $idShop)->get()->pluck('id')->values();

                foreach ($shopProducts as $idProduct){

                    $productPrice = new ShopProductsPricesrate();

                    $productPrice->shop_product_id = $idProduct;
                    $productPrice->currency_id = $currency->id;
                    $productPrice->price = $shopCurrency->rate;

                    $productPrice->save();

                }
            }

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Currency created successfully'
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

            $validateRequest = Validator::make($request->all(), [
                'currencyName' => 'required|min:3|max:255|string',
                'currencyRate' => 'required',
                'currencyMain' => 'required',
                'currencyCode' => 'required|min:3|max:5|string|unique:currencies,code,'.$request->currencyId,
            ]);

            if($validateRequest->fails()){
                return response()->json(
                    [
                        'code' => 'error',
                        'errors' => $validateRequest->errors()
                    ]);
            }

            $currency = Currency::whereId($request->currencyId)->first();

            $currency->name = $request->currencyName;
            $currency->code = $request->currencyCode;
            $currency->main = $request->currencyMain;

            $currency->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Currency updated successfully'
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
