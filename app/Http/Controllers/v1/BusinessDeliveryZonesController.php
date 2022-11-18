<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Shop;
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

        $shop= Shop::with('shopDeliveryZones', 'shopDeliveryZones.locality', 'shopDeliveryZones.locality.municipality', 'shopDeliveryZones.locality.municipality.province')->whereSlug($request->businessUrl)->first();

        if($shop){

            $shopDeliveryZone = $shop->shopDeliveryZones;

            foreach ($shopDeliveryZone as $zone){

                $zone->localitie = $zone->locality->name;
                $zone->municipalitie = $zone->locality->municipality->name;
                $zone->province = $zone->locality->municipality->province->name;

                unset($zone->locality);
                unset($zone->created_at);
                unset($zone->updated_at);
            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business delivery zones',
                    'shopDeliveryZones' => $shopDeliveryZone
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

        $shopDeliveryZone = ShopDeliveryZone::with('locality', 'locality.municipality', 'locality.municipality.province')->whereId($request->businessDeliveryZoneId)->first();

       if($shopDeliveryZone){
           $shopDeliveryZone->localitie = $shopDeliveryZone->locality->name;
           $shopDeliveryZone->municipalitie = $shopDeliveryZone->locality->municipality->name;
           $shopDeliveryZone->province = $shopDeliveryZone->locality->municipality->province->name;

           unset($shopDeliveryZone->locality);
           unset($shopDeliveryZone->created_at);
           unset($shopDeliveryZone->updated_at);

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
                'businessDeliveryZoneLocalitieId' => 'required',
                'businessDeliveryZoneMunicipalitieId' => 'required',
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

            $lengthArrayDeliveryZonesPrices = count($request->businessDeliveryZonePrices);

            if($lengthArrayDeliveryZonesPrices != 0){
                for($i=0; $i<$lengthArrayDeliveryZonesPrices; $i++){
                    $shopDeliveryZonePricesrate = new ShopZonesDeliveryPricesrate();
                    $shopDeliveryZonePricesrate->shop_zones_delivery_id = $shopDeliveryZone->id;
                    $shopDeliveryZonePricesrate->currency_id = $request->businessDeliveryZonePrices[$i]['currencyId'];
                    $shopDeliveryZonePricesrate->price = $request->businessDeliveryZonePrices[$i]['price'];
                    $shopDeliveryZonePricesrate->save();
                }
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

            ShopZonesDeliveryPricesrate::where('shop_zones_delivery_id', $request->businessDeliveryZoneId)->delete();

            $lengthArrayDeliveryZonesPrices = count($request->businessDeliveryZonePrices);

            if($lengthArrayDeliveryZonesPrices != 0){
                for($i=0; $i<$lengthArrayDeliveryZonesPrices; $i++){
                    $shopDeliveryZonePricesrate = new ShopZonesDeliveryPricesrate();
                    $shopDeliveryZonePricesrate->shop_zones_delivery_id = $shopDeliveryZone->id;
                    $shopDeliveryZonePricesrate->currency_id = $request->businessDeliveryZonePrices[$i]['currencyId'];
                    $shopDeliveryZonePricesrate->price = $request->businessDeliveryZonePrices[$i]['price'];
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
