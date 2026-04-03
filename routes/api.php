<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyApiController;
use App\Http\Controllers\Api\SiteApiController;
use App\Http\Controllers\Api\TagsApiController;
use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);

Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => '/company'], function () {
        Route::get('/', [CompanyApiController::class,'index']);
    });

    Route::group(['prefix' => '/sites'], function () {
        Route::get('/', [SiteApiController::class, 'index']);
    });

    Route::group(['prefix' => '/nfc-tags'], function () {
        Route::get('/', [TagsApiController::class, 'index']);
    });
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
