<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('admin.index');
});

use App\Http\Controllers\Admin\Applications;
use App\Http\Controllers\Admin\Modules;
Route::prefix("/manager")->group(function(){
    Route::prefix("/application")->group(function(){
        Route::get("/", [Applications::class, "index"])->name("m.applications");
        Route::post("/create", [Applications::class, "create"])->name("m.applications.create.submit");
        Route::post("/delete", [Applications::class, "delete"])->name("m.applications.delete.submit");
        Route::post("/update", [Applications::class, "edit"])->name("m.applications.edit.submit");
        Route::prefix("query")->group(function(){
            Route::get("/{id}", [Applications::class, "manager"])->name('m.application.query');
            Route::post("/select", [Modules::class, "appSelectModule"])->name('m.module.select.submit');
        });
    });
});