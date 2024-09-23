<?php

use App\Http\Controllers\Api\{
    ApiAuthController
};
use App\Http\Controllers\Api\product\{
    DashboardController
};
use App\Http\Controllers\Api\service\{
    DashboardController as ServiceDashboardController,
};
use App\Http\Controllers\Api\vender\{
    DashboardController as VenderDashboardController
};
use Illuminate\Support\Facades\Route;

Route::post('/',[ApiAuthController::class, 'OTPRequest']);
Route::get('/resendOtp/{number}',[ApiAuthController::class, 'resendOtp']);
Route::post('/otpsubmit',[ApiAuthController::class, 'OTPSubmit']);

Route::post('/register', [ApiAuthController::class,'registeruser']);

Route::post('/signup/{provider}', [ApiAuthController::class,'SignupWithSocial']);

// Route::group(['middleware' => ['auth:api']], function(){
    Route::group(['prefix' => 'product'], function(){
        Route::get('/dashboard', [DashboardController::class,'Index']);
    });

    Route::group(['prefix' => 'service'], function(){
        Route::get('/dashboard', [ServiceDashboardController::class,'Index']);
    });

    Route::group(['prefix' => 'vender'], function(){
        Route::get('/dashboard', [VenderDashboardController::class,'Index']);
    });
    
// });


