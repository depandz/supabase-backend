<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\DriverController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\ProvinceController;
use App\Http\Controllers\Api\V1\PickupRequestController;

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
    Route::post('otp/send',[AuthController::class, 'sendOtp']);
    Route::post('otp/verify',[AuthController::class, 'verifyOtp']);
    //clients
    Route::controller(ClientController::class)->prefix('clients')->group(function(){
        // Route::post('otp/send','sendOtp');
        // Route::post('otp/verify','sendOtp');
        Route::get('/','index');
        Route::post('/register','register');
        // Route::post('/login','login');
        Route::get('{s_id}','show');
        Route::put('{s_id}/update','update');
        Route::post('{s_id}/update-photo','updatePhoto');
        Route::get('{s_id}/pickups-history','pickupsHistory');
    });
    //driver
    Route::controller(DriverController::class)->prefix('drivers')->group(function(){
        Route::get('/','index');
        Route::post('/login','login');
        Route::get('{s_id}','show');
        Route::put('{s_id}/update','update');
        Route::post('{s_id}/update-photo','updatePhoto');
        Route::post('{s_id}/pickup-requests/{pickup_sid}/accept','AcceptDeclinePickupRequest');
        Route::post('{s_id}/pickup-requests/{pickup_sid}/decline','AcceptDeclinePickupRequest');
        Route::post('{s_id}/pickup-requests/{pickup_sid}/confirm-reached-to-client','confirmReachedToClient');
        Route::put('{s_id}/switch-online-status','switchOnlineStatus');
        Route::get('{s_id}/pickups-history','pickupsHistory');

    });
    Route::controller(PickupRequestController::class)->prefix('pickup-requests')->group(function(){
        Route::post('/initialize','initialize');
        Route::post('/{s_id}/confirm','confirm'); //client
        Route::post('/{s_id}/finish','finish'); //driver
        Route::post('/{s_id}/cancel','cancel');
        Route::post('/{s_id}/rate','rateDriver');
    });
    Route::get('/pages/{lang}/{title}', [SettingController::class, 'page']);
    Route::get('/app-settings', [SettingController::class, 'appSettings']);
});
