<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;


class SettingsController extends Controller
{

    //section Get_Settings
    public function getSettings(){

        $settings = Setting::all();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Settings',
                'settings' => $settings
            ]
        );
    }

    //section Set_Settings
    public function setSettings(Request $request){

        try{
            DB::beginTransaction();

            $setting = new Setting();

            $setting->app_name = $request->settingAppName;
            $setting->shop_comission= $request->settingComission;
            $setting->telefono = $request->settingPhone;
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

            $setting = Setting::whereId($request->settingId)->first();

            $setting->app_name = $request->settingAppName;
            $setting->shop_comission= $request->settingComission;
            $setting->telefono = $request->settingPhone;
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
                    'message' => '$setting updated successfully'
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
    public function deleteSettings(Request $request){
        try {
            DB::beginTransaction();

            $result = Setting::whereId($request->settingId)->delete();

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

}
