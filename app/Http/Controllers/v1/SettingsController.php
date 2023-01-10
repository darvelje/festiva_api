<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\CategoriesProduct;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SettingsPage;
use App\Models\ShopProduct;
use App\Models\ShopsAmounts;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Nette\Schema\ValidationException;


class SettingsController extends Controller
{

    //section Get_Settings
    public function getSettings(){

        $settings = Setting::first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Settings',
                'settings' => $settings
            ]
        );

    }

    //section Get_Chart_Orders_Stats
    public function getChartOrdersStats(Request $request){
        $days = collect();
        $ordersTotals = collect();
        $ordersCompleted = collect();
        $array_final =[];

        for ($i = $request->days; $i > 0; $i--) {
            //reverse
            $days->add(Carbon::now()->subDays($i)->format('d-m-Y'));
            $ordersTotals->add(Order::whereDate('created_at', '=', Carbon::now()->subDays($i))->count());
            $ordersCompleted->add(Order::where('status',6)->whereDate('created_at', '=', Carbon::now()->subDays($i))->count());
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

    //section Get_Chart_Products_Sold_By_Categories
    public function getChartProductsSoldByCategories(){

        $products =  ShopProduct::with('shopProductsHasCategoriesProducts.categoriesProduct')->get();
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

    //section Get_Settings
    public function getSettingsDelivery(){

        $settings = Setting::first();

        unset($settings->id);
        unset($settings->app_name);
        unset($settings->app_favicon);
        unset($settings->app_logo);
        unset($settings->shop_comission);
        unset($settings->phone);
        unset($settings->email);
        unset($settings->address);
        unset($settings->description);
        unset($settings->created_at);
        unset($settings->updated_at);

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Settings delivery',
                'settings' => $settings
            ]
        );

    }

    //section Get_Settings_Pages
    public function getSettingsPages(){

        $settingsPages = SettingsPage::first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Settings pages',
                'settings_pages' => $settingsPages
            ]
        );
    }

    //section Set_Settings
    public function setSettings(Request $request){

        try{

            DB::beginTransaction();

            $validateRequest = Validator::make($request->all(), [
                'settingAppName' => 'required|min:3|max:255|string',
                'settingComission' => 'required|min:3|max:255|numeric',
                'settingPhone' => 'required|min:8|max:10',
                'settingEmail' => 'required|min:3|max:255|email',
                'settingAddress' => 'required|min:3|max:255',
                'settingDescription' => 'required|min:3|max:255|string',
            ]);

            if($validateRequest->fails()){
                return response()->json(
                    [
                        'code' => 'error',
                        'errors' => $validateRequest->errors()
                    ]);
            }

            $setting = new Setting();

            $setting->app_name = $request->settingAppName;
            $setting->shop_comission= $request->settingComission;
            $setting->phone = $request->settingPhone;
            $setting->email = $request->settingEmail;
            $setting->address = $request->settingAddress;
            $setting->description = $request->settingDescription;

            if ($request->hasFile('settingFavIcon')) {
                $setting->app_favicon = self::uploadImage($request->settingFavIcon, $request->settingAppName);
            }
            if ($request->hasFile('settingLogo')) {
                $setting->app_logo = self::uploadImage($request->settingLogo, $request->settingAppName);
            }

            $setting->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => '$setting created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Settings
    public function updateSettings(Request $request){

        try{
            DB::beginTransaction();

            $validateRequest = Validator::make($request->all(), [
                'settingAppName' => 'required|min:3|max:255|string',
                'settingComission' => 'required|min:3|max:255|numeric',
                'settingPhone' => 'required|min:8|max:10',
                'settingEmail' => 'required|min:3|max:255|email',
                'settingAddress' => 'required|min:3|max:255',
                'settingDescription' => 'required|min:3|max:255|string',
            ]);

            if($validateRequest->fails()){
                return response()->json(
                    [
                        'code' => 'error',
                        'errors' => $validateRequest->errors()
                    ]);
            }

            $setting = Setting::first();

            $setting->app_name = $request->settingAppName;
            $setting->shop_comission= $request->settingComission;
            $setting->phone = $request->settingPhone;
            $setting->email = $request->settingEmail;
            $setting->address = $request->settingAddress;
            $setting->description = $request->settingDescription;

            if ($request->hasFile('settingFavIcon')) {
                $setting->app_favicon = self::uploadImage($request->settingFavIcon, $request->settingAppName);
            }
            if ($request->hasFile('settingLogo')) {
                $setting->app_logo = self::uploadImage($request->settingLogo, $request->settingAppName);
            }

            $setting->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Setting updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_Settings
    public function deleteSettings(){
        try {
            DB::beginTransaction();

            $result = Setting::first()->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Setting deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Setting not found'
                ]
            );

        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Province_Delivery_Setting
    public function updateProvinceDeliverySetting(Request $request){

        try{
            DB::beginTransaction();

            $setting = Setting::first();

            $setting->delivery_province = $request->deliveryProvince;

            $setting->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Delivery province in setting updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Municipality_Delivery_Settings
    public function updateMunicipalityDeliverySetting(Request $request){

        try{
            DB::beginTransaction();

            $setting = Setting::first();

            $setting->delivery_municipality = $request->deliveryMunicipality;

            $setting->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Delivery municipality in setting updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Locality_Delivery_Settings
    public function updateLocalityDeliverySetting(Request $request){

        try{
            DB::beginTransaction();

            $setting = Setting::first();

            $setting->delivery_locality = $request->deliveryLocality;

            $setting->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Delivery locality in setting updated successfully'
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

        $imageName =  $name . substr(uniqid(rand(), true), 7, 7) . '.png';

        $img = Image::make($image->getRealPath())->encode('png', 50)->orientate();

        $img->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream(); // <-- Key point

        Storage::disk('public')->put('/settingsImages' . '/' . $imageName, $img, 'public');
        $path = '/settingsImages/' . $imageName;

        return $path;
    }

    //section Platform_Economy
    public function getPlatformEconomy(Request $request){

        $userDb = $request->user();

        if($userDb){

//            $walletMoney = DB::table('shops_amounts')->groupBy(['currency_id'])->sum('amount');

            $walletMoney = ShopsAmounts::with('currency')->groupBy(['currency_id'])->get()->sum('amount');

            return response()->json([
                'code' => 'test',
                'message' => 'Debug function',
                'data' => $walletMoney
            ]);
        }
        else{
            return response()->json([
                'code' => 'error',
                'message' => 'You do not have permissions to view this information.',
            ]);
        }


    }
}
