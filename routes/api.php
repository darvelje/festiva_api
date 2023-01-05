<?php

use App\Http\Controllers\v1\BusinessController;
use App\Http\Controllers\v1\LocationController;
use App\Http\Controllers\v1\BusinessCouponsController;
use App\Http\Controllers\v1\BusinessCurrencyController;
use App\Http\Controllers\v1\BusinessDeliveryZonesController;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\CurrencyController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\PaymentController;
use App\Http\Controllers\v1\ProductController;
use App\Http\Controllers\v1\SettingsController;
use App\Http\Controllers\v1\UserAddressController;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\PromosController;
use App\Http\Controllers\v1\SuscriptorsController;
use App\Http\Controllers\v1\TropiPayController;
use App\Http\Controllers\v1\RentalhoPayController;
use App\Http\Controllers\v1\PaymentMethodsController;
use App\Http\Controllers\v1\PermissionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    // section Routes_User
    Route::post('/v1/user/me', [UserController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/v1/register', [UserController::class, 'register']);
    Route::post('/v1/login', [UserController::class, 'login']);
    Route::get('/v1/user/all', [UserController::class, 'getUsers']);
    Route::get('/v1/user/view/{userId}', [UserController::class, 'getUserById']);
    Route::post('/v1/user/new', [UserController::class, 'newUser']);
    Route::post('/v1/user/update', [UserController::class, 'updateUser']);
    Route::post('/v1/user/token', [UserController::class, 'getTokenUser']);
    Route::delete('/v1/user/delete', [UserController::class, 'deleteUser']);
    Route::post('/v1/user/add-favorite', [UserController::class, 'addUserFavoritesProducts'])->middleware('auth:sanctum');
    Route::get('/v1/user/favorites', [UserController::class, 'getUserFavoritesProducts'])->middleware('auth:sanctum');
    Route::delete('/v1/user/delete-favorite', [UserController::class, 'deleteUserFavoritesProducts'])->middleware('auth:sanctum');

    //Rols and permissions
    Route::get('/v1/rols/all', [PermissionsController::class, 'getRols']);
    Route::get('/v1/rols/staff', [PermissionsController::class, 'getStaff']);
    Route::post('/v1/rols/add', [PermissionsController::class, 'addRol']);
    Route::post('/v1/rols/adduser', [PermissionsController::class, 'addUserToRol']);
    Route::post('/v1/rols/permissiontorol', [PermissionsController::class, 'addPermissionToRol']);
    Route::post('/v1/rols/update', [PermissionsController::class, 'updateRol']);
    Route::delete('/v1/rols/staff/delete', [PermissionsController::class, 'deleteRoleForStaff']);
    Route::delete('/v1/rols/delete', [PermissionsController::class, 'deleteRol']);
    Route::delete('/v1/rols/deletePermissionToEdit', [PermissionsController::class, 'deletePermissionToEdit']);

    // section Routes_Business
    Route::get('/v1/business/all', [BusinessController::class, 'getBusinesses']);
    Route::post('/v1/business/all/front', [BusinessController::class, 'getAllBusinesses']);
    Route::get('/v1/business/view/{businessSlug}', [BusinessController::class, 'getBusinessBySlug']);
    Route::get('/v1/business/self/{businessId}', [BusinessController::class, 'getBusinessById']);
    Route::get('/v1/business/data-pie-chart/{businessSlug}', [BusinessController::class, 'getChartProductsSoldByCategoriesByBusinessSlug']);
    Route::post('/v1/business/data-canva-chart', [BusinessController::class, 'getChartOrdersStatsByBusinessSlug']);
    Route::post('/v1/business/new', [BusinessController::class, 'newBusiness']);
    Route::post('/v1/business/update', [BusinessController::class, 'updateBusiness']);
    Route::post('/v1/business/update/delivery', [BusinessController::class, 'changeStatusDelivery']);
    Route::post('/v1/business/update/pick', [BusinessController::class, 'changeStatusPick']);
    Route::delete('/v1/business/delete', [BusinessController::class, 'deleteBusiness']);

    // section Routes_Location
    Route::get('/v1/provinces/all', [LocationController::class, 'getProvinces']);
    Route::get('/v1/provinces/delivery-zones/', [LocationController::class, 'getProvincesWithShop']);
    Route::get('/v1/municipalities/{provinceId}', [LocationController::class, 'getMunicipalities']);
    Route::get('/v1/municipalities/delivery-zones/{provinceId}', [LocationController::class, 'getMunicipalitiesWithShop']);
    Route::get('/v1/localities/{municipalityId}', [LocationController::class, 'getLocalities']);
    Route::get('/v1/localities/delivery-zones/{municipalityId}', [LocationController::class, 'getLocalitiesWithShop']);
    Route::get('/v1/tropipay/countries', [LocationController::class, 'getCountriesTropipay']);

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
    Route::post('/v1/business/currencies', [BusinessCurrencyController::class, 'getBusinessCurrencyByBusinessId']);
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
    Route::post('/v1/category/all/front', [CategoryController::class, 'getAllCategories']);
    Route::post('/v1/category/random', [CategoryController::class, 'getCategoriesByMunicipalityRandom']);

    Route::get('/v1/category/view/{categorySlug}', [CategoryController::class, 'getCategoryBySlug']);
    Route::post('/v1/category/new', [CategoryController::class, 'newCategory']);
    Route::post('/v1/category/update', [CategoryController::class, 'updateCategory']);
    Route::delete('/v1/category/delete', [CategoryController::class, 'deleteCategory']);

// section Routes_Settings
    Route::get('/v1/settings/all', [SettingsController::class, 'getSettings']);
    Route::get('/v1/settings/delivery', [SettingsController::class, 'getSettingsDelivery']);
    Route::get('/v1/settings/pages', [SettingsController::class, 'getSettingsPages']);
    Route::post('/v1/settings/set', [SettingsController::class, 'setSettings']);
    Route::post('/v1/settings/send-message-help', [UserController::class, 'sendHelpMessage']);
    Route::post('/v1/settings/delivery/province', [SettingsController::class, 'updateProvinceDeliverySetting']);
    Route::post('/v1/settings/delivery/municipality', [SettingsController::class, 'updateMunicipalityDeliverySetting']);
    Route::post('/v1/settings/delivery/locality', [SettingsController::class, 'updateLocalityDeliverySetting']);
    Route::post('/v1/settings/update', [SettingsController::class, 'updateSettings']);
    Route::post('/v1/settings/data-canva-chart', [SettingsController::class, 'getChartOrdersStats']);
    Route::get('/v1/settings/data-pie-chart', [SettingsController::class, 'getChartProductsSoldByCategories']);
    Route::delete('/v1/settings/delete', [SettingsController::class, 'deleteSettings']);
    Route::post('/v1/settings/payment-method/new', [PaymentMethodsController::class, 'newPaymentMethod']);
    Route::get('/v1/settings/payment-method/view', [PaymentMethodsController::class, 'getPaymentMethod']);

// section Routes_Currency
    Route::get('/v1/currency/all', [CurrencyController::class, 'getCurrencies']);
    Route::get('/v1/currency/view/{currencyId}', [CurrencyController::class, 'getCurrencyById']);
    Route::post('/v1/currency/new', [CurrencyController::class, 'newCurrency']);
    Route::post('/v1/currency/update', [CurrencyController::class, 'updateCurrency']);
    Route::delete('/v1/currency/delete', [CurrencyController::class, 'deleteCurrency']);

// section Routes_Product
    Route::get('/v1/product/all', [ProductController::class, 'getProducts']);
    Route::post('/v1/product/all/front', [ProductController::class, 'getAllProducts']);
    Route::get('/v1/product/most-seller', [ProductController::class, 'getProductsMostSeller']);
    Route::get('/v1/product/most-seller/category/{categorySlug}', [ProductController::class, 'getProductMostSellerByCategorySlug']);
    Route::post('/v1/product/most-seller/category', [ProductController::class, 'getAllProductMostSellerByCategorySlug']);
    Route::get('/v1/product/view/{productSlug}', [ProductController::class, 'getProductBySlug']);
    Route::get('/v1/products/category/{categorySlug}', [ProductController::class, 'getProductByCategorySlug']);
    Route::post('/v1/products/category/front', [ProductController::class, 'getAllProductsByCategorySlug']);
    Route::get('/v1/business/products/{businessUrl}', [ProductController::class, 'getProductByBusinessSlug']);
    Route::post('/v1/product/new', [ProductController::class, 'newProduct']);
    Route::post('/v1/product/update', [ProductController::class, 'updateProduct']);
    Route::delete('/v1/product/delete', [ProductController::class, 'deleteProduct']);

// section Routes_User_Address
    Route::get('/v1/user/address/all', [UserAddressController::class, 'getUserAddresses']);
    Route::get('/v1/user/address/view/{userAddressId}', [UserAddressController::class, 'getUserAddressById']);
    Route::get('/v1/user/address/{userId}', [UserAddressController::class, 'getUserAddressByUserId']);
    Route::post('/v1/user/address/new', [UserAddressController::class, 'newUserAddress']);
    Route::post('/v1/user/address/update', [UserAddressController::class, 'updateUserAddress']);
    Route::delete('/v1/user/address/delete', [UserAddressController::class, 'deleteUserAddress']);

// section Routes_Promos
    Route::get('/v1/promos/all', [PromosController::class, 'getPromos']);
    Route::get('/v1/promos/view/{promoId}', [PromosController::class, 'getPromoById']);
    Route::get('/v1/promos/types', [PromosController::class, 'getPromosType']);
    Route::post('/v1/promos/home', [PromosController::class, 'getPromosHome']);
    Route::post('/v1/promos/category', [PromosController::class, 'getPromosByCategoryId']);
    Route::post('/v1/promos/new', [PromosController::class, 'newPromo']);
    Route::post('/v1/promos/update', [PromosController::class, 'updatePromo']);
    Route::delete('/v1/promos/delete', [PromosController::class, 'deletePromo']);

// section Routes_Orders
    Route::get('/v1/order/all', [OrderController::class, 'getOrders']);
    Route::get('/v1/order/status/all', [OrderController::class, 'getOrdersByStatus']);
    Route::get('/v1/order/view/{orderId}', [OrderController::class, 'getOrderById'])->middleware('auth:sanctum');
    Route::get('/v1/order/user', [OrderController::class, 'getOrdersByUser'])->middleware('auth:sanctum');
    Route::get('/v1/order/business/{businessSlug}', [OrderController::class, 'getOrdersByBusinessSlug']);
    Route::post('/v1/order/new', [OrderController::class, 'newOrder'])->middleware('auth:sanctum');
    Route::post('/v1/order/change-status', [OrderController::class, 'changeStatus'])->middleware('auth:sanctum');
    Route::delete('/v1/order/delete', [OrderController::class, 'deleteOrder'])->middleware('auth:sanctum');

// section Suscriptors
    Route::get('/v1/suscriptors/all', [SuscriptorsController::class, 'getSuscriptors']);
    Route::post('/v1/suscriptors/new', [SuscriptorsController::class, 'newSuscriptor']);
    Route::delete('/v1/suscriptors/delete', [SuscriptorsController::class, 'deleteSuscriptor']);

// section Tropipay and Rentalho
    Route::post('/v1/tropipay/api/notification', [TropiPayController::class, 'responseNotification']);
    Route::post('/v1/rentalho/api/notification', [RentalhoPayController::class, 'responseNotification']);

// section Payments
    Route::post('/v1/payment/new', [PaymentController::class, 'newPayment'])->middleware('auth:sanctum');

