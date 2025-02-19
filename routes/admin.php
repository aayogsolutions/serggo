<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AdminController,
    AmcController,
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
    DiscountController,
    CouponController,
    NotificationController,
    PageSetupController,
    ReviewsController,
    ReportController,
    WalletBonusController,
    CustomerController,
    CustomerWalletController,
    OrderController,
    VendorController,
    ServicemenController,
    DashboardController,
    TimeSlotController,
    InstallationChargesController
};
use App\Http\Controllers\Admin\Service\{
    ServiceController,
    ServiceCategoryController,
    ServiceTagController,
    ServiceAttributeController,
};
use App\Http\Controllers\Admin\vendor\BannerController;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [AdminController::class,'login'])->name('login');
    Route::post('/', [AdminController::class,'login_submit'])->name('login.submit');

    Route::group(['middleware' => 'Admin-auth'], function(){

        //New Order Check Route
        Route::get('/new-order', [AdminController::class,'NewOrder'])->name('new.order');

        Route::get('/logout', [AdminController::class,'logout'])->name('auth.logout');
        

        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('order-stats', [DashboardController::class, 'orderStats'])->name('order-stats');
        Route::get('dashboard/earning-statistics', [DashboardController::class, 'getEarningStatistics'])->name('dashboard.earning-statistics');
        Route::get('dashboard/order-statistics', [DashboardController::class, 'getOrderStatistics'])->name('dashboard.order-statistics');

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
            Route::get('intallable/{id}/{status}', [CategoryController::class, 'Intallable'])->name('intallable');
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

                Route::post('get-category', [BannersController::class, 'GetCategory'])->name('getCategory');
            });

            Route::group(['prefix' => 'homeslider', 'as' => 'homeslider.'], function() {
                Route::get('add', [BannersController::class, 'HomeSliderIndex'])->name('add');
                Route::post('store', [BannersController::class, 'HomeSliderStore'])->name('store');
                Route::get('edit/{id}', [BannersController::class, 'HomeSliderEdit'])->name('edit');
                Route::post('update/{id}', [BannersController::class, 'HomeSliderUpdate'])->name('update');
                Route::get('status/{id}/{status}', [BannersController::class, 'HomeSliderStatus'])->name('status');
                Route::delete('delete/{id}', [BannersController::class, 'HomeSliderDelete'])->name('delete');
                Route::get('priority', [BannersController::class, 'HomeSliderPriority'])->name('priority');
            });

            Route::group(['prefix' => 'subcategory-banners', 'as' => 'subcategory_banners.'], function () {
                Route::get('add', [BannersController::class, 'SubcategoryIndex'])->name('add');
                Route::get('subcategory-detail/{id}', [BannersController::class, 'SubcategoryDetailSection'])->name('detail.section');
                Route::post('add-content/{id}', [BannersController::class, 'SubcategoryAddContent'])->name('add.content');
                Route::get('priority', [BannersController::class, 'SubcategoryPriority'])->name('priority');
                Route::delete('delete/{id}', [BannersController::class, 'SubcategoryDelete'])->name('delete');
            });   
        });

        Route::group(['prefix' => 'service', 'as' => 'service.'], function () {

            Route::get('list', [ServiceController::class, 'list'])->name('list');
            Route::get('add-new', [ServiceController::class, 'index'])->name('add-new');
            Route::get('get-categories', [ServiceController::class, 'getCategories'])->name('get-categories');
            Route::get('get-child-categories', [ServiceController::class, 'getChildCategories'])->name('get-child-categories');
            Route::post('store', [ServiceController::class, 'store'])->name('store');
            Route::get('view/{id}', [ServiceController::class, 'view'])->name('view');
            Route::get('status/{id}/{status}', [ServiceController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [ServiceController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [ServiceController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ServiceController::class, 'update'])->name('update');
            Route::get('remove-image/{id}/{images}/{service?}/{name?}', [ServiceController::class, 'removeImage'])->name('remove-image');


            // Route::post('variant-combination', [ProductController::class, 'variantCombination'])->name('variant-combination');
            // Route::get('get-variations', [ProductController::class, 'getVariations'])->name('get-variations');
            // Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            
        


            Route::post('daily-needs', [ProductController::class, 'dailyNeeds'])->name('daily-needs');
            Route::get('limited-stock', [ProductController::class, 'limitedStock'])->name('limited-stock');
            // Route::get('feature/{id}/{is_featured}', [ProductController::class, 'feature'])->name('feature');
            // Route::post('update-quantity', [ProductController::class, 'updateQuantity'])->name('update-quantity');

            // Route::post('Product_ajax', [ProductController::class, 'ProductAjax'])->name('ProductAjax');
            // Route::post('Product_data_ajax', [ProductController::class, 'ProductDataAjax'])->name('ProductDataAjax');
            // Route::post('Edit_product_column', [ProductController::class, 'Edit_product_column'])->name('Edit_product_column');
                        

            Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                Route::get('add', [ServiceCategoryController::class, 'index'])->name('add');
                Route::get('add-sub-category', [ServiceCategoryController::class, 'subIndex'])->name('add-sub-category');
                Route::get('add-child-category', [ServiceCategoryController::class, 'childIndex'])->name('add-child-category');
                Route::post('store', [ServiceCategoryController::class, 'store'])->name('store');
                Route::post('child-store', [ServiceCategoryController::class, 'Childstore'])->name('child-store');
                Route::get('edit/{id}', [ServiceCategoryController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [ServiceCategoryController::class, 'update'])->name('update');
                Route::get('status/{id}/{status}', [ServiceCategoryController::class, 'status'])->name('status');
                Route::delete('delete/{id}', [ServiceCategoryController::class, 'delete'])->name('delete');
                Route::get('priority', [ServiceCategoryController::class, 'priority'])->name('priority');

            });

            Route::group(['prefix' => 'tag', 'as' => 'tag.'], function () {
                Route::get('add-new', [ServiceTagController::class, 'index'])->name('add-new');
                Route::post('store', [ServiceTagController::class, 'store'])->name('store');
                Route::get('edit/{id}', [ServiceTagController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [ServiceTagController::class, 'update'])->name('update');
                Route::delete('delete/{id}', [ServiceTagController::class, 'delete'])->name('delete');
                Route::get('status/{id}/{status}', [ServiceTagController::class, 'status'])->name('status');
            });

            Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
                Route::get('add-new', [ServiceAttributeController::class, 'index'])->name('add-new');
                Route::post('store', [ServiceAttributeController::class, 'store'])->name('store');
                Route::get('edit/{id}', [ServiceAttributeController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [ServiceAttributeController::class, 'update'])->name('update');
                Route::delete('delete/{id}', [ServiceAttributeController::class, 'delete'])->name('delete');
                Route::get('status/{id}/{status}', [ServiceAttributeController::class, 'status'])->name('status');
            });

            Route::group(['prefix' => 'subcategory-banners', 'as' => 'subcategory_banners.'], function () {
                Route::get('add', [ServiceController::class, 'SubcategoryIndex'])->name('add');
                Route::get('subcategory-detail/{id}', [ServiceController::class, 'SubcategoryDetailSection'])->name('detail.section');
                Route::post('add-content/{id}', [ServiceController::class, 'SubcategoryAddContent'])->name('add.content');
                Route::get('priority', [ServiceController::class, 'SubcategoryPriority'])->name('priority');
                Route::delete('delete/{id}', [ServiceController::class, 'SubcategoryDelete'])->name('delete');
            });
        });

        Route::group(['prefix' => 'display', 'as' => 'display.'], function () {
            
            Route::group(['prefix' => 'section'], function () {
                Route::get('add', [DisplayController::class, 'Index'])->name('add');
                Route::post('store', [DisplayController::class, 'Store'])->name('store');
                Route::get('status/{id}/{status}', [DisplayController::class, 'Status'])->name('status');
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

            Route::group(['prefix' => 'category', 'as' => 'category.'], function () {

                Route::get('add', [DisplayController::class, 'CategoryIndex'])->name('add');
                Route::post('store', [DisplayController::class, 'CategoryStore'])->name('store');
                Route::get('edit/{id}', [DisplayController::class, 'CategoryEdit'])->name('edit');
                Route::post('update/{id}', [DisplayController::class, 'CategoryUpdate'])->name('update');
                Route::get('status/{id}/{status}/{type}', [DisplayController::class, 'CategoryStatus'])->name('status');
                Route::delete('delete/{id}', [DisplayController::class, 'CategoryDelete'])->name('delete');
                Route::get('priority', [DisplayController::class, 'CategoryPriority'])->name('priority');
    
            });

        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
            Route::get('add-new', [CouponController::class, 'index'])->name('add-new');
            Route::post('store', [CouponController::class, 'store'])->name('store');
            Route::get('update/{id}', [CouponController::class, 'edit'])->name('update');
            Route::post('update/{id}', [CouponController::class, 'update']);
            Route::get('status/{id}/{status}', [CouponController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CouponController::class, 'delete'])->name('delete');
            Route::get('quick-view-details', [CouponController::class, 'details'])->name('quick-view-details');
        });

        Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
            Route::get('add-new', [NotificationController::class, 'index'])->name('add-new');
            Route::post('store', [NotificationController::class, 'store'])->name('store');
            Route::get('edit/{id}', [NotificationController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [NotificationController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [NotificationController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [NotificationController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'discount', 'as' => 'discount.'], function () {
            Route::get('add-new', [DiscountController::class, 'index'])->name('add-new');
            Route::post('store', [DiscountController::class, 'store'])->name('store');
            Route::get('edit/{id}', [DiscountController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [DiscountController::class, 'update'])->name('update');
            Route::get('list', [DiscountController::class, 'list'])->name('list');
            Route::get('status/{id}/{status}', [DiscountController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [DiscountController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
            Route::get('add-new', [AttributeController::class, 'index'])->name('add-new');
            Route::post('store', [AttributeController::class, 'store'])->name('store');
            Route::get('edit/{id}', [AttributeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [AttributeController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [AttributeController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [AttributeController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'installation', 'as' => 'installation.'], function () {
            Route::get('add-new', [InstallationChargesController::class, 'index'])->name('add-new');
            Route::post('store', [InstallationChargesController::class, 'store'])->name('store');
            Route::get('edit/{id}', [InstallationChargesController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [InstallationChargesController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [InstallationChargesController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [InstallationChargesController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'vendor', 'as' => 'vendor.'], function () {
          
            Route::get('list', [VendorController::class, 'list'])->name('list');
            Route::get('view/{user_id}', [VendorController::class, 'view'])->name('view');
            Route::post('advance/update', [VendorController::class, 'AdvanceUpdate'])->name('advance.update');
            
            Route::get('status/{id}/{status}', [VendorController::class, 'status'])->name('status');

            Route::group(['prefix' => 'banner', 'as' => 'banner.'], function () 
            {
                Route::get('add', [BannerController::class, 'index'])->name('add');
                Route::post('store', [BannerController::class, 'store'])->name('store');
                Route::get('edit/{id}', [BannerController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [BannerController::class, 'update'])->name('update');
                Route::get('status/{id}/{status}', [BannerController::class, 'Status'])->name('status');
                Route::delete('delete/{id}', [BannerController::class, 'delete'])->name('delete');
                Route::get('priority', [BannerController::class, 'priority'])->name('priority');
            });

            Route::group(['prefix' => 'category', 'as' => 'category.'], function () 
            {
                Route::get('add', [VendorController::class, 'index'])->name('add');
                Route::post('store', [VendorController::class, 'store'])->name('store');
                Route::get('edit/{id}', [VendorController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [VendorController::class, 'update'])->name('update');
                Route::get('status/{id}/{status}', [VendorController::class, 'CategoryStatus'])->name('status');
                Route::delete('delete/{id}', [VendorController::class, 'delete'])->name('delete');
                Route::get('priority', [VendorController::class, 'priority'])->name('priority');
            });

            Route::group(['prefix' => 'kyc', 'as' => 'kyc.'], function () 
            {
                Route::get('list', [VendorController::class, 'kycList'])->name('list');
                Route::get('view/{id}', [VendorController::class, 'kycView'])->name('view');
                Route::post('store/{id}', [VendorController::class, 'kycStore'])->name('store');
            });
        });

        Route::group(['prefix' => 'service_men', 'as' => 'service_men.'], function () 
        {
            Route::get('list', [ServicemenController::class, 'list'])->name('list');
            Route::get('view/{user_id}', [ServicemenController::class, 'view'])->name('view');
            Route::get('status/{id}/{status}', [ServicemenController::class, 'status'])->name('status');

            Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                Route::get('add', [ServicemenController::class, 'index'])->name('add');
                Route::post('store', [ServicemenController::class, 'store'])->name('store');
                Route::get('edit/{id}', [ServicemenController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [ServicemenController::class, 'update'])->name('update');
                Route::get('status/{id}/{status}', [ServicemenController::class, 'CategoryStatus'])->name('status');
                Route::delete('delete/{id}', [ServicemenController::class, 'delete'])->name('delete');
                Route::get('priority', [ServicemenController::class, 'priority'])->name('priority');
            });
            
            Route::group(['prefix' => 'kyc', 'as' => 'kyc.'], function () 
            {
                Route::get('list', [ServicemenController::class, 'kycList'])->name('list');
                Route::get('view/{id}', [ServicemenController::class, 'kycView'])->name('view');
                Route::post('store/{id}', [ServicemenController::class, 'kycStore'])->name('store');
            });
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
            Route::get('list', [ReviewsController::class, 'list'])->name('list');
            Route::get('status/{id}/{status}', [ReviewsController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
            Route::get('order', [ReportController::class, 'order_index'])->name('order');
            Route::get('earning', [ReportController::class, 'earning_index'])->name('earning');
            Route::post('set-date', [ReportController::class, 'setDate'])->name('set-date');
            Route::get('sale-report', [ReportController::class, 'saleReportIndex'])->name('sale-report');
            Route::get('export-sale-report', [ReportController::class, 'exportSaleReport'])->name('export-sale-report');
            Route::get('order-logs', [ReportController::class, 'OrderLogsReport'])->name('order.logs');
            // Route::get('expense', [ReportController::class, 'expenseIndex'])->name('expense');
            // Route::get('expense-export-excel', [ReportController::class, 'expenseExportExcel'])->name('expense.export.excel');
            // Route::get('expense-export-pdf', [ReportController::class, 'expenseSummaryPdf'])->name('expense.export.pdf');
            
        });

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {

            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::get('status-service', [OrderController::class, 'statusService'])->name('status.service');
            Route::get('add-service-man/{order_id}', [OrderController::class, 'addServiceman'])->name('add.service.man');
            Route::get('add-delivery-man/{order_id}', [OrderController::class, 'addDeliveryman'])->name('add.delivery.man');
            Route::get('payment-status', [OrderController::class, 'paymentStatus'])->name('payment-status');
            Route::get('order-category', [OrderController::class, 'OrderCategory'])->name('order.category');
            Route::get('order-date', [OrderController::class, 'OrderDate'])->name('order.date');
            Route::get('order-time', [OrderController::class, 'OrderTime'])->name('order.time');
            Route::get('generate-invoice/{id}', [OrderController::class, 'generateInvoice'])->name('generate-invoice')->withoutMiddleware(['module:order_management']);
            
            
            Route::post('search', [OrderController::class, 'search'])->name('search');
            Route::get('export/{status}', [OrderController::class, 'exportOrders'])->name('export');
            

            // Order Approval Routes
            Route::get('approval-request', [OrderController::class, 'ApprovalRequest'])->name('approval_request');
            Route::get('approval-request/view/product/{id}', [OrderController::class, 'ApprovalRequestView'])->name('approval.request.product.view');
            Route::get('approval-request/view/service/{id}', [OrderController::class, 'ApprovalRequestServiceView'])->name('approval.request.service.view');
            Route::post('approval-request/action/{id}', [OrderController::class, 'ApprovalRequestAction'])->name('approval.request.action');
            Route::post('update-service-men', [OrderController::class, 'UpdateServiceMen'])->name('update.service.men');




            
            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'addPaymentReferenceCode'])->name('add-payment-ref-code');
            Route::get('verify-offline-payment/{order_id}/{status}', [OrderController::class, 'verifyOfflinePayment']);

            Route::get('update-shipping', [OrderController::class, 'update-shipping'])->name('update-shipping');


            Route::get('edit-item/{id}', [OrderController::class, 'edit_item'])->name('edit_item');
            Route::post('edit-item/{id}', [OrderController::class, 'edit_item_submit'])->name('edit_item.submit');

            Route::post('Product_Replaced_ajax', [OrderController::class, 'ProductReplaceAjax'])->name('ProductReplaceAjax');
            Route::post('Product_delete_ajax', [OrderController::class, 'ProductDeleteAjax'])->name('ProductDeleteAjax');

        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {

            Route::get('list', [CustomerController::class, 'list'])->name('list');
            Route::get('view/{user_id}', [CustomerController::class, 'view'])->name('view');
            Route::post('search', [CustomerController::class, 'search'])->name('search');
            Route::get('subscribed-emails', [CustomerController::class, 'subscribedEmails'])->name('subscribed_emails');
            Route::delete('delete/{id}', [CustomerController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [CustomerController::class, 'status'])->name('status');
            Route::get('export', [CustomerController::class, 'exportCustomer'])->name('export');

            // Route::get('select-list', [CustomerWalletController::class, 'getCustomers'])->name('select-list');

            Route::group(['prefix' => 'wallet', 'as' => 'wallet.'], function () {
                Route::get('add-fund', [CustomerWalletController::class, 'addFundView'])->name('add-fund');
                Route::post('add-fund', [CustomerWalletController::class, 'addFund'])->name('add-fund-store');

                Route::get('deduct-fund', [CustomerWalletController::class, 'DeductFundView'])->name('deduct-fund');
                Route::post('deduct-fund', [CustomerWalletController::class, 'DeductFund'])->name('deduct-fund-store');
                
                Route::get('report', [CustomerWalletController::class, 'report'])->name('report');

                Route::group(['prefix' => 'bonus', 'as' => 'bonus.'], function () {
                    Route::get('index', [WalletBonusController::class, 'index'])->name('index');
                    Route::post('store',  [WalletBonusController::class, 'store'])->name('store');
                    Route::get('edit/{id}',  [WalletBonusController::class, 'edit'])->name('edit');
                    Route::post('update/{id}',  [WalletBonusController::class, 'update'])->name('update');
                    Route::get('status/{id}/{status}',  [WalletBonusController::class, 'status'])->name('status');
                    Route::delete('delete/{id}',  [WalletBonusController::class, 'delete'])->name('delete');
                });
            });
        });

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::get('add-new', [ProductController::class, 'index'])->name('add-new');
            Route::get('get-categories', [ProductController::class, 'getCategories'])->name('get-categories');
            Route::post('variant-combination', [ProductController::class, 'variantCombination'])->name('variant-combination');
            Route::get('get-variations', [ProductController::class, 'getVariations'])->name('get-variations');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
            Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');

            Route::post('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [ProductController::class, 'delete'])->name('delete');
            Route::post('update-quantity/{id}', [ProductController::class, 'updateQuantity'])->name('stack.adjust');

            
            Route::get('bulk-import', [ProductController::class, 'bulkImportIndex'])->name('bulk-import');
            Route::post('bulk-import', [ProductController::class, 'bulkImportProduct']);
            Route::get('bulk-export-index', [ProductController::class, 'bulkExportIndex'])->name('bulk-export-index');
            Route::get('bulk-export', [ProductController::class, 'bulkExportProduct'])->name('bulk-export');
            Route::get('remove-image/{id}/{images}/{product?}/{name?}', [ProductController::class, 'removeImage'])->name('remove-image');

            Route::group(['prefix' => 'vendor'],function(){
                Route::get('pending-list', [ProductController::class, 'PendingList'])->name('pending-list');

                Route::get('approval-list', [ProductController::class, 'ApprovalList'])->name('approval-list');
                Route::get('approval-products-list/{id}', [ProductController::class, 'ApprovedProductList'])->name('approved-products-list');

                Route::get('rejected-list', [ProductController::class, 'RejectedList'])->name('rejected-list');
                Route::get('rejected-products-list/{id}', [ProductController::class, 'RejectedProductList'])->name('rejected-products-list');

                Route::get('product-view/{id}', [ProductController::class, 'AllListView'])->name('all-view');
                Route::post('update-product/{id}', [ProductController::class, 'UpdateProduct'])->name('vendor.update');
            });

            Route::group(['prefix' => 'tag', 'as' => 'tag.'], function () {
                Route::get('add-new', [TagController::class, 'index'])->name('add-new');
                Route::post('store', [TagController::class, 'store'])->name('store');
                Route::get('edit/{id}', [TagController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [TagController::class, 'update'])->name('update');
                Route::delete('delete/{id}', [TagController::class, 'delete'])->name('delete');
                Route::get('status/{id}/{status}', [TagController::class, 'status'])->name('status');
            });




            Route::post('daily-needs', [ProductController::class, 'dailyNeeds'])->name('daily-needs');
            Route::get('limited-stock', [ProductController::class, 'limitedStock'])->name('limited-stock');
            Route::get('feature/{id}/{is_featured}', [ProductController::class, 'feature'])->name('feature');            


            Route::post('Product_ajax', [ProductController::class, 'ProductAjax'])->name('ProductAjax');
            Route::post('Product_data_ajax', [ProductController::class, 'ProductDataAjax'])->name('ProductDataAjax');
            Route::post('Edit_product_column', [ProductController::class, 'Edit_product_column'])->name('Edit_product_column');

            
        });

        Route::group(['prefix' => 'amc', 'as' => 'amc.'], function () 
        {
            Route::group(['prefix' => 'plan', 'as' => 'plan.'], function () 
            {
                Route::get('add-new', [AmcController::class, 'index'])->name('add-new');
                Route::post('add-new', [AmcController::class, 'Store'])->name('store');
                Route::get('list', [AmcController::class, 'List'])->name('list');
                Route::get('update/{id}', [AmcController::class, 'edit'])->name('update');
                Route::post('update/{id}', [AmcController::class, 'update']);
                Route::get('status/{id}/{status}', [AmcController::class, 'status'])->name('status');
                Route::get('priority/{id}/{number}', [AmcController::class, 'priority'])->name('priority');
                Route::delete('delete/{id}', [AmcController::class, 'delete'])->name('delete');
                Route::get('remove-image/{id}/{images}/{product?}/{name?}', [AmcController::class, 'removeImage'])->name('remove-image');
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

                // Business Setting
                Route::get('ecom-setup', [BusinessSettingsController::class, 'businessSettingsIndex'])->name('ecom-setup');
                Route::get('maintenance-mode', [BusinessSettingsController::class, 'maintenanceMode'])->name('maintenance-mode');
                Route::get('partial-payment/{status}', [BusinessSettingsController::class, 'partialPaymentStatus'])->name('partial-payment');
                Route::post('update-setup', [BusinessSettingsController::class, 'businessSetup'])->name('update-setup');

                // Refferal Income
                Route::get('referral-income-setup', [BusinessSettingsController::class, 'ReferralIncomeSetup'])->name('referral-income-setup');
                Route::post('referral-income-setup-update', [BusinessSettingsController::class, 'ReferralIncomeSetupUpdate'])->name('referral-income-setup-update');

                // Delivery Setup
                Route::get('delivery-setup', [BusinessSettingsController::class, 'deliveryIndex'])->name('delivery-setup');
                Route::post('delivery-setup-update', [BusinessSettingsController::class, 'deliverySetupUpdate'])->name('delivery-setup-update');
                Route::get('free-delivery-status/{status}', [BusinessSettingsController::class, 'freeDeliveryStatus'])->name('free-delivery-status');

                // Product Setup
                Route::get('product-setup', [BusinessSettingsController::class, 'productSetup'])->name('product-setup');
                Route::post('product-setup-update', [BusinessSettingsController::class, 'productSetupUpdate'])->name('product-setup-update');

                //firebase configuration
                Route::get('firebase-message-config', [BusinessSettingsController::class, 'firebaseMessageConfigIndex'])->name('firebase_message_config_index');
                Route::post('firebase-message-config', [BusinessSettingsController::class, 'firebaseMessageConfig'])->name('firebase_message_config');

                //Cookies Setup
                Route::get('cookies-setup', [BusinessSettingsController::class, 'cookiesSetup'])->name('cookies-setup');
                Route::get('cookies-status/{status}', [BusinessSettingsController::class, 'cookiesStatus'])->name('cookies-status');
                Route::post('cookies-setup-update', [BusinessSettingsController::class, 'cookiesSetupUpdate'])->name('cookies-setup-update');

                // Otp Setup
                Route::get('otp-setup', [BusinessSettingsController::class, 'OTPSetup'])->name('otp-setup');
                Route::post('otp-setup-update', [BusinessSettingsController::class, 'OTPSetupUpdate'])->name('otp-setup-update');

                // Order Setup
                Route::get('order-setup', [BusinessSettingsController::class, 'orderSetup'])->name('order-setup');
                Route::get('order-status/{status}', [BusinessSettingsController::class, 'orderStatus'])->name('order-status');
                Route::get('order-autoassign/{status}', [BusinessSettingsController::class, 'orderAutoAssign'])->name('order-autoassign');
                Route::post('order-setup-update', [BusinessSettingsController::class, 'orderSetupUpdate'])->name('order-setup-update');

                // Commission Setup
                Route::get('commission-setup', [BusinessSettingsController::class, 'CommissionSetup'])->name('commission-setup');
                Route::post('commission-setup-update', [BusinessSettingsController::class, 'CommissionSetupUpdate'])->name('commission-setup-update');

                // city Setup
                Route::get('city-setup', [BusinessSettingsController::class, 'CitySetup'])->name('city-setup');
                Route::post('city-setup-update', [BusinessSettingsController::class, 'CitySetupUpdate'])->name('city-setup-update');
                Route::get('city-status/{id}', [BusinessSettingsController::class, 'CityStatus'])->name('city.status');
                Route::get('city-edit/{id}', [BusinessSettingsController::class, 'CityEdit'])->name('city.edit');
                Route::post('city-update/{id}', [BusinessSettingsController::class, 'CityUpdate'])->name('city.update');
                Route::delete('city-delete/{id}', [BusinessSettingsController::class, 'CityDelete'])->name('city.delete');

                // Customer Setup
                Route::get('customer-setup', [BusinessSettingsController::class, 'customerSetup'])->name('customer-setup');
                Route::post('customer-setup-update', [BusinessSettingsController::class, 'customerSetupUpdate'])->name('customer-setup-update');

                // Timr Slot Setup
                Route::group(['prefix' => 'timeSlot', 'as' => 'timeSlot.'], function () {
                    Route::get('add-new', [TimeSlotController::class, 'index'])->name('add-new');
                    Route::post('store', [TimeSlotController::class, 'store'])->name('store');
                    Route::get('update/{id}', [TimeSlotController::class, 'edit'])->name('update');
                    Route::post('update/{id}', [TimeSlotController::class, 'update']);
                    Route::get('status/{id}/{status}', [TimeSlotController::class, 'status'])->name('status');
                    Route::delete('delete/{id}', [TimeSlotController::class, 'delete'])->name('delete');
                });

                // Timr Slot Setup
                Route::group(['prefix' => 'service/timeSlot', 'as' => 'service.timeSlot.'], function () {
                    Route::get('add-new', [TimeSlotController::class, 'ServiceIndex'])->name('add-new');
                    Route::post('store', [TimeSlotController::class, 'ServiceStore'])->name('store');
                    Route::get('update/{id}', [TimeSlotController::class, 'ServiceEdit'])->name('update');
                    Route::post('update/{id}', [TimeSlotController::class, 'ServiceUpdate']);
                    Route::get('status/{id}/{status}', [TimeSlotController::class, 'ServiceStatus'])->name('status');
                    Route::post('priority/{id}', [TimeSlotController::class, 'ServicePriority'])->name('priority');
                    Route::delete('delete/{id}', [TimeSlotController::class, 'ServiceDelete'])->name('delete');
                });


                // Route::get('currency-position/{position}', [BusinessSettingsController::class, 'currencySymbolPosition'])->name('currency-position');
                // Route::get('self-pickup/{status}', [BusinessSettingsController::class, 'selfPickupStatus'])->name('self-pickup');
                // Route::get('location-setup', [LocationSettingsController::class, 'locationIndex'])->name('location-setup');
                // Route::post('update-location', [LocationSettingsController::class, 'locationSetup'])->name('update-location');
                // Route::get('max-amount-status/{status}', [BusinessSettingsController::class, 'maximumAmountStatus'])->name('max-amount-status');
                // Route::get('dm-self-registration/{status}', [BusinessSettingsController::class, 'deliverymanSelfRegistrationStatus'])->name('dm-self-registration');
                // Route::get('guest-checkout/{status}', [BusinessSettingsController::class, 'guestCheckoutStatus'])->name('guest-checkout');
            });

            Route::group(['prefix'=>'web-app','as'=>'web-app.'], function() {
                
                // Payment Methods
                Route::get('payment-method', [BusinessSettingsController::class, 'paymentIndex'])->name('payment-method');
                Route::post('payment-method-update/{payment_method}', [BusinessSettingsController::class, 'paymentUpdate'])->name('payment-method-update');
                Route::post('payment-status-update', [BusinessSettingsController::class, 'PaymentStatusUpdate'])->name('payment-status-update');
                Route::post('payment-mode-update', [BusinessSettingsController::class, 'PaymentModeUpdate'])->name('payment-mode-update');
                Route::post('payment-config-update', [BusinessSettingsController::class, 'paymentConfigUpdate'])->name('payment-config-update');

                // Firebase Config
                Route::group(['prefix'=>'system-setup','as'=>'system-setup.'], function() {
                    // Route::get('app-setting', [BusinessSettingsController::class, 'appSettingIndex'])->name('app_setting');
                    // Route::post('app-setting', [BusinessSettingsController::class, 'appSettingUpdate']);
                   
                });

                // SMS Config
                // Route::get('sms-module', [SMSModuleController::class, 'smsIndex'])->name('sms-module');
                // Route::post('sms-module-update/{sms_module}', [SMSModuleController::class, 'smsUpdate'])->name('sms-module-update');


                // Route::get('mail-config', [BusinessSettingsController::class, 'mailIndex'])->name('mail-config');
                // Route::post('mail-config', [BusinessSettingsController::class, 'mailConfig']);
                // Route::get('mail-config/status/{status}', [BusinessSettingsController::class, 'mailConfigStatus'])->name('mail-config.status');
                // Route::post('mail-send', [BusinessSettingsController::class, 'mailSend'])->name('mail-send');

                Route::group(['prefix' => 'third-party', 'as' => 'third-party.'], function () {
    
                    //social media method
                    Route::get('social-media-login', [PageSetupController::class, 'socialMediaLogin'])->name('social-media-login');
                    Route::get('google-social-login/{status}', [BusinessSettingsController::class, 'googleSocialLogin'])->name('google-social-login');
                    Route::get('facebook-social-login/{status}', [BusinessSettingsController::class, 'facebookSocialLogin'])->name('facebook-social-login');

                    //Google Map Api 
                    Route::get('map-api-settings',[BusinessSettingsController::class, 'mapApiSetting'])->name('map-api-settings');
                    Route::post('map-api-store',[BusinessSettingsController::class, 'mapApiStore'])->name('map-api-store');

                    //firebase otp varification
                    Route::get('firebase-otp-verification', [BusinessSettingsController::class, 'firebaseOTPVerification'])->name('firebase-otp-verification');

                    //push notification
                    Route::get('fcm-index', [BusinessSettingsController::class, 'fcmIndex'])->name('fcm-index');
                    Route::post('update-fcm', [BusinessSettingsController::class, 'updateFcm'])->name('update-fcm');
                    Route::get('fcm-config', [BusinessSettingsController::class, 'fcmConfig'])->name('fcm-config');
                    Route::post('update-fcm-messages', [BusinessSettingsController::class, 'updateFcmMessages'])->name('update-fcm-messages');
                    
                    Route::get('social-media', [PageSetupController::class, 'socialMedia'])->name('social-media');
                    Route::get('fetch', [PageSetupController::class, 'fetch'])->name('fetch');
                    Route::post('social-media-store', [PageSetupController::class, 'socialMediaStore'])->name('social-media-store');
                    Route::post('social-media-edit', [PageSetupController::class, 'socialMediaEdit'])->name('social-media-edit');
                    Route::post('social-media-update', [PageSetupController::class, 'socialMediaUpdate'])->name('social-media-update');
                    Route::post('social-media-delete', [PageSetupController::class, 'socialMediaDelete'])->name('social-media-delete');
                    Route::post('social-media-status-update', [PageSetupController::class, 'socialMediaStatusUpdate'])->name('social-media-status-update');
                    
                    Route::post('update-apple-login', [BusinessSettingsController::class, 'updateAppleLogin'])->name('update-apple-login');
                    Route::get('recaptcha', [BusinessSettingsController::class, 'recaptchaIndex'])->name('recaptcha_index');
                    Route::post('recaptcha-update', [BusinessSettingsController::class, 'recaptchaUpdate'])->name('recaptcha_update');
                    Route::get('chat-index', [BusinessSettingsController::class, 'chatIndex'])->name('chat-index');
                    Route::post('update-chat', [BusinessSettingsController::class, 'updateChat'])->name('update-chat');
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
                Route::get('terms-and-conditions', [PageSetupController::class, 'termsAndConditions'])->name('terms-and-conditions');
                Route::post('terms-and-conditions', [PageSetupController::class, 'termsAndConditionsUpdate']);
                Route::post('terms-and-conditions-partner', [PageSetupController::class, 'termsAndConditionsPartnerUpdate'])->name('terms.and.conditions.partner');
                Route::post('terms-and-conditions-vendor', [PageSetupController::class, 'termsAndConditionsVendorUpdate'])->name('terms.and.conditions.vendor');

                Route::get('privacy-policy', [PageSetupController::class, 'privacyPolicy'])->name('privacy-policy');
                Route::post('privacy-policy', [PageSetupController::class, 'privacyPolicyUpdate']);
                Route::post('privacy-policy-partner', [PageSetupController::class, 'privacyPolicyPartnerUpdate'])->name('privacy-policy.partner');
                Route::post('privacy-policy-vendor', [PageSetupController::class, 'privacyPolicyVendorUpdate'])->name('privacy-policy.vendor');

                Route::get('about-us', [PageSetupController::class, 'aboutUs'])->name('about-us');
                Route::post('about-us', [PageSetupController::class, 'aboutUsUpdate']);
                Route::post('about-us-partner', [PageSetupController::class, 'aboutUsPartnerUpdate'])->name('about-us.partner');
                Route::post('about-us-vendor', [PageSetupController::class, 'aboutUsVendorUpdate'])->name('about-us.vendor');

                Route::get('faq', [PageSetupController::class, 'faq'])->name('faq');
                Route::post('faq', [PageSetupController::class, 'faqUpdate']);

                Route::get('cancellation-policy', [PageSetupController::class, 'cancellationPolicy'])->name('cancellation-policy');
                Route::post('cancellation-policy', [PageSetupController::class, 'cancellationPolicyUpdate']);
                Route::get('cancellation-policy/status/{status}', [PageSetupController::class, 'cancellationPolicyStatus'])->name('cancellation-policy.status');

                Route::get('refund-policy', [PageSetupController::class, 'refundPolicy'])->name('refund-policy');
                Route::post('refund-policy', [PageSetupController::class, 'refundPolicyUpdate']);
                Route::get('refund-policy/status/{status}', [PageSetupController::class, 'refundPolicyStatus'])->name('refund-policy.status');

                Route::get('return-policy', [PageSetupController::class, 'returnPolicy'])->name('return-policy');
                Route::post('return-policy', [PageSetupController::class, 'returnPolicyUpdate']);
                Route::get('return-policy/status/{status}', [PageSetupController::class, 'returnPolicyStatus'])->name('return-policy.status');

            });
        });
    });
});