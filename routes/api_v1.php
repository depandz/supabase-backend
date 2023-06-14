<?php

use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\DriverController;
use App\Http\Controllers\Api\V1\ProvinceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['as' => 'api_v1.'], function () {
    //provinces
    Route::get('provinces', [ProvinceController::class, 'index']);
    Route::get('provinces/find-by-code/{code}', [ProvinceController::class, 'findByCode']);

    //clients 
    Route::controller(ClientController::class)->prefix('clients')->group(function(){
        Route::get('/','index');
        Route::post('/register','register');
        Route::post('/login','login');
        Route::get('{s_id}','show');
        Route::put('{s_id}/update','update');
        Route::post('{s_id}/update-photo','updatePhoto');
    });
    //driver 
    Route::controller(DriverController::class)->prefix('drivers')->group(function(){
        Route::post('/login','login');
        Route::get('{s_id}','show');
        Route::put('{s_id}/update','update');
    });
});
