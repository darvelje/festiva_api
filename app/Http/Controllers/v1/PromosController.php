<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewPromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Models\CategoriesProduct;
use App\Models\Promo;
use App\Models\PromosType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;


class PromosController extends Controller
{

    //section Get_Promos_Type
    public function getPromosType(){
        $promosType = PromosType::all();

        if($promosType){

            foreach ($promosType as $promo){
                unset($promo->created_at);
                unset($promo->updated_at);
            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Promos type',
                    'promosType' => $promosType
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Promos type',
                'promosType' => $promosType
            ]
        );


    }

    //section Get_Promos
    public function getPromos(){
        $promos = Promo::with('promosType', 'categoriesProduct')->get();

        if($promos){
            foreach ($promos as $promo){
                $promo->ubicacion = $promo->promosType->ubication;
                if($promo->categoriesProduct !== null){
                    $promo->categoryname = $promo->categoriesProduct->name;
                }
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->id_promo_type);
                unset($promo->promosType);
                unset($promo->categoriesProduct);
            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Promos',
                    'promos' => $promos
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Promos',
                'promos' => $promos
            ]
        );


    }

    //section Get_Promos
    public function getPromoById(Request $request){

        $promos = Promo::with('promosType', 'categoriesProduct')->whereId($request->promoId)->first();

        if($promos){
            $promos->ubicacion = $promos->promosType->ubication;
            if($promos->category_id !== null){
                $promos->category_name = $promos->categoriesProduct->name;
            }
            else{
                $promos->category_name = null;
            }
            $promos->promo_name = $promos->promosType->name;
            unset($promos->created_at);
            unset($promos->updated_at);
            unset($promos->promosType);
            unset($promos->categoriesProduct);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Promos',
                    'promos' => $promos
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Promo not found'
            ]
        );


    }

    //section Get_Promos_HomeMarket
    public function getPromosHome(Request $request){

        $promos = Promo::with('promosType')->whereCategoryId(null)->where('province_id', $request->provinceId)->get();

        if($promos){

            $promo1 = [];
            $promo2 = [];
            $promo3 = [];
            $promo4 = [];
            $promo5 = [];
            $promo6 = [];
            $promo7 = [];
            $promo8 = [];
            $promo9 = [];
            $arrayGlobal = [];

            foreach ($promos as $promo){
                if($promo->promosType->ubication === 'promo1'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo1, $promo);
                }
                if($promo->promosType->ubication === 'promo2'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo2, $promo);
                }
                if($promo->promosType->ubication === 'promo3'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo3, $promo);
                }
                if($promo->promosType->ubication === 'promo4'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo4, $promo);
                }
                if($promo->promosType->ubication === 'promo5'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo5, $promo);
                }
                if($promo->promosType->ubication === 'promo6'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo6, $promo);
                }
                if($promo->promosType->ubication === 'promo7'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo7, $promo);
                }
                if($promo->promosType->ubication === 'promo8'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo8, $promo);
                }
                if($promo->promosType->ubication === 'promo9'){
                    unset($promo->created_at);
                    unset($promo->updated_at);
                    unset($promo->category_id);
                    unset($promo->id_promo_type);
                    array_push($promo9, $promo);
                }

                unset($promo->promosType);

            }
            array_push($arrayGlobal, $promo1);
            array_push($arrayGlobal, $promo2);
            array_push($arrayGlobal, $promo3);
            array_push($arrayGlobal, $promo4);
            array_push($arrayGlobal, $promo5);
            array_push($arrayGlobal, $promo6);
            array_push($arrayGlobal, $promo7);
            array_push($arrayGlobal, $promo8);
            array_push($arrayGlobal, $promo9);

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Promos',
                    'promos' => $arrayGlobal
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Promos',
                'promos' => $promos
            ]
        );

    }

    //section Get_Promos_By_Category_Id
    public function getPromosByCategoryId(Request $request){

        $category = CategoriesProduct::whereId($request->categoryId)->first();

        if($category) {
            $promo = Promo::whereCategoryId($request->categoryId)->where('province_id', $request->provinceId)->get();
            foreach ($promo as $p){
                unset($p->created_at);
                unset($p->updated_at);
            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Promo',
                    'promo' => $promo
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Category no not found',
            ]
        );

    }

    //section New_Promo
    public function newPromo(NewPromoRequest $request){

        try{
            DB::beginTransaction();

            $promo = new Promo();

            if ($request->hasFile('promoPathImage')) {
                $promo->path_image = self::uploadImage($request->promoPathImage, 'promo');
            }

            $promo->status = $request->promoStatus;
            $promo->url = $request->promoURL;
            $promo->id_promo_type = $request->promoIdType;
            $promo->category_id = $request->promoCategoryId;
            $promo->province_id = $request->promoProvinceId;

            $promo->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Promo created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Promo
    public function updatePromo(UpdatePromoRequest $request){

        try{
            DB::beginTransaction();

            $promo = Promo::whereId($request->promoId)->first();

            if($promo){

                if ($request->hasFile('promoPathImage')) {
                    $promo->path_image = self::uploadImage($request->promoPathImage, 'promo');
                }

                $promo->status = $request->promoStatus;
                $promo->url = $request->promoURL;
                $promo->id_promo_type = $request->promoIdType;
                $promo->category_id = $request->promoCategoryId;
                $promo->province_id = $request->promoProvinceId;

                $promo->update();

                DB::commit();

                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Promo updated successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Promo not found'
                ]
            );




        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_Promo
    public function deletePromo(Request $request){
        try {
            DB::beginTransaction();

            $result = Promo::whereId($request->promoId)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Promo deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Promo not found'
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

        Storage::disk('public')->put('/promosImages' . '/' . $avatarName, $img, 'public');
        $path = '/promosImages/' . $avatarName;

        return $path;
    }

}
