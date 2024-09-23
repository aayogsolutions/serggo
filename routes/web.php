<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Artisan,Route,Auth};
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;
use App\Models\User;

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

Route::get('/', function(){
    return view('welcome');
});

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




