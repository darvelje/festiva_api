<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\CategoriesProduct;
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

        $promos = CategoriesProduct::all();

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

        $category = CategoriesProduct::whereSlug($request->categorySlug)->first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Category',
                'category' => $category
            ]
        );
    }

//    //section New_Category
//    public function newCategory(NewCategoryRequest $request){
//
//        try{
//            DB::beginTransaction();
//
//            $category = new CategoriesProduct();
//
//            $category->name = $request->categoryName;
//            $category->slug = Str::slug($request->categorySlug);
//            if($request->categoryParentId){
//                $category->parent_id =$request->categoryParentId;
//            }
//
//            $category->save();
//
//            DB::commit();
//
//            return response()->json(
//                [
//                    'code' => 'ok',
//                    'message' => 'Category created successfully'
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

}
