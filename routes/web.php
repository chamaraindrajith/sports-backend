<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetDataController;
use App\Http\Controllers\GetDataController;
use App\Http\Controllers\GetJsonDataController;

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
    return view('welcome');
});

Route::group(['prefix'=>'api'], function(){
    Route::group(['prefix'=>'/set/{sport}'], function(){
        // Route::get('/live', [SetDataController::class, 'setDataLive']); 
        Route::get('/date/{date}', [SetDataController::class, 'setDataByDate']); 
    });
    Route::group(['prefix'=>'/get/{sport}'], function(){
        // Route::get('/live', [GetDataController::class, 'getDataLive']); 
        Route::get('/today', [GetDataController::class, 'today']); 
        Route::get('/date/{date}', [GetDataController::class, 'getDataByDate']); 

        // Route::get('/live/json', [GetJsonDataController::class, 'getDataLive']); 
        Route::get('/today/json', [GetJsonDataController::class, 'today']); 
        Route::get('/date/{date}/json', [GetJsonDataController::class, 'getDataByDate']); 
    });
});