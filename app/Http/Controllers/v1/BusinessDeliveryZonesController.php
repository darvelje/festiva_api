<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\ShopDeliveryZone;
use App\Models\ShopZonesDeliveryPricesrate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BusinessDeliveryZonesController extends Controller
{

    //section Get_Business_Delivery_Zones
    public function getBusinessDeliveryZonesByBusinessSlug(Request $request){

        $shop= Shop::whereSlug($request->businessUrl)->first();

        if($shop){

            $allZones = collect();

            $zonesShop = ShopDeliveryZone::with('shopZonesDeliveryPricesrates', 'shopZonesDeliveryPricesrates.currency')->where('shop_id', $shop->id)->get();

            foreach ($zonesShop as $zone) {
                $allZones->push($zone);
            }

            $allZones->map(function ($zone) {
                if($zone->localitie_id === null && $zone->municipalitie_id === null){
                   $zone->province_name = $zone->province->name;
                }
                else if($zone->localitie_id === null && $zone->municipalitie_id !== null){
                    $zone->municipalitie_name = $zone->municipality->name;
                    $zone->province_name = $zone->province->name;
                }
                else if($zone->localitie_id !== null){
                    $zone->localitie_name = $zone->locality->name;
                    $zone->municipalitie_name = $zone->municipality->name;
                    $zone->province_name = $zone->province->name;
                }

                $zone->prices = $zone->shopZonesDeliveryPricesrates;

                foreach ($zone->prices as $price){
                    $price->currency_code = $price->currency->code;

                    unset($price->shop_zones_delivery_id);
                    unset($price->id);
                    unset($price->currency);
                    unset($price->created_at);
                    unset($price->updated_at);
                }

                unset($zone->shopZonesDeliveryPricesrates);
                unset($zone->created_at);
                unset($zone->updated_at);
                unset($zone->created_at);
                unset($zone->municipality);
                unset($zone->locality);
                unset($zone->province);

            });

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business delivery zones',
                    'shopDeliveryZones' => $allZones
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Shop not found'
                ]
            );
        }

    }

    //section Get_BusinessDeliveryZonesById
    public function getBusinessDeliveryZoneById(Request $request){

        $shopDeliveryZone = ShopDeliveryZone::with('shopZonesDeliveryPricesrates', 'shopZonesDeliveryPricesrates.currency')->whereId($request->businessDeliveryZoneId)->first();

       if($shopDeliveryZone){

               if($shopDeliveryZone->localitie_id === null && $shopDeliveryZone->municipalitie_id === null){
                   $shopDeliveryZone->province_name = $shopDeliveryZone->province->name;
               }
               else if($shopDeliveryZone->localitie_id === null && $shopDeliveryZone->municipalitie_id !== null){
                   $shopDeliveryZone->municipalitie_name = $shopDeliveryZone->municipality->name;
                   $shopDeliveryZone->province_name = $shopDeliveryZone->province->name;
               }
               else if($shopDeliveryZone->localitie_id !== null){
                   $shopDeliveryZone->localitie_name = $shopDeliveryZone->locality->name;
                   $shopDeliveryZone->municipalitie_name = $shopDeliveryZone->municipality->name;
                   $shopDeliveryZone->province_name = $shopDeliveryZone->province->name;
               }

               $shopDeliveryZone->prices = $shopDeliveryZone->shopZonesDeliveryPricesrates;

               foreach ($shopDeliveryZone->prices as $price){
                   $price->currency_code = $price->currency->code;

                   unset($price->shop_zones_delivery_id);
                   unset($price->id);
                   unset($price->currency);
                   unset($price->created_at);
                   unset($price->updated_at);
               }

               unset($shopDeliveryZone->shopZonesDeliveryPricesrates);
               unset($shopDeliveryZone->created_at);
               unset($shopDeliveryZone->updated_at);
               unset($shopDeliveryZone->created_at);
               unset($shopDeliveryZone->municipality);
               unset($shopDeliveryZone->locality);
               unset($shopDeliveryZone->province);

           return response()->json(
               [
                   'code' => 'ok',
                   'message' => 'Business delivery zone',
                   'shopDeliveryZone' => $shopDeliveryZone
               ]
           );
       }
        return response()->json(
            [
                'code' => 'error',
                'message' => 'Business delivery zone not found'
            ]
        );
    }

    //section New_Business_Delivery_Zones
    public function newBusinessDeliveryZone(Request $request){

        try{
            DB::beginTransaction();

            $validateRequest = Validator::make($request->all(), [
                'businessDeliveryZoneShopId' => 'required',
                'businessDeliveryZoneProvinceId' => 'required',
                'businessDeliveryZoneTime' => 'required',
                'businessDeliveryZoneTimeType' => 'required',
                'businessDeliveryZonePrices' => 'required',
            ]);

            if($validateRequest->fails()){
                return response()->json(
                    [
                        'code' => 'error',
                        'errors' => $validateRequest->errors()
                    ]);
            }

            $shopDeliveryZone = new ShopDeliveryZone();

            $shopDeliveryZone->shop_id = $request->businessDeliveryZoneShopId;
            $shopDeliveryZone->localitie_id = $request->businessDeliveryZoneLocalitieId;
            $shopDeliveryZone->municipalitie_id = $request->businessDeliveryZoneMunicipalitieId;
            $shopDeliveryZone->province_id = $request->businessDeliveryZoneProvinceId;
            $shopDeliveryZone->time = $request->businessDeliveryZoneTime;
            $shopDeliveryZone->time_type = $request->businessDeliveryZoneTimeType;

            $shopDeliveryZone->save();

            $shopCurrencies = ShopCurrency::with('currency')->where('shop_id', $request->businessDeliveryZoneShopId)->get();

            foreach ($shopCurrencies as $currency){

                $shopDeliveryZonePricesrate = new ShopZonesDeliveryPricesrate();
                $shopDeliveryZonePricesrate->shop_zones_delivery_id = $shopDeliveryZone->id;
                $shopDeliveryZonePricesrate->currency_id = $currency->currency->id;
                if($currency->currency->code === 'USD'){
                    $shopDeliveryZonePricesrate->price = $request->businessDeliveryZonePrices;
                }
                else{
                    $shopDeliveryZonePricesrate->price = $request->businessDeliveryZonePrices * $currency->rate;
                }

                $shopDeliveryZonePricesrate->save();

            }

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business delivery zone created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Business_Delivery_Zones
    public function updateBusinessDeliveryZone(Request $request){

        try{
            DB::beginTransaction();

            $shopDeliveryZone = ShopDeliveryZone::whereId($request->businessDeliveryZoneId)->first();

            $shopDeliveryZone->shop_id = $request->businessDeliveryZoneShopId;
            $shopDeliveryZone->localitie_id = $request->businessDeliveryZoneLocalitieId;
            $shopDeliveryZone->municipalitie_id = $request->businessDeliveryZoneMunicipalitieId;
            $shopDeliveryZone->province_id = $request->businessDeliveryZoneProvinceId;
            $shopDeliveryZone->time = $request->businessDeliveryZoneTime;
            $shopDeliveryZone->time_type = $request->businessDeliveryZoneTimeType;

            $shopDeliveryZone->update();

            $IdCurrencyUSD = Currency::whereCode('USD')->first()->id;

            $found = ShopZonesDeliveryPricesrate::where('currency_id', $IdCurrencyUSD)->where('shop_zones_delivery_id', $request->businessDeliveryZoneId)->where('price', $request->businessDeliveryZonePrices)->first();

            if(!$found){

                ShopZonesDeliveryPricesrate::where('shop_zones_delivery_id', $request->businessDeliveryZoneId)->delete();

                $shopId = ShopDeliveryZone::whereId($request->businessDeliveryZoneId)->first()->shop_id;

                $shopCurrencies = ShopCurrency::with('currency')->where('shop_id', $shopId)->get();

                foreach ($shopCurrencies as $currency){

                    $shopDeliveryZonePricesrate = new ShopZonesDeliveryPricesrate();
                    $shopDeliveryZonePricesrate->shop_zones_delivery_id = $request->businessDeliveryZoneId;
                    $shopDeliveryZonePricesrate->currency_id = $currency->currency->id;
                    if($currency->currency->id == $IdCurrencyUSD){
                        $shopDeliveryZonePricesrate->price = $request->businessDeliveryZonePrices;
                    }
                    else{
                        $shopDeliveryZonePricesrate->price = $request->businessDeliveryZonePrices * $currency->rate;
                    }

                    $shopDeliveryZonePricesrate->save();

                }

            }

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business delivery zone updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_Business_Delivery_Zones
    public function deleteBusinessDeliveryZone(Request $request){
        try {
            DB::beginTransaction();

            $result = ShopDeliveryZone::whereId($request->businessDeliveryZoneId)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Business delivery zone deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Business delivery zone not found'
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
