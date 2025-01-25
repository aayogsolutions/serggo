<?php

use App\Http\Controllers\Api\user\{
    BannerController,
    InformationController,
    PaymentController
};
use App\Http\Controllers\Api\auth\{
    ApiAuthController
};
use App\Http\Controllers\Api\partner\{
    AuthController,
    DashboardController as PartnerDashboardController,
    InformationController as PartnerInformationController,
    OrderController as PartnerOrderController
};
use App\Http\Controllers\Api\user\amc\DashboardController as AmcDashboardController;
use App\Http\Controllers\Api\user\amc\OrderController as AmcOrderController;
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
    OrderController as ServiceOrderController,
};
use App\Http\Controllers\Api\vendor\{
    AuthController as VendorAuthController,
    DashboardController as VenderDashboardController,
    InformationController as VendorInformationController,
    OrderController as VendorOrderController,
    ProductController as VendorProductController,
    ProfileController
};
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth'], function() 
{
    Route::post('/',[ApiAuthController::class, 'OTPRequest']);
    Route::get('/resendOtp/{number}',[ApiAuthController::class, 'resendOtp']);
    Route::post('/signup/{provider}', [ApiAuthController::class,'SignupWithSocial']);
    Route::post('/otpsubmit',[ApiAuthController::class, 'OTPSubmit']);

    Route::put('/register', [ApiAuthController::class,'registeruser']);

    Route::group(['prefix' => 'partner'], function() 
    {
        //Login Route
        Route::post('/login',[AuthController::class, 'LogIn']);

        //Sign Up Routes
        Route::get('/category',[AuthController::class, 'Category']);
        Route::post('/signup',[AuthController::class, 'SignUp']);
        Route::get('/resent-otp',[AuthController::class, 'ResendOTP']);
        Route::post('/otp-submit',[AuthController::class, 'OtpSubmit']);
        Route::post('/kyc-submit',[AuthController::class, 'KYCSubmit']);

        //Forget Password Routes
        Route::post('/forget/password/number',[AuthController::class, 'ForgetPasswordNumber']);
        Route::post('/forget/password/otp',[AuthController::class, 'ForgetPasswordOTP']);
        Route::post('/forget/password/submit',[AuthController::class, 'ForgetPasswordSubmit']);
    });

    Route::group(['prefix' => 'vendor'], function() 
    {
        //Login Route
        Route::post('/login',[VendorAuthController::class, 'LogIn']);

        //Sign Up Routes
        Route::get('/category',[VendorAuthController::class, 'Category']);
        Route::post('/signup/personal',[VendorAuthController::class, 'SignUpPersonal']);
        Route::get('/resent-otp',[VendorAuthController::class, 'ResendOTP']);
        Route::post('/signup/business',[VendorAuthController::class, 'SignUpBusiness']);
        Route::post('/otp-submit',[VendorAuthController::class, 'OtpSubmit']);
        Route::post('/kyc-submit',[VendorAuthController::class, 'KYCSubmit']);

        //Forget Password Routes
        Route::post('/forget/password/number',[VendorAuthController::class, 'ForgetPasswordNumber']);
        Route::post('/forget/password/otp',[VendorAuthController::class, 'ForgetPasswordOTP']);
        Route::post('/forget/password/submit',[VendorAuthController::class, 'ForgetPasswordSubmit']);
    });
});

Route::group(['prefix' => 'banner'], function() 
{
    Route::get('/auth/{ui}/{section}',[BannerController::class, 'Auth']);
    Route::get('/splash/{ui}',[BannerController::class, 'Splash']);
});

Route::group(['prefix' => 'information'], function() 
{
    // FCM Routes
    Route::post('/fcm/update', [VenderDashboardController::class,'FcmUpdate']);

    // User Information Routes
    Route::get('/about-us',[InformationController::class, 'AboutUs']);
    Route::get('/term-conditions',[InformationController::class, 'TermConditions']);
    Route::get('/privacy-policy',[InformationController::class, 'PrivacyPolicy']);
    Route::get('/user-cities',[InformationController::class, 'UserCities']);

    Route::group(['prefix' => 'vendor'], function() 
    {
        Route::get('/term-conditions',[VendorInformationController::class, 'TermConditions']);
        Route::get('/privacy-policy',[VendorInformationController::class, 'PrivacyPolicy']);
        Route::get('/about-app',[VendorInformationController::class, 'AboutApp']);
    });

    Route::group(['prefix' => 'partner'], function() 
    {
        Route::get('/term-conditions',[PartnerInformationController::class, 'TermConditions']);
        Route::get('/privacy-policy',[PartnerInformationController::class, 'PrivacyPolicy']);
        Route::get('/about-app',[PartnerInformationController::class, 'AboutApp']);
    });

    // Payment Gateway Routes
    Route::group(['prefix' => 'payment'], function()
    {
        Route::get('/gateway', [PaymentController::class,'PaymentGateway']);
    });
});

Route::group(['prefix' => 'product'], function()
{
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

Route::group(['prefix' => 'service'], function()
{
    Route::get('/dashboard', [ServiceDashboardController::class,'Index']);
    Route::get('/search', [ServiceDashboardController::class,'Search']);

    Route::get('/display/details/{id}', [ServiceDashboardController::class,'DisplayDetails']);

    // Category Details Route
    Route::get('/category/{id}', [ServiceDashboardController::class,'CategoryDetails']);
    Route::get('/sub-category/details/{category_id}', [ServiceDashboardController::class,'SubCategoryDetails']);
    
});

Route::group(['prefix' => 'amc'], function()
{
    Route::get('/dashboard', [AmcDashboardController::class,'Index']);
    Route::get('/search', [AmcDashboardController::class,'Search']);

    Route::get('/plan/details/{id}', [AmcDashboardController::class,'PlanDetails']);
});

Route::group(['middleware' => ['auth:sanctum']], function()
{
    Route::group(['prefix' => 'user'], function()
    {
        // User Location Routes
        Route::post('/user-location', [CustomerController::class,'UserLocation']);

        // User Profiles Routes
        Route::get('/profile', [CustomerController::class,'Profile']);
        Route::post('/profile', [CustomerController::class,'ProfileSubmit']);
        
        // Referral Info Route
        Route::get('/referral/info', [CustomerController::class,'ReferralInfo']);

        Route::group(['prefix' => 'product'], function()
        {
            Route::get('/cart', [CartController::class,'Cart']);

            // Product Favorite Routes
            Route::get('/favorite', [WishlistController::class,'Favorite']);
            Route::get('/favorite/list', [WishlistController::class,'FavoriteList']);
        });

        Route::group(['prefix' => 'service'], function()
        {
            // Service Favorite Routes
            Route::get('/favorite', [WishlistController::class,'ServiceFavorite']);
            Route::get('/favorite/list', [WishlistController::class,'ServiceFavoriteList']);
        });
        
        // Wallet Transaction info
        Route::get('/transaction', [CustomerController::class,'transaction']);

        // Notification route
        Route::get('/notification', [CustomerController::class,'Notification']);

        // User Address Routes
        Route::group(['prefix' => 'address'], function()
        {
            Route::get('/list', [AddressController::class,'addresslist']);
            Route::post('/store', [AddressController::class,'addressStore']);
            Route::post('/update/{id}', [AddressController::class,'addressUpdate']);
            Route::post('/delete/{id}', [AddressController::class,'addressDelete']);
        });

        // Coupon route
        Route::get('/coupon', [CustomerController::class,'Coupon']);

        // Payment Gateway Routes
        Route::group(['prefix' => 'payment'], function()
        {
            Route::get('/gateway', [PaymentController::class,'PaymentGateway']);
        });
    });

    Route::group(['prefix' => 'order'], function()
    {
        Route::group(['prefix' => 'product'], function()
        {
            Route::get('checkout',[OrderController::class,'Checkout']);
            Route::post('place-order',[OrderController::class,'PlaceOrder']);

            // Order History
            Route::get('order/history',[OrderController::class,'OrderHistory']);
            Route::get('order/{id}',[OrderController::class,'OrderItems']);

            // Product Review Route
            Route::post('review',[OrderController::class,'OrderProductReview']);
        });

        Route::group(['prefix' => 'service'], function()
        {
            Route::get('checkout/{id}',[ServiceOrderController::class,'CheckOut']);
            Route::post('place-order',[ServiceOrderController::class,'PlaceOrder']);

            // Order History
            Route::get('order/history',[ServiceOrderController::class,'OrderHistory']);
            Route::get('order/{id}',[ServiceOrderController::class,'OrderItems']);

            // Product Review Route
            Route::post('review',[ServiceOrderController::class,'OrderProductReview']);
        });

        Route::group(['prefix' => 'amc'], function()
        {
            Route::get('checkout/{id}',[AmcOrderController::class,'CheckOut']);
            Route::post('place-order',[AmcOrderController::class,'PlaceOrder']);
            Route::get('book/checkout',[AmcOrderController::class,'BookedCheckOut']);
            Route::post('book',[AmcOrderController::class,'BookOrder']);
        });
    });

    Route::group(['prefix' => 'vendor'], function()
    {
        Route::get('/dashboard', [VenderDashboardController::class,'Index']);
        Route::get('/download/sale/report', [VenderDashboardController::class,'SaleReport']);
        Route::get('/notification/list', [VenderDashboardController::class,'NotificationList']);

        Route::group(['prefix' => 'profile'], function()
        {
            Route::get('/data', [ProfileController::class,'VendorData']);
            Route::post('/update', [ProfileController::class,'VendorUpdate']);
        });

        Route::group(['prefix' => 'product'], function()
        {
            Route::get('/create', [VendorProductController::class,'CreateProduct']);
            Route::get('/sub-category-detail/{id}', [VendorProductController::class,'SubCategoryDetail']);
            Route::post('/store', [VendorProductController::class,'StoreProduct']);
            Route::get('/list', [VendorProductController::class,'ProductList']);
            Route::get('/edit/{id}', [VendorProductController::class,'EditProduct']);
            Route::post('/update/{id}', [VendorProductController::class,'UpdateProduct']);
            Route::post('/delete/{id}', [VendorProductController::class,'DeleteProduct']);
            Route::post('/delete/image/{id}/{image}', [VendorProductController::class,'DeleteImage']);

            Route::get('/search', [VendorProductController::class,'ProductSearch']);
        });

        Route::group(['prefix' => 'order'], function()
        {
            Route::get('/list', [VendorOrderController::class,'OrderList']);
            Route::get('/detail/{id}', [VendorOrderController::class,'OrderDetail']);
            Route::get('/approval/{id}', [VendorOrderController::class,'OrderApproval']);
            Route::get('/status/{id}', [VendorOrderController::class,'OrderStatus']);
            Route::post('/date/{id}', [VendorOrderController::class,'OrderDate']);

            Route::get('/search', [VendorOrderController::class,'OrderSearch']);

            Route::get('/timesolts', [VendorOrderController::class,'OrderGetTimeSlots']);
            Route::post('/timesolts/{id}', [VendorOrderController::class,'OrderTimeSlots']);
        });

        Route::group(['prefix' => 'transaction'], function()
        {
            Route::post('/withdrawal/request', [VenderDashboardController::class,'WithdrawalRequest']);
            Route::get('/withdrawal/list', [VenderDashboardController::class,'WithdrawalList']);
            Route::get('/transaction/list', [VenderDashboardController::class,'TransactionList']);
        });
    });

    Route::group(['prefix' => 'partner'], function()
    {
        Route::get('dashboard', [PartnerDashboardController::class,'Index']);
        Route::get('/download/sale/report', [PartnerDashboardController::class,'SaleReport']);
        Route::get('/notification/list', [PartnerDashboardController::class,'NotificationList']);

        Route::group(['prefix' => 'profile'], function()
        {
            Route::get('/data', [ProfileController::class,'PartnerData']);
            Route::post('/update', [ProfileController::class,'PartnerUpdate']);
        });

        Route::group(['prefix' => 'order'], function()
        {
            Route::get('/completed/list', [PartnerOrderController::class,'OrderList']);
            Route::get('/ongoing/list', [PartnerOrderController::class,'OrderOngoingList']);
            Route::get('/approval/{id}', [PartnerOrderController::class,'OrderApproval']);
            Route::get('/status/{id}', [PartnerOrderController::class,'OrderStatus']);
            Route::post('/date/{id}', [PartnerOrderController::class,'OrderDate']);

            Route::get('/search', [PartnerOrderController::class,'OrderSearch']);

            Route::get('/timesolts', [PartnerOrderController::class,'OrderGetTimeSlots']);
            Route::post('/timesolts/{id}', [PartnerOrderController::class,'OrderTimeSlots']);
        });

        Route::group(['prefix' => 'transaction'], function()
        {
            Route::post('/withdrawal/request', [PartnerDashboardController::class,'WithdrawalRequest']);
            Route::get('/withdrawal/list', [PartnerDashboardController::class,'WithdrawalList']);
            Route::get('/transaction/list', [PartnerDashboardController::class,'TransactionList']);
        });
    });
});


