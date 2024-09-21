<?php

use App\Http\Controllers\Api\{
    ApiAuthController,
    DashboardController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::post('/',[ApiAuthController::class, 'OTPRequest']);
Route::get('/resendOtp/{number}',[ApiAuthController::class, 'resendOtp']);
Route::post('/otpsubmit',[ApiAuthController::class, 'OTPSubmit']);

Route::post('/register', [ApiAuthController::class,'registeruser']);

Route::post('/signup/{provider}', [ApiAuthController::class,'SignupWithSocial']);

Route::group(['middleware' => ['auth:api']], function(){
    Route::group(['prefix' => 'product'], function(){
        Route::get('/dashboard', [DashboardController::class,'Index']);
    });

    Route::group(['prefix' => 'service'], function(){
        Route::get('/dashboard', [DashboardController::class,'Index']);
    });

    Route::group(['prefix' => 'vender'], function(){
        Route::get('/dashboard', [DashboardController::class,'Index']);
    });

    Route::group(['prefix' => 'serviceman'], function(){
        Route::get('/dashboard', [DashboardController::class,'Index']);
    });
    
});


