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

Route::get('/{platform}', function(Request $request, $platform){
    return Socialite::driver($platform)->redirect();
})->name('socialSignUp');

Route::get('/response/{provider}', function(Request $request,$provider)
{
    // $validated = $this->validateProvider($provider);
    // if (!is_null($validated)) {
    //     return $validated;
    // }
    try {

        $user = Socialite::driver($provider)->user();
        dd($user);
        if($provider == 'google')
        {
            
            $userdata = User::where('email',$user->email)->first();
            if(!is_null($userdata))
            {
                $token = $userdata->createToken(($userdata->f_name) != null ? $userdata->f_name : 'my_token')->accessToken;
                return response()->json([
                    'message' => 'logged in',
                    'data' => [
                        'token' => $token
                    ],
                ],202);
            }else{
                $user = new User();
                $user->f_name = $user->user->given_name;
                if(!is_null($user->user->family_name))
                {
                    $user->l_name = $user->user->family_name;
                }
                $user->email = $user->email;
                $user->number = null;
                $user->image = 'default.png';
                $user->is_block = 0;
                $user->referral_code = Helpers_generate_referer_code();
                $user->email_verified_at = now();
                $user->save();

                return response()->json([
                    'message' => 'New user Created',
                    'required' => 'number login',
                    'data' =>[
                        'email' => $user->email,
                        'first name' => $user->f_name,
                        'last name' => $user->l_name,
                    ],
                ],202);
            }
        }elseif ($provider == 'facebook'){
            
        }

    } catch (ClientException $exception) {
        return response()->json(['error' => 'Invalid credentials provided.'], 422);
    }

    // $client = new Client();
    // $token = $request['token'];
    // $email = $request['email'];
    // $uniqueId = $request['unique_id'];

    // try {
    //     if ($request['medium'] == 'google') {
    //         $res = $client->request('GET', 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $token);
    //         $data = json_decode($res->getBody()->getContents(), true);
    //     } elseif ($request['medium'] == 'facebook') {
    //         $res = $client->request('GET', 'https://graph.facebook.com/' . $uniqueId . '?access_token=' . $token . '&&fields=name,email');
    //         $data = json_decode($res->getBody()->getContents(), true);
    //     }elseif ($request['medium'] == 'apple') {
    //         $appleLogin = Helpers::get_business_settings('apple_login');
    //         $teamId = $appleLogin['team_id'];
    //         $keyId = $appleLogin['key_id'];
    //         $sub = $appleLogin['client_id'];
    //         $aud = 'https://appleid.apple.com';
    //         $iat = strtotime('now');
    //         $exp = strtotime('+60days');
    //         $keyContent = file_get_contents('storage/app/public/apple-login/'.$appleLogin['service_file']);

    //         $token = JWT::encode([
    //             'iss' => $teamId,
    //             'iat' => $iat,
    //             'exp' => $exp,
    //             'aud' => $aud,
    //             'sub' => $sub,
    //         ], $keyContent, 'ES256', $keyId);

    //         $redirectUri = $appleLogin['redirect_url']??'www.example.com/apple-callback';

    //         $res = Http::asForm()->post('https://appleid.apple.com/auth/token', [
    //             'grant_type' => 'authorization_code',
    //             'code' => $uniqueId,
    //             'redirect_uri' => $redirectUri,
    //             'client_id' => $sub,
    //             'client_secret' => $token,
    //         ]);

    //         $claims = explode('.', $res['id_token'])[1];
    //         $data = json_decode(base64_decode($claims),true);
    //     }
    // } catch (\Exception $exception) {
    //     $errors = [];
    //     $errors[] = ['code' => 'auth-001', 'message' => 'Invalid Token'];
    //     return response()->json([
    //         'errors' => $errors
    //     ], 401);
    // }
});




