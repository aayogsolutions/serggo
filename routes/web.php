<?php

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

require "admin.php";
require "branch.php";
 
Route::get('/', function(){
    return redirect(route('admin.login'));
});

Route::get('/ApiLogin', function(){

    return response()->json([
        'status' => false,
        'message' => 'Login Required',
        'data' => [],
    ],204);
})->name('login');



Route::post('/', function(Request $request){
    $name = Helpers_upload('admin/', 'png', $request->file('image'));
    dd($name);
})->name('signup');

Route::get('/TC', function(Request $request){
    return view('T&C');
});

Route::get('/PP', function(Request $request){
    return view('PP');
});




