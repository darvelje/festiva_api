<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\CategoriesProduct;
use App\Models\Shop;
use App\Models\ShopCoupon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class BusinessCouponsController extends Controller
{

    //section Get_Business_Coupons
    public function getShopCoupons(){

        $shopCoupons = ShopCoupon::with('shop')->get();

        foreach ($shopCoupons as $coupon){
            $coupon->shop_name = $coupon->shop->name;

            unset($coupon->shop);
            unset($coupon->created_at);
            unset($coupon->updated_at);
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Shops coupons',
                'shopCoupons' => $shopCoupons
            ]
        );
    }

    //section Get_Business_Coupon
    public function getShopCouponById(Request $request){

        $shopCoupon = ShopCoupon::with('shop')->whereId($request->shopCouponId)->first();

        if($shopCoupon){
            $shopCoupon->shop_name = $shopCoupon->shop->name;

            unset($shopCoupon->shop);
            unset($shopCoupon->created_at);
            unset($shopCoupon->updated_at);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Shop coupon',
                    'shopCoupon' => $shopCoupon
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Shop coupon not found'
                ]
            );
        }


    }

    //section Get_Business_Coupon_By_Shop_Slug
    public function getShopCouponByShopSlug(Request $request){

        $shop = Shop::with('shopCoupons')->whereSlug($request->businessUrl)->first();

        if($shop){
            $shopCoupons = $shop->shopCoupons;

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Shop coupons',
                    'shopCoupons' => $shopCoupons
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Business not found'
                ]
            );
        }


    }

    //section New_Business_Coupon
    public function newShopCoupon(Request $request){

        try{
            DB::beginTransaction();

            $shopCoupon = new ShopCoupon();

            $shopCoupon->name = $request->shopCouponName;
            $shopCoupon->code = $request->shopCouponCode;
            $shopCoupon->value = $request->shopCouponValue;
            $shopCoupon->status = $request->shopCouponStatus;
            $shopCoupon->type = $request->shopCouponType;
            $shopCoupon->shop_id = $request->shopCouponShopId;

            $shopCoupon->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Shop coupon created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Business_Coupon
    public function updateShopCoupon(Request $request){

        try{
            DB::beginTransaction();

            $shopCoupon = ShopCoupon::whereId($request->shopCouponId)->first();

            $shopCoupon->name = $request->shopCouponName;
            $shopCoupon->code = $request->shopCouponCode;
            $shopCoupon->value = $request->shopCouponValue;
            $shopCoupon->status = $request->shopCouponStatus;
            $shopCoupon->type = $request->shopCouponType;
            $shopCoupon->shop_id = $request->shopCouponShopId;

            $shopCoupon->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Shop coupon updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_Business_Coupon
    public function deleteShopCoupon(Request $request){
        try {
            DB::beginTransaction();

            $result = ShopCoupon::whereId($request->shopCouponId)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Shop coupon deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Shop coupon not found'
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
