<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewBusinessRequest;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Model;
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

        //$userAddress = UserAddress::with('locality', 'locality.municipality', 'locality.municipality.province')->whereId($request->userAddressId)->first();
        $userAddress =  DB::table('view_useraddresses_id')->whereId($request->userAddressId)->first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'User address',
                'userAddress' => $userAddress
            ]
        );
    }

    //section Get_UserAddress_By_User
    public function getUserAddressByUserId(Request $request){

        $userAddress =  DB::table('view_useraddresses_userid')->whereUserId($request->userId)->get();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'User address',
                'userAddress' => $userAddress
            ]
        );
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
