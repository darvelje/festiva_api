<?php

use App\Http\Controllers\v1\BusinessController;
use App\Http\Controllers\v1\BusinessCouponsController;
use App\Http\Controllers\v1\BusinessCurrencyController;
use App\Http\Controllers\v1\BusinessDeliveryZonesController;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\CurrencyController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\ProductController;
use App\Http\Controllers\v1\SettingsController;
use App\Http\Controllers\v1\UserAddressController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

// section Routes_User
    Route::get('/v1/user/all', [UserController::class, 'getUsers']);
    Route::get('/v1/user/view/{userId}', [UserController::class, 'getUserById']);
    Route::post('/v1/user/new', [UserController::class, 'newUser']);
    Route::post('/v1/user/update', [UserController::class, 'updateUser']);
    Route::delete('/v1/user/delete', [UserController::class, 'deleteUser']);

// section Routes_Business
    Route::get('/v1/business/all', [BusinessController::class, 'getBusinesses']);
    Route::get('/v1/business/view/{businessSlug}', [BusinessController::class, 'getBusinessBySlug']);
    Route::post('/v1/business/new', [BusinessController::class, 'newBusiness']);
    Route::post('/v1/business/update', [BusinessController::class, 'updateBusiness']);
    Route::delete('/v1/business/delete', [BusinessController::class, 'deleteBusiness']);

// section Routes_Business_Coupons
    Route::get('/v1/business/coupons/all', [BusinessCouponsController::class, 'getShopCoupons']);
    Route::get('/v1/business/coupons/view/{shopCouponId}', [BusinessCouponsController::class, 'getShopCouponById']);
    Route::get('/v1/business/coupons/{businessUrl}', [BusinessCouponsController::class, 'getShopCouponByShopSlug']);
    Route::post('/v1/business/coupons/new', [BusinessCouponsController::class, 'newShopCoupon']);
    Route::post('/v1/business/coupons/update', [BusinessCouponsController::class, 'updateShopCoupon']);
    Route::delete('/v1/business/coupons/delete', [BusinessCouponsController::class, 'deleteShopCoupon']);

// section Routes_Shop_Currency
    Route::get('/v1/business/currency/all', [BusinessCurrencyController::class, 'getBusinessCurrencies']);
    Route::post('/v1/business/currency/view', [BusinessCurrencyController::class, 'getBusinessCurrencyById']);
    Route::get('/v1/business/currency/{businessUrl}', [BusinessCurrencyController::class, 'getBusinessCurrencyBySlug']);
    Route::post('/v1/business/currency/new', [BusinessCurrencyController::class, 'newBusinessCurrency']);
    Route::post('/v1/business/currency/update', [BusinessCurrencyController::class, 'updateBusinessCurrency']);
    Route::delete('/v1/business/currency/delete', [BusinessCurrencyController::class, 'deleteBusinessCurrency']);

// section Routes_Business_Delivery_Zones
    Route::get('/v1/business/delivery/zones/{businessUrl}', [BusinessDeliveryZonesController::class, 'getBusinessDeliveryZonesByBusinessSlug']);
    Route::get('/v1/business/delivery/zones/view/{businessDeliveryZoneId}', [BusinessDeliveryZonesController::class, 'getBusinessDeliveryZoneById']);
    Route::post('/v1/business/delivery/zones/new', [BusinessDeliveryZonesController::class, 'newBusinessDeliveryZone']);
    Route::post('/v1/business/delivery/zones/update', [BusinessDeliveryZonesController::class, 'updateBusinessDeliveryZone']);
    Route::delete('/v1/business/delivery/zones/delete', [BusinessDeliveryZonesController::class, 'deleteBusinessDeliveryZone']);

// section Routes_Category
    Route::get('/v1/category/all', [CategoryController::class, 'getCategories']);
    Route::get('/v1/category/view/{categorySlug}', [CategoryController::class, 'getCategoryBySlug']);
    Route::post('/v1/category/new', [CategoryController::class, 'newCategory']);
    Route::post('/v1/category/update', [CategoryController::class, 'updateCategory']);
    Route::delete('/v1/category/delete', [CategoryController::class, 'deleteCategory']);

// section Routes_Settings
    Route::get('/v1/settings/all', [SettingsController::class, 'getSettings']);
    Route::post('/v1/settings/set', [SettingsController::class, 'setSettings']);
    Route::post('/v1/settings/update', [SettingsController::class, 'updateSettings']);
    Route::delete('/v1/settings/delete', [SettingsController::class, 'deleteSettings']);

// section Routes_Currency
    Route::get('/v1/currency/all', [CurrencyController::class, 'getCurrencies']);
    Route::get('/v1/currency/view/{currencyId}', [CurrencyController::class, 'getCurrencyById']);
    Route::post('/v1/currency/new', [CurrencyController::class, 'newCurrency']);
    Route::post('/v1/currency/update', [CurrencyController::class, 'updateCurrency']);
    Route::delete('/v1/currency/delete', [CurrencyController::class, 'deleteCurrency']);

// section Routes_Product
    Route::get('/v1/product/all', [ProductController::class, 'getProducts']);
    Route::get('/v1/product/view/{productSlug}', [ProductController::class, 'getProductBySlug']);
    Route::get('/v1/business/products/{businessUrl}', [ProductController::class, 'getProductByBusinessSlug']);
    Route::post('/v1/product/new', [ProductController::class, 'newProduct']);
    Route::post('/v1/product/update', [ProductController::class, 'updateProduct']);
    Route::delete('/v1/product/delete', [ProductController::class, 'deleteProduct']);

// section Routes_Order
    Route::get('/v1/order/all', [OrderController::class, 'getOrders']);
    Route::get('/v1/order/user/{userId}', [OrderController::class, 'getOrdersByUserId']);
    Route::get('/v1/order/view/{orderId}', [OrderController::class, 'getOrderById']);
    Route::post('/v1/order/new', [OrderController::class, 'newOrder']);
    Route::delete('/v1/order/delete', [OrderController::class, 'deleteOrder']);

// section Routes_User_Address
    Route::get('/v1/user/address/all', [UserAddressController::class, 'getUserAddresses']);
    Route::get('/v1/user/address/view/{userAddressId}', [UserAddressController::class, 'getUserAddressById']);
    Route::get('/v1/user/address/{userId}', [UserAddressController::class, 'getUserAddressByUserId']);
    Route::post('/v1/user/address/new', [UserAddressController::class, 'newUserAddress']);
    Route::post('/v1/user/address/update', [UserAddressController::class, 'updateUserAddress']);
    Route::delete('/v1/user/address/delete', [UserAddressController::class, 'deleteUserAddress']);
