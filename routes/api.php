<?php

use App\Http\Controllers\Api\user\{
    BannerController,
    InformationController
};
use App\Http\Controllers\Api\auth\{
    ApiAuthController
};
use App\Http\Controllers\Api\user\product\{
    AddressController,
    CategoryCntroller,
    CustomerController,
    CartController,
    DashboardController,
    OrderController,
    ProductController,
    WishlistController
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
    Route::post('/signup/{provider}', [ApiAuthController::class,'SignupWithSocial']);
    Route::post('/otpsubmit',[ApiAuthController::class, 'OTPSubmit']);

    Route::put('/register', [ApiAuthController::class,'registeruser']);
});

Route::group(['prefix' => 'banner'], function() {

    Route::get('/auth/{ui}/{section}',[BannerController::class, 'Auth']);
    Route::get('/splash/{ui}',[BannerController::class, 'Splash']);
});

Route::group(['prefix' => 'information'], function() {

    Route::get('/about-us',[InformationController::class, 'AboutUs']);
    Route::get('/term-conditions',[InformationController::class, 'TermConditions']);
    Route::get('/privacy-policy',[InformationController::class, 'PrivacyPolicy']);
    
});

Route::group(['prefix' => 'product'], function(){

    Route::get('/dashboard', [DashboardController::class,'Index']);
    Route::get('/category_display/{ui}', [DashboardController::class,'CategoryDisplay']);
    Route::post('/display_section_details', [ProductController::class,'Display']);

    Route::get('/product_details', [ProductController::class,'Index']);
    Route::get('/brand_details', [ProductController::class,'BrandSelected']);
    Route::get('/category_inside/assets', [CategoryCntroller::class,'CategoryDetailsAssets']);
    Route::get('/category_inside', [CategoryCntroller::class,'CategoryDetails']);
    Route::get('/subcategory_inside/assets', [CategoryCntroller::class,'SubCategoryDetailsAssets']);
    Route::get('/subcategory_inside', [CategoryCntroller::class,'SubCategoryDetails']);

    Route::get('/search', [ProductController::class,'Search']);

    Route::get('/selection/deliver/timeline', [ProductController::class,'DeliveryTimeLine']);
});

Route::group(['prefix' => 'service'], function(){

    Route::get('/dashboard', [ServiceDashboardController::class,'Index']);
});

Route::group(['prefix' => 'vender'], function(){
  
    Route::get('/dashboard', [VenderDashboardController::class,'Index']);
});

Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::group(['prefix' => 'user'], function(){

        // User Location Routes
        Route::post('/user-location', [CustomerController::class,'UserLocation']);

        // User Profiles Routes
        Route::get('/profile', [CustomerController::class,'Profile']);
        Route::post('/profile', [CustomerController::class,'ProfileSubmit']);
        
        // Referral Info Route
        Route::get('/referral/info', [CustomerController::class,'ReferralInfo']);

        Route::group(['prefix' => 'product'], function(){
            
            Route::get('/cart', [CartController::class,'Cart']);

            // Product Favorite Routes
            Route::get('/favorite', [WishlistController::class,'Favorite']);
            Route::get('/favorite/list', [WishlistController::class,'FavoriteList']);

            // Selected Product Route
            Route::get('/selected/info', [ProductController::class,'SelectedProduct']);
        });
        
        // Wallet Transaction info
        Route::get('/transaction', [CustomerController::class,'transaction']);

        // User Address Routes
        Route::group(['prefix' => 'address'], function(){

            Route::get('/list', [AddressController::class,'addresslist']);
            Route::post('/store', [AddressController::class,'addressStore']);
            Route::post('/update/{id}', [AddressController::class,'addressUpdate']);
            Route::post('/delete/{id}', [AddressController::class,'addressDelete']);
        });
    });

    Route::group(['prefix' => 'order'], function(){

        Route::group(['prefix' => 'product'], function(){

            Route::get('checkout',[OrderController::class,'Checkout']);
            Route::post('place-order',[OrderController::class,'PlaceOrder']);
        });
    });
});


