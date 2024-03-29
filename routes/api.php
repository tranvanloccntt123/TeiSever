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
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//import controller api v1
use App\Http\Controllers\API\v1\Application as ApplicationControllerv1;
use App\Http\Controllers\API\v1\PMCategory as PMCategoryControllerv1;
use App\Http\Controllers\API\v1\PMProduct as PMProductControllerv1;
use App\Http\Controllers\API\v1\Authentication as Authenticationv1;
use App\Http\Controllers\API\v1\Chat as Chatv1;
use App\Http\Controllers\API\v1\Post as Postv1;
use App\Http\Controllers\API\v1\Profile as Profilev1;
use App\Http\Controllers\API\v1\Events as Eventv1;
use App\Http\Controllers\API\v1\RelationShip as RelationShipv1;
use App\Http\Controllers\API\v1\TokenDevice as TokenDevicev1;
Route::prefix('v1')->group(function(){
    Route::prefix('application')->group(function(){
        Route::post('create', [ApplicationControllerv1::class, 'create']);
    });
    Route::prefix('pm')->group(function(){
        Route::prefix('category')->group(function(){
            Route::get('/', [PMCategoryControllerv1::class, 'get']);
            Route::post('create', [PMCategoryControllerv1::class, 'create']);
            Route::post('join',[PMCategoryControllerv1::class, 'joinApplication']);
        });
        Route::prefix('product')->group(function(){
            Route::get('/', [PMProductControllerv1::class, 'get']);
            Route::get('/paginate',[PMProductControllerv1::class, 'paginate']);
            Route::post('create', [PMProductControllerv1::class, 'create']);
        });
    });

    Route::middleware(['application_verified'])->group(function(){
        Route::middleware(['auth:sanctum', 'user_verified'])->group(function(){
            Route::prefix('chat')->group(function(){
                Route::get('list', [Chatv1::class, 'getListMessage']);
                Route::get('messages', [Chatv1::class, 'getCurrentMessages']);
                Route::get('detail', [Chatv1::class, 'getDetailMessage']);
                Route::post('send', [Chatv1::class, 'sendMessage']);
                Route::get('get/group', [Chatv1::class, 'getOrCreateGroupMessage']);
            });
        
            Route::prefix('post')->group(function(){
                Route::post('create', [Postv1::class, 'create']);
                Route::post('delete', [Postv1::class, 'delete']);
                Route::get('list', [Postv1::class, 'list']);
                Route::post('update', [Postv1::class, 'update']);
            });
        
            Route::prefix('profile')->group(function(){
                Route::prefix('change')->group(function(){
                    Route::post('avatar', [Profilev1::class, 'changeAvatar']);
                    Route::post('background', [Profilev1::class, 'changeBackground']);
                    Route::post('detail', [Profilev1::class, 'updateDetail']);
                });
        
                Route::get('/visit', [Profilev1::class, 'viewProfile']);
        
                Route::get('search', [Profilev1::class, 'searchUser']);
        
                Route::prefix('relation')->group(function(){
                    Route::get('list', [RelationShipv1::class, 'getList']);
                    Route::post('request', [RelationShipv1::class, 'create']);
                    Route::get('check', [RelationShipv1::class, 'checkRelation']);
                    Route::post('near/description', [RelationShipv1::class, 'createRelationShipDescription']);
                    Route::get('near/description', [RelationShipv1::class, 'getRelationShipDescription']);
                });
            });   

            Route::prefix('events')->group(function(){
                Route::post('create', [Eventv1::class, 'create']);
                Route::get('detail', [Eventv1::class, 'detail']);
                Route::get('get', [Eventv1::class, 'get']);
                Route::get('get/from', [Eventv1::class, 'getScheduleFromDate']);
                Route::post('delete', [Eventv1::class, 'deleteSchedule']);
            });

            Route::prefix('/device')->group(function(){
                Route::post('/', [TokenDevicev1::class, 'sendTokenDevice']);
            });
        });
        
        Route::prefix('auth')->group(function(){
            Route::post('register', [Authenticationv1::class, 'createAccount'])->name('api.auth.register');
            Route::post('login', [Authenticationv1::class, 'createToken'])->name('api.auth.login');
        });
    });
    
});


Route::post('/upload-evanto', [ApplicationControllerv1::class, 'saveEvanto']);