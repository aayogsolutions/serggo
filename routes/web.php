<?php

use App\Http\Controllers\Api\user\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Artisan,Route};

Route::get('/optimize', function () {
    try {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        // Artisan::call('route:cache');
        // Artisan::call('optimize');
        echo 'Optimized';
    } catch (\Throwable $th) {
        echo 'error';
    }
});

// Payment Gateway Routes
Route::group(['prefix' => 'payment','as' => 'payment.'], function()
{
    Route::get('/gateway', [PaymentController::class,'PaymentGateway']);
    Route::post('/gateway', [PaymentController::class,'PaymentGatewayOrder'])->name('gateway');
    Route::post('/gateway/response', [PaymentController::class,'PaymentGatewayResponse'])->name('gateway.response');
    Route::get('/gateway/success', [PaymentController::class,'PaymentGatewaySuccess'])->name('gateway.success');
    Route::post('/gateway/failed', [PaymentController::class,'PaymentGatewayFailed'])->name('gateway.failed');
});

require "admin.php";
 
// Route::get('/', function(){
//     return redirect(route('admin.login'));
// });

Route::get('/ApiLogin', function(){

    return response()->json([
        'status' => false,
        'message' => 'Login Required',
        'data' => [],
    ],401);
})->name('login');




