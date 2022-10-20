<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewBusinessRequest;
use App\Models\Shop;
use App\Models\ShopDeliveryZone;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class BusinessDeliveryZonesController extends Controller
{

    //section Get_BusinessDeliveryZones
    public function getBusinessDeliveryZones(){

        $userAddresses = UserAddress::with('locality', 'locality.municipality', 'locality.municipality.province')->get();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Business delivery zones',
                'userAddresses' => $userAddresses
            ]
        );
    }

    //section Get_BusinessDeliveryZones
    public function getBusinessDeliveryZoneById(Request $request){

        $userAddress = UserAddress::with('locality', 'locality.municipality', 'locality.municipality.province')->whereId($request->userAddressId)->first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Business delivery zone',
                'userAddress' => $userAddress
            ]
        );
    }

    //section New_BusinessDeliveryZones
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
                'businessDeliveryZoneCurrencyCode' => 'required',
                'businessDeliveryZonePrice' => 'required',
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
            $shopDeliveryZone->currency_code = $request->businessDeliveryZoneCurrencyCode;
            $shopDeliveryZone->price= $request->businessDeliveryZonePrice;

            $shopDeliveryZone->save();

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

    //section Update_BusinessDeliveryZones
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
            $shopDeliveryZone->currency_code = $request->businessDeliveryZoneCurrencyCode;
            $shopDeliveryZone->price= $request->businessDeliveryZonePrice;

            $shopDeliveryZone->update();

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

    // section Delete_BusinessDeliveryZones
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
