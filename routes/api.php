<?php

use App\Http\Controllers\Api\user\BannerController;
use App\Http\Controllers\Api\user\product\CartController;
use App\Http\Controllers\Api\user\product\WishlistController;
use App\Http\Controllers\Api\auth\{
    ApiAuthController
};
use App\Http\Controllers\Api\user\product\{
    DashboardController,
    ProductController
};
use App\Http\Controllers\Api\user\service\{
    DashboardController as ServiceDashboardController,
};
use App\Http\Controllers\Api\vender\{
    DashboardController as VenderDashboardController
};
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function() {

    Route::post('/',[ApiAuthController::class, 'OTPRequest']);
    Route::get('/resendOtp/{number}',[ApiAuthController::class, 'resendOtp']);
    Route::post('/otpsubmit',[ApiAuthController::class, 'OTPSubmit']);
    
    Route::post('/register', [ApiAuthController::class,'registeruser']);
    
    Route::post('/signup/{provider}', [ApiAuthController::class,'SignupWithSocial']);
});

Route::group(['prefix' => 'banner'], function() {

    Route::get('/auth/{ui}/{section}',[BannerController::class, 'Auth']);
    Route::get('/splash/{ui}',[BannerController::class, 'Splash']);
});

Route::group(['prefix' => 'product'], function(){

    Route::get('/dashboard', [DashboardController::class,'Index']);
    Route::get('/category_display/{ui}', [DashboardController::class,'CategoryDisplay']);
    Route::post('/display_section_details', [ProductController::class,'display']);

    Route::get('/product_details', [ProductController::class,'Index']);
    
});

Route::group(['prefix' => 'service'], function(){

    Route::get('/dashboard', [ServiceDashboardController::class,'Index']);
});

Route::group(['prefix' => 'vender'], function(){
  
    Route::get('/dashboard', [VenderDashboardController::class,'Index']);
});

Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::group(['prefix' => 'product'], function(){

        Route::get('/cart', [CartController::class,'cart']);
        Route::get('/wishlist', [WishlistController::class,'wishlist']);
    });
});


