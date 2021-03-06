<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//import controller api v1

use App\Http\Controllers\API\v1\Application as ApplicationControllerv1;
use App\Http\Controllers\API\v1\PMCategory as PMCategoryControllerv1;
use App\Http\Controllers\API\v1\PMProduct as PMProductControllerv1;

Route::prefix("v1")->group(function(){
    Route::prefix("application")->group(function(){
        Route::post("create", [ApplicationControllerv1::class, "create"]);
    });
    Route::prefix("pm")->group(function(){
        Route::prefix("category")->group(function(){
            Route::get('/', [PMCategoryControllerv1::class, "get"]);
            Route::post('create', [PMCategoryControllerv1::class, "create"]);
            Route::post('join',[PMCategoryControllerv1::class, "joinApplication"]);
        });
        Route::prefix("product")->group(function(){
            Route::get("/", [PMProductControllerv1::class, "get"]);
            Route::get("/paginate",[PMProductControllerv1::class, "paginate"]);
            Route::post("create", [PMProductControllerv1::class, "create"]);
        });
    });
});