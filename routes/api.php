<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

// section Routes_User
    Route::get('/v1/user/getUsers', [\App\Http\Controllers\v1\UserController::class, 'getUsers']);
    Route::get('/v1/user/getUser/{userId}', [\App\Http\Controllers\v1\UserController::class, 'getUserById']);
    Route::post('/v1/user/new', [\App\Http\Controllers\v1\UserController::class, 'newUser']);
    Route::put('/v1/user/update/{userId}', [\App\Http\Controllers\v1\UserController::class, 'updateUser']);
    Route::delete('/v1/user/delete/{userId}', [\App\Http\Controllers\v1\UserController::class, 'deleteUser']);

// section Routes_Business
    Route::get('/v1/business/getBusinesses', [\App\Http\Controllers\v1\BusinessController::class, 'getBusinesses']);
    Route::get('/v1/business/getBusiness/{businessId}', [\App\Http\Controllers\v1\BusinessController::class, 'getBusinessById']);
    Route::post('/v1/business/new', [\App\Http\Controllers\v1\BusinessController::class, 'newBusiness']);
    Route::put('/v1/business/update/{businessId}', [\App\Http\Controllers\v1\BusinessController::class, 'updateBusiness']);
    Route::delete('/v1/business/delete/{businessId}', [\App\Http\Controllers\v1\BusinessController::class, 'deleteBusiness']);

// section Routes_Product


