<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewBusinessRequest;
use App\Models\Locality;
use App\Models\Municipality;
use App\Models\Order;
use App\Models\Province;
use App\Models\Shop;
use App\Models\ShopDeliveryZone;
use App\Models\ShopProduct;
use App\Models\User;
use Carbon\Carbon;
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
    public function getBusinesses(){

        $businesses = Shop::with('shopProducts','shopProducts.shopProductPhotos', 'orders' )->get();

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

    //section Get_Business_by_Slug
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

    //section Get_Business_By_Id
    public function getBusinessById(Request $request){

        $business = Shop::with('shopDeliveryZones', 'shopDeliveryZones.shopZonesDeliveryPricesrates', 'shopDeliveryZones.shopZonesDeliveryPricesrates.currency')->whereId($request->businessId)->first();

        if($business){

            foreach ($business->shopDeliveryZones as $deliver_price){
                $business->deliver_price = $deliver_price->shopZonesDeliveryPricesrates;

                foreach($business->deliver_price as $price){
                    
                    $price->currency_code = $price->currency->currency_code;

                    unset($price->id);
                    unset($price->shop_zones_delivery_id);
                    unset($price->currency_id);
                    unset($price->currency);
                    unset($price->created_at);
                    unset($price->updated_at);
                }

                unset($deliver_price->id);
                unset($deliver_price->shop_id);
                unset($deliver_price->localitie_id);
                unset($deliver_price->municipalitie_id);
                unset($deliver_price->province_id);
                unset($deliver_price->time);
                unset($deliver_price->time_type);
                unset($deliver_price->created_at);
                unset($deliver_price->updated_at);
                unset($deliver_price->shopZonesDeliveryPricesrates);

            }

            unset($business->shopDeliveryZones);
            unset($business->created_at);
            unset($business->updated_at);

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

    //section Get_Chart_Orders_Stats
    public function getChartOrdersStatsByBusinessSlug(Request $request){

        $shop = Shop::whereSlug($request->businessSlug)->first();

        if($shop){
            $days = collect();
            $ordersTotals = collect();
            $ordersCompleted = collect();
            $array_final = [];

            for ($i = $request->days; $i > 0; $i--) {
                //reverse
                $days->add(Carbon::now()->subDays($i)->format('d-m-Y'));
                $ordersTotals->add(Order::where('shop_id', $shop->id)->whereDate('created_at', '=', Carbon::now()->subDays($i))->count());
                $ordersCompleted->add(Order::where('shop_id', $shop->id)->where('status',6)->whereDate('created_at', '=', Carbon::now()->subDays($i))->count());
            }

            for($i=0; $i<count($ordersTotals); $i++){
                array_push($array_final, (object)[
                    'date' => $days[$i],
                    'orders_completed' => $ordersCompleted[$i],
                    'orders' => $ordersTotals[$i]]);
            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Chart orders data by days',
                    'orders' => $array_final
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

    //section Get_Chart_Products_Sold_By_Categories
    public function getChartProductsSoldByCategoriesByBusinessSlug(Request $request){

        $shop = Shop::whereSlug($request->businessSlug)->first();

        if($shop){
            $products =  ShopProduct::with('shopProductsHasCategoriesProducts.categoriesProduct')->where('shop_id', $shop->id)->get();

            $array_categories = [];
            $array_categories_id = [];
            $array_count = [];
            $array_final = [];

            foreach ($products as $product){
                foreach ($product->shopProductsHasCategoriesProducts as $category){
                    if($product->sales !== null){
                        $key = array_search($category->categoriesProduct->id, $array_categories_id);
                        if($key === false){
                            array_push($array_categories, $category->categoriesProduct->name);
                            array_push($array_count, $product->sales);
                            array_push($array_categories_id, $category->categoriesProduct->id);
                        }
                        else{
                            $array_count[$key] = $array_count[$key] + $product->sales;
                        }
                    }
                }
            }

            for($i=0; $i<count($array_categories); $i++){
                array_push($array_final, (object)[
                    'name' => $array_categories[$i],
                    'value' =>$array_count[$i]]);
            }


            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Chart sold data',
                    'sold_data' => $array_final
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
