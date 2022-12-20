<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\CategoriesProduct;
use App\Models\Locality;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\ShopDeliveryZone;
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
use function GuzzleHttp\Promise\all;


class ProductController extends Controller
{

    //section Get_Products
    public function getProducts(){

        $products = ShopProduct::with('shopProductPhotos', 'shop', 'shopProductsHasCategoriesProducts.categoriesProduct', 'shopProductsPricesrates',  'shopProductsPricesrates.currency' )->get();

        if($products){
            foreach ($products as $product){

                $product->categories = $product->shopProductsHasCategoriesProducts;

                foreach ($product->categories as $prod_cat){
                    $prod_cat->id = $prod_cat->categoriesProduct->id;
                    $prod_cat->name = $prod_cat->categoriesProduct->name;
                    $prod_cat->parent_id = $prod_cat->categoriesProduct->parent_id;
                    $prod_cat->icon = $prod_cat->categoriesProduct->icon;
                    unset($prod_cat->categoriesProduct);
                    unset($prod_cat->shop_product_id);
                    unset($prod_cat->category_product_id);
                    unset($prod_cat->created_at);
                    unset($prod_cat->updated_at);
                }

                $product->photos = $product->shopProductPhotos;

                foreach ($product->photos as $prod_photo){
                    unset($prod_photo->created_at);
                    unset($prod_photo->updated_at);
                }

                $product->prices = $product->shopProductsPricesrates;

                foreach ($product->prices as $prod_prices){
                    $prod_prices->currency_code = $prod_prices->currency->code;
                    unset($prod_prices->currency);
                    unset($prod_prices->created_at);
                    unset($prod_prices->updated_at);
                }

                unset($product->shopProductPhotos);
                unset($product->shopProductsHasCategoriesProducts);
                unset($product->shopProductsPricesrates);
                unset($product->created_at);
                unset($product->updated_at);
                unset($product->shop_id);

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Products',
                    'products' => $products
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Products',
                'products' => $products
            ]
        );

    }

    //section Get_Products_By_Ubication
    public function getAllProducts(Request $request){

        if($request->provinceId && $request->municipalityId !== null && $request->localityId !== null){

            $locality = Locality::whereId($request->localityId)->first();

            $municipality = Municipality::whereId($locality->municipalitie_id)->first();

            if ($locality) {

                $shopsArrayIds = ShopDeliveryZone::whereLocalitieId($locality->id)->orwhere('municipalitie_id',$locality->municipalitie_id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();

                $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->get();

                foreach ($products as $product){

                    $product->categories = $product->shopProductsHasCategoriesProducts;

                    foreach ($product->categories as $prod_cat){
                        $prod_cat->id = $prod_cat->categoriesProduct->id;
                        $prod_cat->name = $prod_cat->categoriesProduct->name;
                        $prod_cat->parent_id = $prod_cat->categoriesProduct->parent_id;
                        $prod_cat->icon = $prod_cat->categoriesProduct->icon;
                        unset($prod_cat->categoriesProduct);
                        unset($prod_cat->shop_product_id);
                        unset($prod_cat->category_product_id);
                        unset($prod_cat->created_at);
                        unset($prod_cat->updated_at);
                    }

                    $product->photos = $product->shopProductPhotos;

                    foreach ($product->photos as $prod_photo){
                        unset($prod_photo->created_at);
                        unset($prod_photo->updated_at);
                    }

                    $product->prices = $product->shopProductsPricesrates;

                    foreach ($product->prices as $prod_prices){
                        $prod_prices->currency_code = $prod_prices->currency->code;
                        unset($prod_prices->currency);
                        unset($prod_prices->created_at);
                        unset($prod_prices->updated_at);
                    }

                    unset($product->shopProductPhotos);
                    unset($product->shopProductsHasCategoriesProducts);
                    unset($product->shopProductsPricesrates);
                    unset($product->created_at);
                    unset($product->updated_at);

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

                foreach ($products as $product){

                    $product->categories = $product->shopProductsHasCategoriesProducts;

                    foreach ($product->categories as $prod_cat){
                        $prod_cat->id = $prod_cat->categoriesProduct->id;
                        $prod_cat->name = $prod_cat->categoriesProduct->name;
                        $prod_cat->parent_id = $prod_cat->categoriesProduct->parent_id;
                        $prod_cat->icon = $prod_cat->categoriesProduct->icon;
                        unset($prod_cat->categoriesProduct);
                        unset($prod_cat->shop_product_id);
                        unset($prod_cat->category_product_id);
                        unset($prod_cat->created_at);
                        unset($prod_cat->updated_at);
                    }

                    $product->photos = $product->shopProductPhotos;

                    foreach ($product->photos as $prod_photo){
                        unset($prod_photo->created_at);
                        unset($prod_photo->updated_at);
                    }

                    $product->prices = $product->shopProductsPricesrates;

                    foreach ($product->prices as $prod_prices){
                        $prod_prices->currency_code = $prod_prices->currency->code;
                        unset($prod_prices->currency);
                        unset($prod_prices->created_at);
                        unset($prod_prices->updated_at);
                    }

                    unset($product->shopProductPhotos);
                    unset($product->shopProductsHasCategoriesProducts);
                    unset($product->shopProductsPricesrates);
                    unset($product->created_at);
                    unset($product->updated_at);

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

                foreach ($products as $product){

                    $product->categories = $product->shopProductsHasCategoriesProducts;

                    foreach ($product->categories as $prod_cat){
                        $prod_cat->id = $prod_cat->categoriesProduct->id;
                        $prod_cat->name = $prod_cat->categoriesProduct->name;
                        $prod_cat->parent_id = $prod_cat->categoriesProduct->parent_id;
                        $prod_cat->icon = $prod_cat->categoriesProduct->icon;
                        unset($prod_cat->categoriesProduct);
                        unset($prod_cat->shop_product_id);
                        unset($prod_cat->category_product_id);
                        unset($prod_cat->created_at);
                        unset($prod_cat->updated_at);
                    }

                    $product->photos = $product->shopProductPhotos;

                    foreach ($product->photos as $prod_photo){
                        unset($prod_photo->created_at);
                        unset($prod_photo->updated_at);
                    }

                    $product->prices = $product->shopProductsPricesrates;

                    foreach ($product->prices as $prod_prices){
                        $prod_prices->currency_code = $prod_prices->currency->code;
                        unset($prod_prices->currency);
                        unset($prod_prices->created_at);
                        unset($prod_prices->updated_at);
                    }

                    unset($product->shopProductPhotos);
                    unset($product->shopProductsHasCategoriesProducts);
                    unset($product->shopProductsPricesrates);
                    unset($product->created_at);
                    unset($product->updated_at);

                }

            } else {
                return response()->json(['code' => 'error', 'message' => 'Province not found'], 404);
            }

        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Products',
                'products' => $products
            ]
        );

    }

    //section Get_Products_Most_Seller
    public function getProductsMostSeller(){

        $products = ShopProduct::with('shopProductPhotos', 'shop', 'shopProductsHasCategoriesProducts.categoriesProduct', 'shopProductsPricesrates',  'shopProductsPricesrates.currency' )->orderByDesc('sales')->get();

        if($products){
            foreach ($products as $product){
                if($product->shopProductsHasCategoriesProducts->count()>0){
                    $product->category_name = $product->shopProductsHasCategoriesProducts->first()->categoriesProduct->name;
                }

                $product->photos = $product->shopProductPhotos;

                foreach ($product->photos as $prod_photo){
                    unset($prod_photo->created_at);
                    unset($prod_photo->updated_at);
                }

                $product->prices = $product->shopProductsPricesrates;

                foreach ($product->prices as $prod_prices){
                    $prod_prices->currency_code = $prod_prices->currency->code;
                    unset($prod_prices->currency);
                    unset($prod_prices->created_at);
                    unset($prod_prices->updated_at);
                }

                unset($product->shopProductPhotos);
                unset($product->shopProductsHasCategoriesProducts);
                unset($product->shopProductsPricesrates);
                unset($product->created_at);
                unset($product->updated_at);
                unset($product->shop_id);

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Products most seller',
                    'products' => $products
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Products',
                'products' => $products
            ]
        );

    }

    //section Get_Product_By_Slug
    public function getProductBySlug(Request $request){

        $product = ShopProduct::with('shopProductPhotos', 'shop', 'shopProductsHasCategoriesProducts.categoriesProduct', 'shopProductsPricesrates',  'shopProductsPricesrates.currency')->whereSlug($request->productSlug)->first();

        if($product){
            if($product->shopProductsHasCategoriesProducts->count()>0){
                $product->category_id = $product->shopProductsHasCategoriesProducts->first()->categoriesProduct->id;
                $product->category_name = $product->shopProductsHasCategoriesProducts->first()->categoriesProduct->name;
                $product->category_slug = $product->shopProductsHasCategoriesProducts->first()->categoriesProduct->slug;
            }

            $product->photos = $product->shopProductPhotos;

            foreach ($product->photos as $prod_photo){
                unset($prod_photo->created_at);
                unset($prod_photo->updated_at);
            }

            $product->shop_name = $product->shop->name;

            $product->prices = $product->shopProductsPricesrates;

            foreach ($product->prices as $prod_prices){
                $prod_prices->currency_code = $prod_prices->currency->code;
                unset($prod_prices->currency);
                unset($prod_prices->created_at);
                unset($prod_prices->updated_at);
            }

            unset($product->shopProductPhotos);
            unset($product->shopProductsHasCategoriesProducts);
            unset($product->shopProductsPricesrates);
            unset($product->created_at);
            unset($product->updated_at);


            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Product',
                    'product' => $product
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Product not found'
                ]
            );
        }

    }

    //section Get_Product_By_Shop_Slug
    public function getProductByBusinessSlug(Request $request){

        $shop = Shop::with('shopProducts.shopProductPhotos','shopProducts', 'shopProducts.shop','shopProducts.shopProductsHasCategoriesProducts.categoriesProduct', 'shopProducts.shopProductsPricesrates',  'shopProducts.shopProductsPricesrates.currency')->whereSlug($request->businessUrl)->first();

        if($shop){
            $products =$shop->shopProducts;

            foreach ($products as $product){

                $product->categories = $product->shopProductsHasCategoriesProducts;

                foreach ( $product->categories as $prod_cat){
                    $prod_cat->category_id = $prod_cat->categoriesProduct->id;
                    $prod_cat->category_name = $prod_cat->categoriesProduct->name;
                    $prod_cat->category_slug = $prod_cat->categoriesProduct->slug;
                    unset($prod_cat->id);
                    unset($prod_cat->category_product_id);
                    unset($prod_cat->shop_product_id);
                    unset($prod_cat->created_at);
                    unset($prod_cat->updated_at);
                    unset($prod_cat->categoriesProduct);
                }

                $product->photos = $product->shopProductPhotos;

                foreach ($product->photos as $prod_photo){
                    unset($prod_photo->created_at);
                    unset($prod_photo->updated_at);
                }

                $product->prices = $product->shopProductsPricesrates;

                foreach ($product->prices as $prod_prices){
                    $prod_prices->currency_code =  $prod_prices->currency->code;
                    unset($prod_prices->id);
                    unset($prod_prices->shop_product_id);
                    unset($prod_prices->created_at);
                    unset($prod_prices->updated_at);
                    unset($prod_prices->currency);
                }

                unset($product->shopProductPhotos);
                unset($product->shopProductsHasCategoriesProducts);
                unset($product->shopProductsPricesrates);
                unset($product->created_at);
                unset($product->updated_at);

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Products',
                    'products' => $products
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

    //section Get_Product_By_category_Slug
    public function getProductByCategorySlug(Request $request){

        $category = CategoriesProduct::with(
            'shopProductsHasCategoriesProducts',
            'shopProductsHasCategoriesProducts.shopProduct',
            'shopProductsHasCategoriesProducts.shopProduct.shopProductPhotos',
            'shopProductsHasCategoriesProducts.shopProduct.shopProductsPricesrates',
            'shopProductsHasCategoriesProducts.shopProduct.shopProductsPricesrates.currency')
        ->whereSlug($request->categorySlug)->first();

        if($category){
            unset($category->created_at);
            unset($category->updated_at);

            $category->products =  $category->shopProductsHasCategoriesProducts;
            unset($category->shopProductsHasCategoriesProducts);

            foreach ($category->products as $product){
                $product->product_id = $product->shopProduct->id;
                $product->product_name = $product->shopProduct->name;
                $product->product_slug = $product->shopProduct->slug;
                $product->product_rating = $product->shopProduct->rating;
                $product->product_discount = $product->shopProduct->discount_value ;
                $product->product_stock = $product->shopProduct->stock;
                $product->product_quantity_min = $product->shopProduct->quantity_min;
                $product->product_status = $product->shopProduct->status;
                $product->product_photo = $product->shopProduct->shopProductPhotos[0]->path_photo;

                $product->product_price = $product->shopProduct->shopProductsPricesrates;

                foreach ($product->product_price as $price){
                    $price->product_price = $price->price;
                    $price->product_currency_id = $price->currency_id;
                    $price->product_currency_code = $price->currency->code;

                    unset($price->id);
                    unset($price->shop_product_id);
                    unset($price->price);
                    unset($price->created_at);
                    unset($price->updated_at);
                    unset($price->currency);
                }

                unset($product->shopProduct->shopProductsPricesrates);
                unset($product->shopProduct);
                unset($product->category_product_id);
                unset($product->shop_product_id);
                unset($product->created_at);
                unset($product->updated_at);
                unset($product->id);

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Products',
                    'category' => $category
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Category not found'
                ]
            );
        }

    }

    //section Get_Product_By_category_Slug_By_Ubication
    public function getAllProductsByCategorySlug(Request $request){

        $category = CategoriesProduct::whereSlug($request->categorySlug)->first();

        if($category){

            $array_products=[];

            if($request->provinceId && $request->municipalityId !== null && $request->localityId !== null){

                $locality = Locality::whereId($request->localityId)->first();

                $municipality = Municipality::whereId($locality->municipalitie_id)->first();



                if ($locality) {

                    $shopsArrayIds = ShopDeliveryZone::whereLocalitieId($locality->id)->orwhere('municipalitie_id',$locality->municipalitie_id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();

                    $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct', 'shop')->whereIn('shop_id', $shopsArrayIds)->get();

                    foreach ($products as $product){

                        $product->categories = $product->shopProductsHasCategoriesProducts;

                        foreach ($product->categories as $prod_cat){
                            if($prod_cat->categoriesProduct->slug === $request->categorySlug){
                                array_push($array_products, $product);
                            }
                        }

                        $product->photos = $product->shopProductPhotos;

                        foreach ($product->photos as $prod_photo){
                            unset($prod_photo->created_at);
                            unset($prod_photo->updated_at);
                        }

                        $product->prices = $product->shopProductsPricesrates;

                        foreach ($product->prices as $prod_prices){
                            $prod_prices->currency_code = $prod_prices->currency->code;
                            unset($prod_prices->currency);
                            unset($prod_prices->created_at);
                            unset($prod_prices->updated_at);
                        }

                        unset($product->shopProductPhotos);
                        unset($product->shopProductsHasCategoriesProducts);
                        unset($product->shopProductsPricesrates);
                        unset($product->created_at);
                        unset($product->updated_at);
                        unset($product->categories);

                    }

                } else {
                    return response()->json(['code' => 'error', 'message' => 'Locality not found'], 404);
                }

            }
            else if($request->provinceId && $request->municipalityId !== null && $request->localityId === null){

                $municipality = Municipality::whereId($request->municipalityId)->first();

                if ($municipality) {
                    $shopsArrayIds = ShopDeliveryZone::whereMunicipalitieId($municipality->id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();
                    $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct', 'shop')->whereIn('shop_id', $shopsArrayIds)->get();

                    foreach ($products as $product){

                        $product->categories = $product->shopProductsHasCategoriesProducts;

                        foreach ($product->categories as $prod_cat){
                            if($prod_cat->categoriesProduct->slug === $request->categorySlug){
                                array_push($array_products, $product);
                            }
                        }

                        $product->photos = $product->shopProductPhotos;

                        foreach ($product->photos as $prod_photo){
                            unset($prod_photo->created_at);
                            unset($prod_photo->updated_at);
                        }

                        $product->prices = $product->shopProductsPricesrates;

                        foreach ($product->prices as $prod_prices){
                            $prod_prices->currency_code = $prod_prices->currency->code;
                            unset($prod_prices->currency);
                            unset($prod_prices->created_at);
                            unset($prod_prices->updated_at);
                        }

                        unset($product->shopProductPhotos);
                        unset($product->shopProductsHasCategoriesProducts);
                        unset($product->shopProductsPricesrates);
                        unset($product->created_at);
                        unset($product->updated_at);
                        unset($product->categories);

                    }

                } else {
                    return response()->json(['code' => 'error', 'message' => 'Municipality not found'], 404);
                }

            }
            else if($request->provinceId && $request->municipalityId === null && $request->localityId === null){

                $province = Province::whereId($request->provinceId)->first();

                if ($province) {
                    $shopsArrayIds = ShopDeliveryZone::whereProvinceId($province->id)->pluck('shop_id')->unique();
                    $products = ShopProduct::with('shop','shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->get();

                    foreach ($products as $product){

                        $product->categories = $product->shopProductsHasCategoriesProducts;

                        foreach ($product->categories as $prod_cat){
                            if($prod_cat->categoriesProduct->slug === $request->categorySlug){
                                array_push($array_products, $product);
                            }
                        }

                        $product->photos = $product->shopProductPhotos;

                        foreach ($product->photos as $prod_photo){
                            unset($prod_photo->created_at);
                            unset($prod_photo->updated_at);
                        }

                        $product->prices = $product->shopProductsPricesrates;

                        foreach ($product->prices as $prod_prices){
                            $prod_prices->currency_code = $prod_prices->currency->code;
                            unset($prod_prices->currency);
                            unset($prod_prices->created_at);
                            unset($prod_prices->updated_at);
                        }

                        unset($product->shopProductPhotos);
                        unset($product->shopProductsHasCategoriesProducts);
                        unset($product->shopProductsPricesrates);
                        unset($product->created_at);
                        unset($product->updated_at);
                        unset($product->categories);

                    }

                } else {
                    return response()->json(['code' => 'error', 'message' => 'Province not found'], 404);
                }

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Products',
                    'category' => $category->name,
                    'products' => $array_products
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Category not found'
                ]
            );
        }


    }

    //section Get_Product_Most_Saller_By_category_Slug
    public function getProductMostSellerByCategorySlug(Request $request){

        $category = CategoriesProduct::with(
            'shopProducts',
            'shopProducts.shopProductPhotos',
            'shopProducts.shopProductsPricesrates',
            'shopProducts.shopProductsPricesrates.currency')
        ->whereSlug($request->categorySlug)->first();

        if($category){
            unset($category->created_at);
            unset($category->updated_at);

            $category->products =  $category->shopProducts;

            foreach ($category->products as $product){

                $product->product_photo = $product->shopProductPhotos[0]->path_photo;

                $product->product_price = $product->shopProductsPricesrates;

                foreach ($product->product_price as $price){
                    $price->product_price = $price->price;
                    $price->product_currency_id = $price->currency_id;
                    $price->product_currency_code = $price->currency->code;

                    unset($price->id);
                    unset($price->shop_product_id);
                    unset($price->price);
                    unset($price->created_at);
                    unset($price->updated_at);
                    unset($price->currency);
                }

                unset($product->shopProductsPricesrates);
                unset($product->shopProductPhotos);

                unset($product->category_product_id);
                unset($product->laravel_through_key);
                unset($product->shop_product_id);
                unset($product->created_at);
                unset($product->updated_at);


            }

            unset($category->shopProducts);


            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Products',
                    'category' => $category
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Category not found'
                ]
            );
        }

    }

    //section Get_Product_Most_Saller_By_category_Slug_By_Ubication
    public function getAllProductMostSellerByCategorySlug(Request $request){

        $category = CategoriesProduct::whereSlug($request->categorySlug)->first();

        if($category){

            $array_products=[];

            if($request->provinceId && $request->municipalityId !== null && $request->localityId !== null){

                $locality = Locality::whereId($request->localityId)->first();

                $municipality = Municipality::whereId($locality->municipalitie_id)->first();



                if ($locality) {

                    $shopsArrayIds = ShopDeliveryZone::whereLocalitieId($locality->id)->orwhere('municipalitie_id',$locality->municipalitie_id)->orWhere('province_id', $municipality->province_id)->pluck('shop_id')->unique();

                    $products = ShopProduct::with('shopProductsHasCategoriesProducts', 'shopProductsHasCategoriesProducts.categoriesProduct')->whereIn('shop_id', $shopsArrayIds)->orderByDesc('sales')->get();

                    foreach ($products as $product){

                        $product->categories = $product->shopProductsHasCategoriesProducts;

                        foreach ($product->categories as $prod_cat){
                            if($prod_cat->categoriesProduct->slug === $request->categorySlug){
                                array_push($array_products, $product);
                            }
                        }

                        $product->photos = $product->shopProductPhotos;

                        foreach ($product->photos as $prod_photo){
                            unset($prod_photo->created_at);
                            unset($prod_photo->updated_at);
                        }

                        $product->prices = $product->shopProductsPricesrates;

                        foreach ($product->prices as $prod_prices){
                            $prod_prices->currency_code = $prod_prices->currency->code;
                            unset($prod_prices->currency);
                            unset($prod_prices->created_at);
                            unset($prod_prices->updated_at);
                        }

                        unset($product->shopProductPhotos);
                        unset($product->shopProductsHasCategoriesProducts);
                        unset($product->shopProductsPricesrates);
                        unset($product->created_at);
                        unset($product->updated_at);
                        unset($product->shop_id);
                        unset($product->categories);

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

                    foreach ($products as $product){

                        $product->categories = $product->shopProductsHasCategoriesProducts;

                        foreach ($product->categories as $prod_cat){
                            if($prod_cat->categoriesProduct->slug === $request->categorySlug){
                                array_push($array_products, $product);
                            }
                        }

                        $product->photos = $product->shopProductPhotos;

                        foreach ($product->photos as $prod_photo){
                            unset($prod_photo->created_at);
                            unset($prod_photo->updated_at);
                        }

                        $product->prices = $product->shopProductsPricesrates;

                        foreach ($product->prices as $prod_prices){
                            $prod_prices->currency_code = $prod_prices->currency->code;
                            unset($prod_prices->currency);
                            unset($prod_prices->created_at);
                            unset($prod_prices->updated_at);
                        }

                        unset($product->shopProductPhotos);
                        unset($product->shopProductsHasCategoriesProducts);
                        unset($product->shopProductsPricesrates);
                        unset($product->created_at);
                        unset($product->updated_at);
                        unset($product->shop_id);
                        unset($product->categories);

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

                    foreach ($products as $product){

                        $product->categories = $product->shopProductsHasCategoriesProducts;

                        foreach ($product->categories as $prod_cat){
                            if($prod_cat->categoriesProduct->slug === $request->categorySlug){
                                array_push($array_products, $product);
                            }
                        }

                        $product->photos = $product->shopProductPhotos;

                        foreach ($product->photos as $prod_photo){
                            unset($prod_photo->created_at);
                            unset($prod_photo->updated_at);
                        }

                        $product->prices = $product->shopProductsPricesrates;

                        foreach ($product->prices as $prod_prices){
                            $prod_prices->currency_code = $prod_prices->currency->code;
                            unset($prod_prices->currency);
                            unset($prod_prices->created_at);
                            unset($prod_prices->updated_at);
                        }

                        unset($product->shopProductPhotos);
                        unset($product->shopProductsHasCategoriesProducts);
                        unset($product->shopProductsPricesrates);
                        unset($product->created_at);
                        unset($product->updated_at);
                        unset($product->shop_id);
                        unset($product->categories);

                    }

                } else {
                    return response()->json(['code' => 'error', 'message' => 'Province not found'], 404);
                }

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Products',
                    'category' => $category->name,
                    'products' => $array_products
                ]
            );
        }
        else{
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Category not found'
                ]
            );
        }

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
            $product->description = $request->productDescription;
            $product->status = $request->productStatus;
            $product->discount_status = $request->productDiscountStatus;
            $product->discount_value = $request->productDiscountValue;
            $product->summary = $request->productSummary;
            $product->rating = 1;

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

            $shopCurrencies = ShopCurrency::with('currency')->where('shop_id', $request->productShopId)->get();

            foreach ($shopCurrencies as $currency){

                $productPrice = new ShopProductsPricesrate();
                $productPrice->shop_product_id = $product->id;
                $productPrice->currency_id = $currency->currency->id;

                if($currency->currency->code === 'USD'){
                    $productPrice->price = $request->productPrice;
                }
                else{
                    $productPrice->price = $request->productPrice * $currency->rate;
                }

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
            $product->description = $request->productDescription;
            $product->slug = Str::slug($request->productSlug);
            $product->status = $request->productStatus;
            $product->discount_status = $request->productDiscountStatus;
            $product->discount_value = $request->productDiscountValue;
            $product->summary = $request->productSummary;

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

            ShopProductsHasCategoriesProduct::where('shop_product_id',$request->productId)->delete();

            if($lengthArrayProductCategory != 0){
                for($i=0; $i<$lengthArrayProductCategory; $i++){
                    $productCategory = new ShopProductsHasCategoriesProduct();
                    $productCategory->shop_product_id = $request->productId;
                    $productCategory->category_product_id = $request->productCategory[$i];
                    $productCategory->save();
                }
            }

            $lengthArrayProductPrice= count($request->productPrice);

            ShopProductsPricesrate::where('shop_product_id',$request->productId)->delete();

            for($i=0; $i<$lengthArrayProductPrice; $i++){
                $productPrice = new ShopProductsPricesrate();
                $productPrice->shop_product_id = $request->productId;
                $productPrice->currency_id = $request->productPrice[$i]['currencyId'];
                $productPrice->price = $request->productPrice[$i]['value'];

                $productPrice->save();
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

    // section Delete_Product
    public function deleteProduct(Request $request){
        try {
            DB::beginTransaction();

            $result = ShopProduct::whereId($request->productId)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Product deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Product not found'
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

        Storage::disk('public')->put('/productsImages' . '/' . $avatarName, $img, 'public');
        $path = '/productsImages/' . $avatarName;

        return $path;
    }

}
