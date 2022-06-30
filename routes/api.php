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

Route::prefix("v1")->group(function(){
    Route::prefix("application")->group(function(){
        Route::post("insert", [ApplicationControllerv1::class, "create"]);
    });
});