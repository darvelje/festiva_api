<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Locality;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\ShopDeliveryZone;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    //section Get_Provinces
    public function getProvinces(){

        $provinces = Province::all();

        if($provinces){

            unset($provinces->updated_at);
            unset($provinces->created_at);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Provinces',
                    'provinces' => $provinces
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Provinces',
                'provinces' => $provinces
            ]
        );

    }

    //section Get_Provinces what include shop delivery zones
    public function getProvincesWithShop(){

        $shopsArrayIds = ShopDeliveryZone::pluck('province_id')->unique();

        $provinces = Province::whereIn('id', $shopsArrayIds)->get();

        if($provinces){

            unset($provinces->updated_at);
            unset($provinces->created_at);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Provinces',
                    'provinces' => $provinces
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Provinces',
                'provinces' => $provinces
            ]
        );

    }

    //section Get_Municipalities
    public function getMunicipalities(Request $request){

        $municipalities = Municipality::where('province_id', $request->provinceId)->get();

        if(count($municipalities) !== 0){

            unset($municipalities->updated_at);
            unset($municipalities->created_at);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Municipalities',
                    'municipalities' => $municipalities
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Province not found'
            ]
        );

    }

    //section Get_Municipalities what include shop delivery zones
    public function getMunicipalitiesWithShop(Request $request){

        $shopsArrayIds = ShopDeliveryZone::pluck('municipalitie_id')->unique();

        $municipalities = Municipality::whereIn('id', $shopsArrayIds)->where('province_id', $request->provinceId)->get();

        if(count($municipalities) !== 0){

            unset($municipalities->updated_at);
            unset($municipalities->created_at);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Municipalities',
                    'municipalities' => $municipalities
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Province not found'
            ]
        );

    }

    //section Get_Localities
    public function getLocalities(Request $request){

        $localities = Locality::where('municipalitie_id', $request->municipalityId)->get();

        if(count($localities) !== 0){

            unset($localities->updated_at);
            unset($localities->created_at);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Localities',
                    'localities' => $localities
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Municipality not found'
            ]
        );

    }

    //section Get_Localities what include shop delivery zones
    public function getLocalitiesWithShop(Request $request){

        $shopsArrayIds = ShopDeliveryZone::pluck('localitie_id')->unique();

        $localities = Locality::whereIn('id', $shopsArrayIds)->where('municipalitie_id', $request->municipalityId)->get();

        if(count($localities) !== 0){

            unset($localities->updated_at);
            unset($localities->created_at);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Localities',
                    'localities' => $localities
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Municipality not found'
            ]
        );

    }
}
