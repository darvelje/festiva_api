<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewBusinessRequest;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserFavoritesHasShopProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class UserAddressController extends Controller
{

    //section Get_UserAddresses
    public function getUserAddresses(){

        $userAddresses = UserAddress::with('locality', 'locality.municipality', 'locality.municipality.province')->get();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'User addresses',
                'userAddresses' => $userAddresses
            ]
        );
    }

    //section Get_UserAddress_By_Id
    public function getUserAddressById(Request $request){

        $userAddress =  DB::table('view_useraddresses_id')->whereId($request->userAddressId)->first();

        if($userAddress){
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'User address',
                    'userAddress' => $userAddress
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'User address not found'
            ]
        );
    }

    //section Get_UserAddress_By_User
    public function getUserAddressByUserId(Request $request){

        $userAddress =  DB::table('view_useraddresses_userid')->where('user_id', $request->userId)->get();

        if($userAddress){
            $resultUserAddresses = collect();

            $resultUserAddresses['user_id'] = $userAddress[0]->user_id;

            $resultUserAddresses['addresses'] = $userAddress->groupBy(['addres_id'])->flatten(1);

            $resultUserAddresses['addresses'] = $resultUserAddresses['addresses']->map(function ($item) {
                return collect($item)->forget(['user_id']);
            });

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'User address',
                    'userAddress' => $resultUserAddresses
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'There are no addresses for that user'
            ]
        );

    }

    //section Get_User_Favorites_Products
    public function getUserFavoritesProducts(Request $request){

        $userDb = $request->user();

        $userFavoritesProducts = UserFavoritesHasShopProduct::with('shopProduct',
            'shopProduct.shopProductsHasCategoriesProducts',
            'shopProduct.shopProductsHasCategoriesProducts.categoriesProduct',
            'shopProduct.shopProductsPricesrates',
            'shopProduct.shopProductsPricesrates.currency',
        )->where('user_id', $userDb->id)->get();

        if($userFavoritesProducts){
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Favorites products',
                    'favorites_products' => $userFavoritesProducts
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'User not found'
                ]
            );
        }

    }

    //section Add_User_Favorites_Products
    public function addUserFavoritesProducts(Request $request){

        try{
            DB::beginTransaction();

            $userDb = $request->user();

            $userFavoritesProducts = new UserFavoritesHasShopProduct();

            $userFavoritesProducts->user_id =  $userDb->id;
            $userFavoritesProducts->shop_product_id = $request->favoriteProductId;

            $userFavoritesProducts->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Product added favorite successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section New_UserAddress
    public function newUserAddress(Request $request){

        try{
            DB::beginTransaction();

            $userAddress = new UserAddress();

            $userAddress->user_id = $request->userId;
            $userAddress->localitie_id = $request->userLocalitieId;
            $userAddress->contact_name = $request->userContactName;
            $userAddress->contact_phone = $request->userContactPhone;
            $userAddress->zip_code = $request->userZipCode;

            $userAddress->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'User address created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_UserAddress
    public function updateUserAddress(Request $request){

        try{
            DB::beginTransaction();

            $userAddress = UserAddress::whereId($request->userAddressId)->first();

            $userAddress->user_id = $request->userId;
            $userAddress->localitie_id = $request->userLocalitieId;
            $userAddress->contact_name = $request->userContactName;
            $userAddress->contact_phone = $request->userContactPhone;
            $userAddress->zip_code = $request->userZipCode;

            $userAddress->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'User address updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_UserAddress
    public function deleteUserAddress(Request $request){
        try {
            DB::beginTransaction();

            $result = UserAddress::whereId($request->userAddressId)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'User address deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'User address not found'
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
