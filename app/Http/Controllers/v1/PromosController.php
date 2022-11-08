<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewPromoRequest;
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

    //section Get_Promos
    public function getPromos(){

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

        $promos = PromosType::with('promos')->whereNot('ubication','category_promo')->get();

        foreach ($promos as $promo){
            if($promo->ubication === 'promo1'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo1, $promo);
            }
            if($promo->ubication === 'promo2'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo2, $promo);
            }
            if($promo->ubication === 'promo3'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo3, $promo);
            }
            if($promo->ubication === 'promo4'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo4, $promo);
            }
            if($promo->ubication === 'promo5'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo5, $promo);
            }
            if($promo->ubication === 'promo6'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo6, $promo);
            }
            if($promo->ubication === 'promo7'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo7, $promo);
            }
            if($promo->ubication === 'promo8'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo8, $promo);
            }
            if($promo->ubication === 'promo9'){
                unset($promo->created_at);
                unset($promo->updated_at);
                unset($promo->category_id);
                unset($promo->id);
                unset($promo->ubication);
                foreach ($promo->promos as $p){
                    $promo->path_image = $p->path_image;
                    $promo->url = $p->url;
                }
                unset($promo->promos);
                array_push($promo9, $promo);
            }
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
//        $promos->promo1 = $promo1;
//        $promos->promo2 = $promo2;
//        $promos->promo3 = $promo3;
//        $promos->promo4 = $promo4;
//        $promos->promo5 = $promo5;
//        $promos->promo6 = $promo6;
//        $promos->promo7 = $promo7;
//        $promos->promo8 = $promo8;
//        $promos->promo9 = $promo9;



        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Promos',
                'promos' => $arrayGlobal
            ]
        );
    }

    //section Get_Promos_By_Category_Id
    public function getPromosByCategoryId(Request $request){

        $promo = PromosType::with('promos')->whereCategoryId($request->categoryId)->first();

        if($promo){
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Promo',
                    'promo' => $promo
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Category no not found',
                ]
            );
        }
    }

    //section New_Promo
    public function newPromo(Request $request){

        try{
            DB::beginTransaction();

            $promoType = new PromosType();

            $promoType->ubication = $request->promoUbication;
            $promoType->category_id = $request->promoCategoryId;

            $promoType->save();

            $promo = new Promo();

            if ($request->hasFile('promoPathImage')) {
                $promo->path_image = self::uploadImage($request->promoPathImage, 'promo');
            }

            $promo->status = $request->promoStatus;
            $promo->url = $request->promoURL;
            $promo->id_promo_type = $promoType->id;


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
//
//    //section Update_Category
//    public function updateCategory(UpdateCategoryRequest $request){
//
//        try{
//            DB::beginTransaction();
//
//            $category = CategoriesProduct::whereId($request->categoryId)->first();
//
//            $category->name = $request->categoryName;
//            $category->slug = Str::slug($request->categorySlug);
//            if($request->categoryParentId){
//                $category->parent_id =$request->categoryParentId;
//            }
//
//            $category->update();
//
//            DB::commit();
//
//            return response()->json(
//                [
//                    'code' => 'ok',
//                    'message' => 'Category updated successfully'
//                ]
//            );
//        }
//        catch(\Throwable $th){
//            return response()->json(
//                ['code' => 'error', 'message' => $th->getMessage()]
//            );
//        }
//    }
//
//    // section Delete_Category
//    public function deleteCategory(Request $request){
//        try {
//            DB::beginTransaction();
//
//            $result = CategoriesProduct::whereId($request->categoryId)->delete();
//
//            DB::commit();
//
//            if($result){
//                return response()->json(
//                    [
//                        'code' => 'ok',
//                        'message' => 'Category deleted successfully'
//                    ]
//                );
//            }
//
//            return response()->json(
//                [
//                    'code' => 'error',
//                    'message' => 'Category not found'
//                ]
//            );
//
//        }
//        catch(\Throwable $th){
//            return response()->json(
//                ['code' => 'error', 'message' => $th->getMessage()]
//            );
//        }
//    }
//
//    //section Get_Random_Categories
//    public function getRandomCategories(){
//
//        $arrayCat = [];
//
//        $categories = CategoriesProduct::with(
//                'shopProductsHasCategoriesProducts',
//                        'shopProductsHasCategoriesProducts.shopProduct')->get();
//
//        foreach($categories as $category){
//            unset($category->created_at);
//            unset($category->updated_at);
//            unset($category->parent_id);
//
//            if($category->shopProductsHasCategoriesProducts->count()>0){
//                array_push($arrayCat, $category);
//            }
//
//            unset($category->shopProductsHasCategoriesProducts);
//
//        }
//
//        return response()->json(
//            [
//                'code' => 'ok',
//                'message' => 'RandomCategories',
//                'random_categories' => Arr::random($arrayCat, 3)
//            ]
//        );
//    }

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
