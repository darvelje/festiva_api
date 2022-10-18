<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\ShopProductPhoto;
use App\Models\ShopProductsHasCategoriesProduct;
use App\Models\ShopProductsPricesrate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;


class ProductController extends Controller
{


    //section Get_Products
    public function getProducts(){

        $products = ShopProduct::with('shopProductPhotos', 'shop', 'categoriesProducts' )->get();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Products',
                'products' => $products
            ]
        );
    }

    //section Get_Product
    public function getProductBySlug(Request $request){

        $product = ShopProduct::with('shopProductPhotos', 'shop', 'categoriesProducts' )->whereSlug($request->productSlug)->first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Product',
                'product' => $product
            ]
        );
    }

    //section New_Product
    public function newProduct(Request $request){

        try{
            DB::beginTransaction();

            $product = new ShopProduct();

            $product->name = $request->productName;
            $product->stock = $request->productStock;
            $product->quantity_min = $request->productQuantityMin;
            $product->slug = Str::slug($request->productSlug);
            $product->shop_id = $request->productShopId;

            $product->save();

            $lengthArrayProductImage = count($request->productImage);

            if($lengthArrayProductImage != 0){
                for($i=0; $i<$lengthArrayProductImage; $i++){
                    $productPhoto = new ShopProductPhoto();
                    $productPhoto->shop_product_id = $product->id;
                    $productPhoto->main = $request->productImage[$i]['main'];
                    $productPhoto->path_photo = self::uploadImage($request->productImage[$i]['image'], $request->productName);

                    $productPhoto->save();
                }
            }

            $lengthArrayProductCategory = count($request->productCategory);

            if($lengthArrayProductCategory != 0){
                for($i=0; $i<$lengthArrayProductCategory; $i++){
                    $productCategory = new ShopProductsHasCategoriesProduct();
                    $productCategory->shop_product_id = $product->id;
                    $productCategory->category_product_id = $request->productCategory[$i];
                    $productCategory->save();

                }
            }

            $lengthArrayProductPrice= count($request->productPrice);

            for($i=0; $i<$lengthArrayProductPrice; $i++){
                $productPrice = new ShopProductsPricesrate();
                $productPrice->shop_product_id = $product->id;
                $productPrice->currency_code = $request->productPrice[$i]['currencyCode'];
                $productPrice->rate = $request->productPrice[$i]['value'];
                $productPrice->main = $request->productPrice[$i]['main'];
                $productPrice->save();
            }

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Product created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_Product
    public function updateProduct(Request $request){

        try{
            DB::beginTransaction();

            $product = ShopProduct::whereId($request->productId)->first();

            $product->name = $request->productName;
            $product->stock = $request->productStock;
            $product->quantity_min = $request->productQuantityMin;
            $product->slug = Str::slug($request->productSlug);

            $product->update();

            $lengthArrayProductImageDeleted = count($request->productImageDeleted);

            for($i=0; $i<$lengthArrayProductImageDeleted; $i++){
                ShopProductPhoto::whereId($request->productImageDeleted[$i])->delete();
            }

            $lengthArrayProductImage = count($request->productImage);

            if($lengthArrayProductImage != 0){
                for($i=0; $i<$lengthArrayProductImage; $i++){
                    $productPhoto = new ShopProductPhoto();
                    $productPhoto->shop_product_id = $product->id;
                    $productPhoto->main = $request->productImage[$i]['main'];
                    $productPhoto->path_photo = self::uploadImage($request->productImage[$i]['image'], $request->productName);
                    $productPhoto->save();
                }
            }

            $productMain = ShopProductPhoto::where('shop_product_id',$request->productId)->whereMain(true)->count();

            if($productMain == 0){
                $productPhotoMain = ShopProductPhoto::where('shop_product_id',$request->productId)->first();

                $productPhotoMain->main = true;
                $productPhotoMain->update();
            }

            $lengthArrayProductCategory = count($request->productCategory);

            ShopProductPhoto::whereId($request->productId)->delete();

            if($lengthArrayProductCategory != 0){
                for($i=0; $i<$lengthArrayProductCategory; $i++){
                    $productCategory = new ShopProductsHasCategoriesProduct();
                    $productCategory->shop_product_id = $request->productId;
                    $productCategory->category_product_id = $request->productCategory[$i];
                    $productCategory->save();
                }
            }

            $lengthArrayProductPrice= count($request->productPrice);



            for($i=0; $i<$lengthArrayProductPrice; $i++){
                $productPrice = ShopProductsPricesrate::where('shop_product_id',$request->productId);
                //$productPrice->shop_product_id = $request->productId;
                $productPrice->currency_code = $request->productPrice[$i]['currencyCode'];
                $productPrice->rate = $request->productPrice[$i]['value'];
                $productPrice->main = $request->productPrice[$i]['main'];
                $productPrice->update();
            }

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Product updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

//    // section Delete_Currency
//    public function deleteCurrency(Request $request){
//        try {
//            DB::beginTransaction();
//
//            $result = Currency::whereId($request->currencyId)->delete();
//
//            DB::commit();
//
//            if($result){
//                return response()->json(
//                    [
//                        'code' => 'ok',
//                        'message' => 'Currency deleted successfully'
//                    ]
//                );
//            }
//
//            return response()->json(
//                [
//                    'code' => 'error',
//                    'message' => 'Currency not found'
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

    //section Upload_image
    public static function uploadImage($path, $name){
        $image = $path;

        $avatarName =  $name . substr(uniqid(rand(), true), 7, 7) . '.png';

        $img = Image::make($image->getRealPath())->encode('png', 50)->orientate();

        $img->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream(); // <-- Key point

        Storage::disk('public')->put('/productsImages' . '/' . $avatarName, $img, 'public');
        $path = '/productsImages/' . $avatarName;

        return $path;
    }

}
