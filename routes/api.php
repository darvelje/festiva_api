<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

// section Routes_User
    Route::get('/v1/user/all', [\App\Http\Controllers\v1\UserController::class, 'getUsers']);
    Route::get('/v1/user/view/{userId}', [\App\Http\Controllers\v1\UserController::class, 'getUserById']);
    Route::post('/v1/user/new', [\App\Http\Controllers\v1\UserController::class, 'newUser']);
    Route::post('/v1/user/update/{userId}', [\App\Http\Controllers\v1\UserController::class, 'updateUser']);
    Route::delete('/v1/user/delete', [\App\Http\Controllers\v1\UserController::class, 'deleteUser']);

// section Routes_Business
    Route::get('/v1/business/all', [\App\Http\Controllers\v1\BusinessController::class, 'getBusinesses']);
    Route::get('/v1/business/view/{businessSlug}', [\App\Http\Controllers\v1\BusinessController::class, 'getBusinessBySlug']);
    Route::post('/v1/business/new', [\App\Http\Controllers\v1\BusinessController::class, 'newBusiness']);
    Route::post('/v1/business/update', [\App\Http\Controllers\v1\BusinessController::class, 'updateBusiness']);
    Route::delete('/v1/business/delete', [\App\Http\Controllers\v1\BusinessController::class, 'deleteBusiness']);

// section Routes_Category
    Route::get('/v1/category/all', [\App\Http\Controllers\v1\CategoryController::class, 'getCategories']);
    Route::get('/v1/category/view/{categorySlug}', [\App\Http\Controllers\v1\CategoryController::class, 'getCategoryBySlug']);
    Route::post('/v1/category/new', [\App\Http\Controllers\v1\CategoryController::class, 'newCategory']);
    Route::post('/v1/category/update', [\App\Http\Controllers\v1\CategoryController::class, 'updateCategory']);
    Route::delete('/v1/category/delete', [\App\Http\Controllers\v1\CategoryController::class, 'deleteCategory']);

// section Routes_Settings
    Route::get('/v1/settings/all', [\App\Http\Controllers\v1\SettingsController::class, 'getSettings']);
    Route::post('/v1/settings/set', [\App\Http\Controllers\v1\SettingsController::class, 'setSettings']);
    Route::post('/v1/settings/update', [\App\Http\Controllers\v1\SettingsController::class, 'updateSettings']);
    Route::delete('/v1/settings/delete', [\App\Http\Controllers\v1\SettingsController::class, 'deleteSettings']);

// section Routes_Currency
    Route::get('/v1/currency/all', [\App\Http\Controllers\v1\CurrencyController::class, 'getCurrencies']);
    Route::get('/v1/currency/view/{currencyId}', [\App\Http\Controllers\v1\CurrencyController::class, 'getCurrencyById']);
    Route::post('/v1/currency/new', [\App\Http\Controllers\v1\CurrencyController::class, 'newCurrency']);
    Route::post('/v1/currency/update', [\App\Http\Controllers\v1\CurrencyController::class, 'updateCurrency']);
    Route::delete('/v1/currency/delete', [\App\Http\Controllers\v1\CurrencyController::class, 'deleteCurrency']);

// section Routes_Product


