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
    public function newCategory(NewCategoryRequest $request){

        try{
            DB::beginTransaction();

            $category = new CategoriesProduct();

            $category->name = $request->categoryName;
            $category->slug = Str::slug($request->categorySlug);
            if($request->categoryParentId){
                $category->parent_id =$request->categoryParentId;
            }

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
                        'shopProductsHasCategoriesProducts.shopProduct',
                        'shopProductsHasCategoriesProducts.shopProduct.shopProductPhotos',
                        'shopProductsHasCategoriesProducts.shopProduct.shopProductsPricesrates',
                        'shopProductsHasCategoriesProducts.shopProduct.shopProductsPricesrates.currency')->get();


        foreach($categories as $category){
            unset($category->created_at);
            unset($category->updated_at);
            unset($category->parent_id);

            $category->products = $category->shopProductsHasCategoriesProducts;

            foreach ($category->products as $product){
                unset($product->created_at);
                unset($product->updated_at);
                unset($product->category_product_id);
                unset($product->shop_product_id);
                $product->id = $product->shopProduct->id;
                $product->name = $product->shopProduct->name;
                $product->slug = $product->shopProduct->slug;
                $product->rating = $product->shopProduct->rating;
                foreach ($product->shopProduct->shopProductPhotos as $prod){
                    if($prod->main === true){
                        $product->photo = $prod->path_photo;

                    }
                }

                $product->prices = $product->shopProduct->shopProductsPricesrates;
                foreach ($product->prices as $prod_prices){
                    $prod_prices->currency_code = $prod_prices->currency->code;
                    unset($prod_prices->currency);
                    unset($prod_prices->created_at);
                    unset($prod_prices->updated_at);
                    unset($prod_prices->shop_product_id);
                    unset($prod_prices->currency_id);
                    unset($prod_prices->id);
                }
                unset($product->shopProduct);
            }

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

}
