<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\CategoriesProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class CategoryController extends Controller
{

    //section Get_Categories
    public function getCategories(){

        $categories = CategoriesProduct::all();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Categories',
                'categories' => $categories
            ]
        );
    }

    //section Get_Category
    public function getCategoryBySlug(Request $request){

        $category = CategoriesProduct::whereSlug($request->categorySlug)->first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Category',
                'category' => $category
            ]
        );
    }

    //section New_Category
    public function newCategory(Request $request){

        try{
            DB::beginTransaction();

            $category = new CategoriesProduct();

            $category->name = $request->categoryName;
            $category->slug = Str::slug($request->categorySlug);

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
    public function updateCategory(Request $request){

        try{
            DB::beginTransaction();

            $category = CategoriesProduct::whereId($request->categoryId)->first();

            $category->name = $request->categoryName;
            $category->slug = Str::slug($request->categorySlug);

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

}
