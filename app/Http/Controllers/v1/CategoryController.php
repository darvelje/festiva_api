<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\CategoriesProduct;
use App\Models\Locality;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Shop;
use App\Models\ShopDeliveryZone;
use App\Models\ShopProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;


class CategoryController extends Controller
{

    //section Get_Categories
    public function getCategories(){

        $categories = CategoriesProduct::with(
            'shopProductsHasCategoriesProducts',
            'shopProductsHasCategoriesProducts.shopProduct')->get();

        if($categories){
            foreach($categories as $category){
                $category->products_count = $category->shopProductsHasCategoriesProducts->count();
                unset($category->shopProductsHasCategoriesProducts);
            }

            unset($category->created_at);
            unset($category->updated_at);
            unset($category->parent_id);
            }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Categories',
                'categories' => $categories
            ]
        );
    }

    //section Get_Categories_By_Ubication
    public function getAllCategories(Request $request){

        $categories = [];

        $categoriesId = [];

        if($request->provinceId && $request->municipalityId !== null && $request->localityId !== null){

            $locality = Locality::whereId($request->localityId)->first();

            $municipality = Municipality::whereId($locality->municipalitie_id)->first();

            if ($locality) {
                $shopsArrayIds = ShopDeliveryZone::whereLocalitieId($locality->id)->orwhere('municipalitie_id',$locality->municipalitie_id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();
                $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->get();

                foreach ($products as $prod) {
                    foreach ($prod->shopProductsHasCategoriesProducts as $cat) {
                        if (!in_array($cat->categoriesProduct->id, $categoriesId)) {
                            array_push($categoriesId, $cat->categoriesProduct->id);
                            array_push($categories, $cat->categoriesProduct);
                        }
                    }
                }

            } else {
                return response()->json(['code' => 'error', 'message' => 'Locality not found'], 404);
            }
        }
        else if($request->provinceId && $request->municipalityId !== null && $request->localityId === null){

            $municipality = Municipality::whereId($request->municipalityId)->first();

            if ($municipality) {
                $shopsArrayIds = ShopDeliveryZone::whereMunicipalitieId($municipality->id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();
                $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->get();

                foreach ($products as $prod) {
                    foreach ($prod->shopProductsHasCategoriesProducts as $cat) {
                        if (!in_array($cat->categoriesProduct->id, $categoriesId)) {
                            array_push($categoriesId, $cat->categoriesProduct->id);
                            array_push($categories, $cat->categoriesProduct);
                        }
                    }
                }

            } else {
                return response()->json(['code' => 'error', 'message' => 'Municipality not found'], 404);
            }
        }
        else if($request->provinceId && $request->municipalityId === null && $request->localityId === null){

            $province = Province::whereId($request->provinceId)->first();

            if ($province) {
                $shopsArrayIds = ShopDeliveryZone::whereProvinceId($province->id)->pluck('shop_id')->unique();
                $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->get();

                foreach ($products as $prod) {
                    foreach ($prod->shopProductsHasCategoriesProducts as $cat) {
                        if (!in_array($cat->categoriesProduct->id, $categoriesId)) {
                            array_push($categoriesId, $cat->categoriesProduct->id);
                            array_push($categories, $cat->categoriesProduct);
                        }
                    }
                }

            } else {
                return response()->json(['code' => 'error', 'message' => 'Province not found'], 404);
            }
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Success',
                'categories' => $categories
            ]
        );

    }

    //section Get_Categories_By_Ubication_Random
    public function getCategoriesByMunicipalityRandom(Request $request){

        $categories = [];

        $categoriesId = [];

        if($request->provinceId && $request->municipalityId !== null && $request->localityId !== null){

            $locality = Locality::whereId($request->localityId)->first();

            $municipality = Municipality::whereId($locality->municipalitie_id)->first();

            if ($locality) {
                $shopsArrayIds = ShopDeliveryZone::whereLocalitieId($locality->id)->orwhere('municipalitie_id',$locality->municipalitie_id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();
                $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->get();

                foreach ($products as $prod) {
                    foreach ($prod->shopProductsHasCategoriesProducts as $cat) {
                        if (!in_array($cat->categoriesProduct->id, $categoriesId)) {
                            array_push($categoriesId, $cat->categoriesProduct->id);
                            array_push($categories, $cat->categoriesProduct);
                        }
                    }
                }

            } else {
                return response()->json(['code' => 'error', 'message' => 'Locality not found'], 404);
            }
        }
        else if($request->provinceId && $request->municipalityId !== null && $request->localityId === null){

            $municipality = Municipality::whereId($request->municipalityId)->first();

            if ($municipality) {
                $shopsArrayIds = ShopDeliveryZone::whereMunicipalitieId($municipality->id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();
                $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->get();

                foreach ($products as $prod) {
                    foreach ($prod->shopProductsHasCategoriesProducts as $cat) {
                        if (!in_array($cat->categoriesProduct->id, $categoriesId)) {
                            array_push($categoriesId, $cat->categoriesProduct->id);
                            array_push($categories, $cat->categoriesProduct);
                        }
                    }
                }

            } else {
                return response()->json(['code' => 'error', 'message' => 'Municipality not found'], 404);
            }
        }
        else if($request->provinceId && $request->municipalityId === null && $request->localityId === null){

            $province = Province::whereId($request->provinceId)->first();

            if ($province) {
                $shopsArrayIds = ShopDeliveryZone::whereProvinceId($province->id)->pluck('shop_id')->unique();
                $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->get();

                foreach ($products as $prod) {
                    foreach ($prod->shopProductsHasCategoriesProducts as $cat) {
                        if (!in_array($cat->categoriesProduct->id, $categoriesId)) {
                            array_push($categoriesId, $cat->categoriesProduct->id);
                            array_push($categories, $cat->categoriesProduct);
                        }
                    }
                }

            } else {
                return response()->json(['code' => 'error', 'message' => 'Province not found'], 404);
            }
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Success',
                'categories' => count($categories) > 3
                    ? Arr::random($categories, 3)
                    : $categories
            ]
        );

    }

    //section Get_Category
    public function getCategoryBySlug(Request $request){

        $category = CategoriesProduct::whereSlug($request->categorySlug)->first();

        if($category){
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Category',
                    'category' => $category
                ]
            );

        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Category not found'
            ]
        );
    }

    //section New_Category
    public function newCategory(NewCategoryRequest $request){

        try{
            DB::beginTransaction();

            $category = new CategoriesProduct();

            $category->name = $request->categoryName;
            $category->slug = Str::slug($request->categorySlug);
            if($request->categoryParentId){
                $category->parent_id =$request->categoryParentId;
            }
            $category->icon = $request->categoryIcon;

            $category->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Category created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Category
    public function updateCategory(UpdateCategoryRequest $request){

        try{
            DB::beginTransaction();

            $category = CategoriesProduct::whereId($request->categoryId)->first();

            $category->name = $request->categoryName;
            $category->slug = Str::slug($request->categorySlug);
            if($request->categoryParentId){
                $category->parent_id =$request->categoryParentId;
            }
            $category->icon = $request->categoryIcon;

            $category->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Category updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_Category
    public function deleteCategory(Request $request){
        try {

 
            DB::beginTransaction();

            $result = CategoriesProduct::whereId($request->categoryId)->delete();


            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Category deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Category not found'
                ]
            );

        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Get_Random_Categories
    public function getRandomCategories(){

        $arrayCat = [];

        $categories = CategoriesProduct::with(
                'shopProductsHasCategoriesProducts',
                        'shopProductsHasCategoriesProducts.shopProduct')->get();

        if($categories){
            foreach($categories as $category){
                unset($category->created_at);
                unset($category->updated_at);
                unset($category->parent_id);

                if($category->shopProductsHasCategoriesProducts->count()>0){
                    array_push($arrayCat, $category);
                }

                unset($category->shopProductsHasCategoriesProducts);

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'RandomCategories',
                    'random_categories' => Arr::random($arrayCat, 3)
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'Not Categories'
            ]
        );

    }

}
