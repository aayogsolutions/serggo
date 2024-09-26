<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AdminController,
    BusinessSetting,
    EmployeeController,
    CustomRoleController,
    CategoryController,
    SystemController,
    BusinessSettingsController,
    ProductController,
    AttributeController,
    BannersController,
    BrandsController,
    DisplayController,
    TagController,
    BranchController
};

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [AdminController::class,'login'])->name('login');
    Route::post('/', [AdminController::class,'login_submit'])->name('login.submit');

    Route::group(['middleware' => 'Admin-auth'], function(){
        Route::get('/dashboard', [AdminController::class,'dashboard'])->name('dashboard');
        Route::get('/logout', [AdminController::class,'logout'])->name('auth.logout');
    
        Route::get('/test', [AdminController::class,'test'])->name('test');

        Route::get('terms-and-conditions', [BusinessSetting::class, 'termsAndConditions'])->name('terms-and-conditions');
        Route::post('terms-and-conditions', [BusinessSetting::class, 'termsAndConditionsUpdate']);

        Route::get('privacy-policy', [BusinessSetting::class, 'privacyPolicy'])->name('privacy-policy');
        Route::post('privacy-policy', [BusinessSetting::class, 'privacyPolicyUpdate']);

        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settingsUpdate'])->name('UpdateSettings');
        Route::post('settings-password', [SystemController::class, 'settingsPasswordUpdate'])->name('settings-password');

        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('add', [CategoryController::class, 'index'])->name('add');
            Route::get('add-sub-category', [CategoryController::class, 'subIndex'])->name('add-sub-category');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [CategoryController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CategoryController::class, 'delete'])->name('delete');
            Route::get('priority', [CategoryController::class, 'priority'])->name('priority');
        });

        Route::group(['prefix' => 'brands', 'as' => 'brands.'], function () {
            Route::get('add', [BrandsController::class, 'index'])->name('add');
            Route::post('store', [BrandsController::class, 'store'])->name('store');
            Route::get('edit/{id}', [BrandsController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [BrandsController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [BrandsController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [BrandsController::class, 'delete'])->name('delete');
            Route::get('priority', [BrandsController::class, 'priority'])->name('priority');
        });

        Route::group(['prefix' => 'banners', 'as' => 'banners.'], function () {

            Route::group(['prefix' => 'splash', 'as' => 'splash.'], function() {
                Route::get('add', [BannersController::class, 'SplashIndex'])->name('add');
                Route::post('store', [BannersController::class, 'SplashStore'])->name('store');
                Route::get('edit/{id}', [BannersController::class, 'SplashEdit'])->name('edit');
                Route::post('update/{id}', [BannersController::class, 'SplashUpdate'])->name('update');
                Route::get('status/{id}/{status}/{type}', [BannersController::class, 'SplashStatus'])->name('status');
                Route::delete('delete/{id}', [BannersController::class, 'SplashDelete'])->name('delete');
            });

            Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {
                Route::get('add', [BannersController::class, 'AuthIndex'])->name('add');
                Route::post('store', [BannersController::class, 'AuthStore'])->name('store');
                Route::get('edit/{id}', [BannersController::class, 'AuthEdit'])->name('edit');
                Route::post('update/{id}', [BannersController::class, 'AuthUpdate'])->name('update');
                Route::get('status/{id}/{status}/{type}/{screen}', [BannersController::class, 'AuthStatus'])->name('status');
                Route::delete('delete/{id}', [BannersController::class, 'AuthDelete'])->name('delete');
            });

            Route::group(['prefix' => 'home', 'as' => 'home.'], function() {
                Route::get('add', [BannersController::class, 'HomeIndex'])->name('add');
                Route::post('store', [BannersController::class, 'HomeStore'])->name('store');
                Route::get('edit/{id}', [BannersController::class, 'HomeEdit'])->name('edit');
                Route::post('update/{id}', [BannersController::class, 'HomeUpdate'])->name('update');
                Route::get('status/{id}/{status}/{type}', [BannersController::class, 'HomeStatus'])->name('status');
                Route::delete('delete/{id}', [BannersController::class, 'HomeDelete'])->name('delete');
            });

            Route::group(['prefix' => 'homeslider', 'as' => 'homeslider.'], function() {
                Route::get('add', [BannersController::class, 'HomeSliderIndex'])->name('add');
                Route::post('store', [BannersController::class, 'HomeSliderStore'])->name('store');
                Route::get('edit/{id}', [BannersController::class, 'HomeSliderEdit'])->name('edit');
                Route::post('update/{id}', [BannersController::class, 'HomeSliderUpdate'])->name('update');
                Route::get('status/{id}/{status}/{type}', [BannersController::class, 'HomeSliderStatus'])->name('status');
                Route::delete('delete/{id}', [BannersController::class, 'HomeSliderDelete'])->name('delete');
                Route::get('priority', [BannersController::class, 'HomeSliderPriority'])->name('priority');
            });
        });

        Route::group(['prefix' => 'display', 'as' => 'display.'], function () {
            
            Route::get('add', [DisplayController::class, 'Index'])->name('add');
            Route::post('store', [DisplayController::class, 'Store'])->name('store');
            Route::get('status/{id}/{status}/{type}/{section_type}', [DisplayController::class, 'Status'])->name('status');
            Route::get('edit/{id}', [DisplayController::class, 'Edit'])->name('edit');
            Route::post('add-content/{id}', [DisplayController::class, 'AddContent'])->name('add.content');


            Route::post('section-detail', [DisplayController::class, 'DetailSection'])->name('detail.section');
            Route::post('section-item', [DisplayController::class, 'DetailItem'])->name('detail.item');
            Route::post('update-section', [DisplayController::class, 'UpdateSection'])->name('update.section');
            Route::post('update-item', [DisplayController::class, 'UpdateItem'])->name('update.item');
            Route::get('priority', [DisplayController::class, 'Priority'])->name('priority');
            Route::get('sectionpriority', [DisplayController::class, 'SectionPriority'])->name('section.priority');


            Route::delete('delete/{id}', [DisplayController::class, 'DeleteSection'])->name('delete');
            Route::delete('delete-content/{id}', [DisplayController::class, 'DeleteContent'])->name('delete.content');
        });

        Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
            Route::get('add-new', [AttributeController::class, 'index'])->name('add-new');
            Route::post('store', [AttributeController::class, 'store'])->name('store');
            Route::get('edit/{id}', [AttributeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [AttributeController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [AttributeController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [AttributeController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'branch', 'as' => 'branch.'], function () {
            Route::get('add-new', [BranchController::class, 'index'])->name('add-new');
            Route::get('list', [BranchController::class, 'list'])->name('list');
            Route::post('store', [BranchController::class, 'store'])->name('store');
            Route::get('edit/{id}', [BranchController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [BranchController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [BranchController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [BranchController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::get('add-new', [ProductController::class, 'index'])->name('add-new');
            Route::get('get-categories', [ProductController::class, 'getCategories'])->name('get-categories');
            Route::post('variant-combination', [ProductController::class, 'variantCombination'])->name('variant-combination');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
            Route::get('feature/{id}/{is_featured}', [ProductController::class, 'feature'])->name('feature');
            Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');

            Route::post('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [ProductController::class, 'delete'])->name('delete');
            
            Route::get('bulk-import', [ProductController::class, 'bulkImportIndex'])->name('bulk-import');
            Route::post('bulk-import', [ProductController::class, 'bulkImportProduct']);
            Route::get('bulk-export-index', [ProductController::class, 'bulkExportIndex'])->name('bulk-export-index');
            Route::get('bulk-export', [ProductController::class, 'bulkExportProduct'])->name('bulk-export');
            Route::get('remove-image/{id}/{name}', [ProductController::class, 'removeImage'])->name('remove-image');
            Route::post('daily-needs', [ProductController::class, 'dailyNeeds'])->name('daily-needs');
            Route::get('limited-stock', [ProductController::class, 'limitedStock'])->name('limited-stock');
            Route::get('get-variations', [ProductController::class, 'getVariations'])->name('get-variations');
            Route::post('update-quantity', [ProductController::class, 'updateQuantity'])->name('update-quantity');
            


            Route::post('Product_ajax', [ProductController::class, 'ProductAjax'])->name('ProductAjax');
            Route::post('Product_data_ajax', [ProductController::class, 'ProductDataAjax'])->name('ProductDataAjax');
            Route::post('Edit_product_column', [ProductController::class, 'Edit_product_column'])->name('Edit_product_column');

            Route::group(['prefix' => 'tag', 'as' => 'tag.'], function () {
                Route::get('add-new', [TagController::class, 'index'])->name('add-new');
                Route::post('store', [TagController::class, 'store'])->name('store');
                Route::get('edit/{id}', [TagController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [TagController::class, 'update'])->name('update');
                Route::delete('delete/{id}', [TagController::class, 'delete'])->name('delete');
                Route::get('status/{id}/{status}', [TagController::class, 'status'])->name('status');
            });
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
            Route::get('add-new', [EmployeeController::class, 'index']);
            Route::post('add-new', [EmployeeController::class, 'store'])->name('add-new');
            Route::get('list', [EmployeeController::class, 'list'])->name('list');
            Route::get('update/{id}', [EmployeeController::class, 'edit'])->name('update');
            Route::post('update/{id}', [EmployeeController::class, 'update']);
            Route::get('status/{id}/{status}', [EmployeeController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [EmployeeController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.'], function () {
            Route::get('create', [CustomRoleController::class, 'create'])->name('create');
            Route::post('create', [CustomRoleController::class, 'store'])->name('store');
            Route::get('update/{id}', [CustomRoleController::class, 'edit'])->name('update');
            Route::post('update/{id}', [CustomRoleController::class, 'update']);
            Route::delete('delete/{id}', [CustomRoleController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [CustomRoleController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function () {

            Route::group(['prefix'=>'store','as'=>'store.'], function() {
                Route::get('ecom-setup', [BusinessSettingsController::class, 'businessSettingsIndex'])->name('ecom-setup');
                Route::get('delivery-setup', [BusinessSettingsController::class, 'deliveryIndex'])->name('delivery-setup');
                Route::post('delivery-setup-update', [BusinessSettingsController::class, 'deliverySetupUpdate'])->name('delivery-setup-update');
                Route::post('update-setup', [BusinessSettingsController::class, 'businessSetup'])->name('update-setup');
                Route::get('maintenance-mode', [BusinessSettingsController::class, 'maintenanceMode'])->name('maintenance-mode');
                Route::get('currency-position/{position}', [BusinessSettingsController::class, 'currencySymbolPosition'])->name('currency-position');
                Route::get('self-pickup/{status}', [BusinessSettingsController::class, 'selfPickupStatus'])->name('self-pickup');
                // Route::get('location-setup', [LocationSettingsController::class, 'locationIndex'])->name('location-setup');
                // Route::post('update-location', [LocationSettingsController::class, 'locationSetup'])->name('update-location');
                Route::get('main-branch-setup', [BusinessSettingsController::class, 'mainBranchSetup'])->name('main-branch-setup');
                Route::get('product-setup', [BusinessSettingsController::class, 'productSetup'])->name('product-setup');
                Route::post('product-setup-update', [BusinessSettingsController::class, 'productSetupUpdate'])->name('product-setup-update');
                Route::get('cookies-setup', [BusinessSettingsController::class, 'cookiesSetup'])->name('cookies-setup');
                Route::post('cookies-setup-update', [BusinessSettingsController::class, 'cookiesSetupUpdate'])->name('cookies-setup-update');
                Route::get('max-amount-status/{status}', [BusinessSettingsController::class, 'maximumAmountStatus'])->name('max-amount-status');
                Route::get('free-delivery-status/{status}', [BusinessSettingsController::class, 'freeDeliveryStatus'])->name('free-delivery-status');
                Route::get('dm-self-registration/{status}', [BusinessSettingsController::class, 'deliverymanSelfRegistrationStatus'])->name('dm-self-registration');
                Route::get('otp-setup', [BusinessSettingsController::class, 'OTPSetup'])->name('otp-setup');
                Route::post('otp-setup-update', [BusinessSettingsController::class, 'OTPSetupUpdate'])->name('otp-setup-update');
                Route::get('guest-checkout/{status}', [BusinessSettingsController::class, 'guestCheckoutStatus'])->name('guest-checkout');
                Route::get('partial-payment/{status}', [BusinessSettingsController::class, 'partialPaymentStatus'])->name('partial-payment');
                Route::get('customer-setup', [BusinessSettingsController::class, 'customerSetup'])->name('customer-setup');
                Route::post('customer-setup-update', [BusinessSettingsController::class, 'customerSetupUpdate'])->name('customer-setup-update');
                Route::get('order-setup', [BusinessSettingsController::class, 'orderSetup'])->name('order-setup');
                Route::post('order-setup-update', [BusinessSettingsController::class, 'orderSetupUpdate'])->name('order-setup-update');

                Route::get('referral-income-setup', [BusinessSettingsController::class, 'ReferralIncomeSetup'])->name('referral-income-setup');
                Route::post('referral-income-setup-update', [BusinessSettingsController::class, 'ReferralIncomeSetupUpdate'])->name('referral-income-setup-update');

                // Route::group(['prefix' => 'timeSlot', 'as' => 'timeSlot.'], function () {
                //     Route::get('add-new', [TimeSlotController::class, 'index'])->name('add-new');
                //     Route::post('store', [TimeSlotController::class, 'store'])->name('store');
                //     Route::get('update/{id}', [TimeSlotController::class, 'edit'])->name('update');
                //     Route::post('update/{id}', [TimeSlotController::class, 'update']);
                //     Route::get('status/{id}/{status}', [TimeSlotController::class, 'status'])->name('status');
                //     Route::delete('delete/{id}', [TimeSlotController::class, 'delete'])->name('delete');
                // });
            });

            Route::group(['prefix'=>'web-app','as'=>'web-app.'], function() {
                Route::get('mail-config', [BusinessSettingsController::class, 'mailIndex'])->name('mail-config');
                Route::post('mail-config', [BusinessSettingsController::class, 'mailConfig']);
                Route::get('mail-config/status/{status}', [BusinessSettingsController::class, 'mailConfigStatus'])->name('mail-config.status');
                Route::post('mail-send', [BusinessSettingsController::class, 'mailSend'])->name('mail-send');

                Route::get('payment-method', [BusinessSettingsController::class, 'paymentIndex'])->name('payment-method');
                Route::post('payment-method-update/{payment_method}', [BusinessSettingsController::class, 'paymentUpdate'])->name('payment-method-update');
                Route::post('payment-config-update', [BusinessSettingsController::class, 'paymentConfigUpdate'])->name('payment-config-update');


                Route::group(['prefix'=>'system-setup','as'=>'system-setup.'], function() {
                    Route::get('app-setting', [BusinessSettingsController::class, 'appSettingIndex'])->name('app_setting');
                    Route::post('app-setting', [BusinessSettingsController::class, 'appSettingUpdate']);
                    Route::get('firebase-message-config', [BusinessSettingsController::class, 'firebaseMessageConfigIndex'])->name('firebase_message_config_index');
                    Route::post('firebase-message-config', [BusinessSettingsController::class, 'firebaseMessageConfig'])->name('firebase_message_config');
                });

                Route::group(['prefix' => 'third-party', 'as' => 'third-party.'], function () {
                    Route::get('map-api-settings',[BusinessSettingsController::class, 'mapApiSetting'])->name('map-api-settings');
                    Route::post('map-api-store',[BusinessSettingsController::class, 'mapApiStore'])->name('map-api-store');
                    Route::get('social-media', [BusinessSettingsController::class, 'socialMedia'])->name('social-media');
                    Route::get('fetch', [BusinessSettingsController::class, 'fetch'])->name('fetch');
                    Route::post('social-media-store', [BusinessSettingsController::class, 'socialMediaStore'])->name('social-media-store');
                    Route::post('social-media-edit', [BusinessSettingsController::class, 'socialMediaEdit'])->name('social-media-edit');
                    Route::post('social-media-update', [BusinessSettingsController::class, 'socialMediaUpdate'])->name('social-media-update');
                    Route::post('social-media-delete', [BusinessSettingsController::class, 'socialMediaDelete'])->name('social-media-delete');
                    Route::post('social-media-status-update', [BusinessSettingsController::class, 'socialMediaStatusUpdate'])->name('social-media-status-update');
                    Route::get('social-media-login', [BusinessSettingsController::class, 'socialMediaLogin'])->name('social-media-login');
                    Route::get('google-social-login/{status}', [BusinessSettingsController::class, 'googleSocialLogin'])->name('google-social-login');
                    Route::get('facebook-social-login/{status}', [BusinessSettingsController::class, 'facebookSocialLogin'])->name('facebook-social-login');
                    Route::post('update-apple-login', [BusinessSettingsController::class, 'updateAppleLogin'])->name('update-apple-login');
                    Route::get('recaptcha', [BusinessSettingsController::class, 'recaptchaIndex'])->name('recaptcha_index');
                    Route::post('recaptcha-update', [BusinessSettingsController::class, 'recaptchaUpdate'])->name('recaptcha_update');
                    Route::get('fcm-index', [BusinessSettingsController::class, 'fcmIndex'])->name('fcm-index');
                    Route::get('fcm-config', [BusinessSettingsController::class, 'fcmConfig'])->name('fcm-config');
                    Route::post('update-fcm', [BusinessSettingsController::class, 'updateFcm'])->name('update-fcm');
                    Route::post('update-fcm-messages', [BusinessSettingsController::class, 'updateFcmMessages'])->name('update-fcm-messages');
                    Route::get('chat-index', [BusinessSettingsController::class, 'chatIndex'])->name('chat-index');
                    Route::post('update-chat', [BusinessSettingsController::class, 'updateChat'])->name('update-chat');
                    Route::get('firebase-otp-verification', [BusinessSettingsController::class, 'firebaseOTPVerification'])->name('firebase-otp-verification');
                    Route::post('firebase-otp-verification-update', [BusinessSettingsController::class, 'firebaseOTPVerificationUpdate'])->name('firebase-otp-verification-update');

                    // Route::group(['prefix' => 'offline-payment', 'as' => 'offline-payment.'], function(){
                    //     Route::get('list', [OfflinePaymentMethodController::class, 'list'])->name('list');
                    //     Route::get('add', [OfflinePaymentMethodController::class, 'add'])->name('add');
                    //     Route::post('store', [OfflinePaymentMethodController::class, 'store'])->name('store');
                    //     Route::get('edit/{id}', [OfflinePaymentMethodController::class, 'edit'])->name('edit');
                    //     Route::post('update/{id}', [OfflinePaymentMethodController::class, 'update'])->name('update');
                    //     Route::get('status/{id}/{status}', [OfflinePaymentMethodController::class, 'status'])->name('status');
                    //     Route::post('delete', [OfflinePaymentMethodController::class, 'delete'])->name('delete');
                    // });
                });

            });

            Route::group(['prefix' => 'page-setup', 'as' => 'page-setup.'], function () {
                Route::get('terms-and-conditions', [BusinessSettingsController::class, 'termsAndConditions'])->name('terms-and-conditions');
                Route::post('terms-and-conditions', [BusinessSettingsController::class, 'termsAndConditionsUpdate']);

                Route::get('privacy-policy', [BusinessSettingsController::class, 'privacyPolicy'])->name('privacy-policy');
                Route::post('privacy-policy', [BusinessSettingsController::class, 'privacyPolicyUpdate']);

                Route::get('about-us', [BusinessSettingsController::class, 'aboutUs'])->name('about-us');
                Route::post('about-us', [BusinessSettingsController::class, 'aboutUsUpdate']);

                Route::get('faq', [BusinessSettingsController::class, 'faq'])->name('faq');
                Route::post('faq', [BusinessSettingsController::class, 'faqUpdate']);

                Route::get('cancellation-policy', [BusinessSettingsController::class, 'cancellationPolicy'])->name('cancellation-policy');
                Route::post('cancellation-policy', [BusinessSettingsController::class, 'cancellationPolicyUpdate']);
                Route::get('cancellation-policy/status/{status}', [BusinessSettingsController::class, 'cancellationPolicyStatus'])->name('cancellation-policy.status');

                Route::get('refund-policy', [BusinessSettingsController::class, 'refundPolicy'])->name('refund-policy');
                Route::post('refund-policy', [BusinessSettingsController::class, 'refundPolicyUpdate']);
                Route::get('refund-policy/status/{status}', [BusinessSettingsController::class, 'refundPolicyStatus'])->name('refund-policy.status');

                Route::get('return-policy', [BusinessSettingsController::class, 'returnPolicy'])->name('return-policy');
                Route::post('return-policy', [BusinessSettingsController::class, 'returnPolicyUpdate']);
                Route::get('return-policy/status/{status}', [BusinessSettingsController::class, 'returnPolicyStatus'])->name('return-policy.status');

            });
        });
    });
});
