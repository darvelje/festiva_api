<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewBusinessRequest;
use App\Models\Locality;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Shop;
use App\Models\ShopDeliveryZone;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


//        return response()->json(
//            [
//                'code' => 'ok',
//                'message' => 'Test',
//                'request' => $request->all()
//            ]
//        );

class BusinessController extends Controller
{

    //section Get_Businesses
    public function getBusinesses(Request $request){

        $businesses = Shop::with('shopProducts','shopProducts.shopProductPhotos' )->get();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Businesses',
                'businesses' => $businesses
            ]
        );
    }

    //section Get_Businesses_by_Ubication
    public function getAllBusinesses(Request $request){
        $businesses = [];

        if($request->provinceId && $request->municipalityId !== null && $request->localityId !== null){

            $locality = Locality::whereId($request->localityId)->first();

            $municipality = Municipality::whereId($locality->municipalitie_id)->first();

            if ($locality) {

                $shopsArrayIds = ShopDeliveryZone::whereLocalitieId($locality->id)->orwhere('municipalitie_id',$locality->municipalitie_id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();

                $businesses = Shop::with('shopProducts','shopProducts.shopProductPhotos' )->whereIn('id', $shopsArrayIds)->get();

            } else {
                return response()->json(['code' => 'error', 'message' => 'Locality not found'], 404);
            }
        }
        else if($request->provinceId && $request->municipalityId !== null && $request->localityId === null){

            $municipality = Municipality::whereId($request->municipalityId)->first();

            if ($municipality) {
                $shopsArrayIds = ShopDeliveryZone::whereMunicipalitieId($municipality->id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();

                $businesses = Shop::with('shopProducts','shopProducts.shopProductPhotos' )->whereIn('id', $shopsArrayIds)->get();

            } else {
                return response()->json(['code' => 'error', 'message' => 'Municipality not found'], 404);
            }
        }
        else if($request->provinceId && $request->municipalityId === null && $request->localityId === null){

            $province = Province::whereId($request->provinceId)->first();

            if ($province) {
                $shopsArrayIds = ShopDeliveryZone::whereProvinceId($province->id)->pluck('shop_id')->unique();

                $businesses = Shop::with('shopProducts','shopProducts.shopProductPhotos' )->whereIn('id', $shopsArrayIds)->get();

            } else {
                return response()->json(['code' => 'error', 'message' => 'Province not found'], 404);
            }
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Businesses',
                'businesses' => $businesses
            ]
        );
    }

    //section Get_Business
    public function getBusinessBySlug(Request $request){

        $business = Shop::whereSlug($request->businessSlug)->first();

        if($business){
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business',
                    'business' => $business
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Business not found',
            ]
        );


    }

    //section change_Status_Delivery
    public function changeStatusDelivery(Request $request){
        try{
            DB::beginTransaction();

            $business = Shop::whereId($request->businessId)->first();

            $business->delivery = $request->businessDelivery;

            $business->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business delivery updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section change_Status_Pick
    public function changeStatusPick(Request $request){
        try{
            DB::beginTransaction();

            $business = Shop::whereId($request->businessId)->first();

            $business->pick = $request->businessPick;

            $business->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business pick updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section New_Business
    public function newBusiness(Request $request){

        try{
            DB::beginTransaction();

            $business = new Shop();

            if($request->userId){
                $business->user_id = $request->userId;
            }
            else{

                $user = new User();

                $user->name = $request->userName;
                $user->email = $request->userEmail;
                $user->password = Hash::make($request->userPassword);

                $user->save();

                $business->user_id = $user->id;
            }

            $business->name = $request->businessName;
            $business->slug = Str::slug($request->businessUrl);
            $business->description = $request->businessDescription;
            $business->address = $request->businessAddress;
            $business->phone = $request->businessPhone;
            $business->email = $request->businessEmail;
            $business->url = $request->businessUrl;
            $business->comission = $request->businessComission;
            $business->delivery = false;
            $business->pick = false;
            if ($request->hasFile('businessAvatar')) {
                $business->avatar = self::uploadImage($request->businessAvatar, $request->businessName);
            }
            if ($request->hasFile('businessCover')) {
                $business->cover = self::uploadImage($request->businessCover, $request->businessName);
            }

            $business->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Business
    public function updateBusiness(Request $request){

        try{
            DB::beginTransaction();

            $business = Shop::whereId($request->businessId)->first();

            $business->name = $request->businessName;
            $business->slug = Str::slug($request->businessUrl);
            $business->description = $request->businessDescription;
            $business->address = $request->businessAddress;
            $business->phone = $request->businessPhone;
            $business->email = $request->businessEmail;
            $business->url = $request->businessUrl;
            $business->comission = $request->businessComission;
            if ($request->hasFile('businessAvatar')) {
                $business->avatar = self::uploadImage($request->businessAvatar, $request->businessName);
            }
            if ($request->hasFile('businessCover')) {
                $business->cover = self::uploadImage($request->businessCover, $request->businessName);
            }

            $business->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Business updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_Business
    public function deleteBusiness(Request $request){
        try {
            DB::beginTransaction();

            $result = Shop::whereId($request->businessId)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Business deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Business not found'
                ]
            );

        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Upload_image
    public static function uploadImage($path, $name){
        $image = $path;

        $avatarName =  $name . substr(uniqid(rand(), true), 7, 7) . '.png';

        $img = Image::make($image->getRealPath())->encode('png', 50)->orientate();

        $img->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream(); // <-- Key point

        Storage::disk('public')->put('/businessImages' . '/' . $avatarName, $img, 'public');
        $path = '/businessImages/' . $avatarName;

        return $path;
    }
}
