<?php

namespace App\Http\Controllers\Api\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;

class ApiAuthController extends Controller
{
    public function OTPRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $usercheck = User::where('number', $request->number)->exists();


        if ($usercheck) {
            // user exist;
            $otp = rand(1000, 9999);
            $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');

            $authuser = User::where('number', $request->number)->first();

            $user = User::find($authuser->id);
            $user->otp = $otp;
            $user->otp_expired_at = $expired_at;
            $user->number_verify = 0;
            $user->save();

            return response()->json([
                'status' => true,
                'Registration' => $user->registration,
                'message' => 'Otp Sended',
                'data' => [
                    'number' => $request->number,
                    'otp' => $otp,
                ],
            ], 202);
        } else {
            // user not exist;
            $otp = rand(1000, 9999);
            $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');

            $user = new User();
            $user->number = $request->number;
            $user->otp = $otp;
            $user->otp_expired_at = $expired_at;
            $user->number_verify = 0;
            $user->registration = 0;
            $user->save();

            return response()->json([
                'status' => true,
                'Registration' => $user->registration,
                'message' => 'Otp Sended',
                'data' => [
                    'number' => $request->number,
                    'otp' => $otp,
                ],
            ], 201);
        }
    }

    public function resendOtp($number)
    {
        if (is_numeric($number)) {
            $usercheck = User::where('number', $number)->exists();

            if ($usercheck) {
                $otp = rand(1000, 9999);
                $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');

                $authuser = User::where('number', $number)->first();

                $user = User::find($authuser->id);
                $user->otp = $otp;
                $user->otp_expired_at = $expired_at;
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Otp Resend Successfully',
                    'data' => [
                        'number' => $number,
                        'otp' => $otp,
                    ],
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'Phone Number not exist',
                'data' => [],
            ], 404);
        }
        return response()->json([
            'status' => false,
            'message' => 'Invalid Phone Number',
            'data' => [],
        ], 406);
    }

    public function OTPSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric|digits:10',
            'otp' => 'required|numeric|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $usercheck = User::where('number', $request->number)->exists();

        if ($usercheck) {
            // user exist;
            $user = User::where('number', $request->number)->first();

            if ($user->otp_expired_at->diff(Carbon::now()->format('Y/m/d H:i:s'))->format('%R') == '-') {
                if ($request->otp == $user->otp) {
                    if ($user->registration == 0) {
                        $user->number_verify = 1;
                        $user->save();
                        return response()->json([
                            'status' => true,
                            'Registration' => $user->registration,
                            'message' => 'Registration is pending',
                            'data' => [
                                'user' => $user
                            ],
                        ],203);
                    } else {
                        if ($user->number == $request->number && $user->is_block == 0) {
                            $user->number_verify = 1;
                            $user->save();
                            $token = $user->createToken(($user->name) != null ? $user->name : $user->number)->plainTextToken;

                            return response()->json([
                                'status' => true,
                                'message' => 'login Successfully',
                                'data' => [
                                    'token' => $token,
                                    'user' => $user
                                ],
                            ], 202);
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'User is Blocked by Admin',
                                'data' => [],
                            ], 401);
                        }
                    }

                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Otp Is inCorrect',
                        'data' => [],
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Otp Is Expired',
                    'data' => [],
                ], 408);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Phone Number',
                'data' => [],
            ], 404);
        }
    }

    public function registeruser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'number' => 'required|numeric|digits:10',
            'referred_by' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            
            if(User::where('number', $request->number)->exists()){
                $user = User::where('number', $request->number)->first();

                $user->name = $request->name;
                $user->email = $request->email;

                if (!is_null($request->referred_by)) {
                    $referred = User::where('referral_code', $request->referred_by)->first();
                    $user->referred_by = $referred->id;
                }
                $user->referral_code = Helpers_generate_referer_code();
                $user->registration = 1;
                $user->save();

                if($user->number_verify == 0)
                {
                    $otp = rand(1000, 9999);
                    $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');

                    $user = User::where('number', $request->number)->first();
                    $user->otp = $otp;
                    $user->otp_expired_at = $expired_at;
                    $user->save();

                    return response()->json([
                        'status' => true,
                        'Number verification' => $user->number_verify,
                        'message' => 'Otp Sended',
                        'data' => [
                            'number' => $request->number,
                            'otp' => $otp,
                        ],
                    ], 203);
                }else{
                    $token = $user->createToken($user->name)->plainTextToken;

                    return response()->json([
                        'status' => true,
                        'message' => 'login Successfully',
                        'data' => [
                            'token' => $token,
                            'user' => $user
                        ],
                    ], 202);
                }
                
            }elseif (User::where('provider_id', $request->id)->exists()) {
                $otp = rand(1000, 9999);
                $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');
                
                $user = User::where('provider_id', $request->id)->first();

                $user->name = $request->name;
                $user->email = $request->email;
                $user->number = $request->number;

                if (!is_null($request->referred_by)) {
                    $referred = User::where('referral_code', $request->referred_by)->first();
                    $user->referred_by = $referred->id;
                }
                $user->referral_code = Helpers_generate_referer_code();
                $user->otp = $otp;
                $user->otp_expired_at = $expired_at;
                $user->registration = 1;
                $user->save();
                
                return response()->json([
                    'status' => true,
                    'Number verification' => $user->number_verify,
                    'message' => 'Otp Sended',
                    'data' => [
                        'number' => $request->number,
                        'otp' => $otp,
                    ],
                ], 203);
            }
            
        } catch (\Throwable $th) {
            $error = $th->getMessage();
            return response()->json([
                'status' => false,
                'message' => $error,
                'data' => [],
            ], 403);
        }
    }


    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function SignupWithSocial(Request $request, $provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated))
        {
            return $validated;
        }

        try {
            if ($provider == 'google') {
                
                $userdata = User::where('email', $request->email)->first();
                if (!is_null($userdata) && $userdata->registration == 1 && $userdata->number_verify == 1) {
                    $token = $userdata->createToken(($userdata->name) != null ? $userdata->name : $userdata->email)->plainTextToken;
                    return response()->json([
                        'status' => true,
                        'message' => 'logged in',
                        'data' => [
                            'token' => $token,
                            'user' => $userdata
                        ],
                    ], 202);
                } elseif (!is_null($userdata) && $userdata->registration == 0 && $userdata->number_verify == 0) {
                    return response()->json([
                        'status' => true,
                        'Registration' => $userdata->registration,
                        'Number verification' => $userdata->number_verify,
                        'message' => 'Number verification or registration required',
                        'data' => [
                            'user' => $userdata
                        ],
                    ], 203);
                } elseif (!is_null($userdata) && $userdata->registration == 1 && $userdata->number_verify == 0) {

                    $otp = rand(1000, 9999);
                    $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');
                    
                    $userdata->otp = $otp;
                    $userdata->otp_expired_at = $expired_at;
                    $userdata->save();

                    return response()->json([
                        'status' => true,
                        'Number verification' => $userdata->number_verify,
                        'message' => 'Number verification required',
                        'data' => [
                            'number' => $userdata->number,
                            'otp' => $otp,
                            'user' => $userdata
                        ],
                    ], 203);
                } elseif (!is_null($userdata) && $userdata->registration == 0 && $userdata->number_verify == 1) {
                    return response()->json([
                        'status' => true,
                        'Registration' => $userdata->registration,
                        'message' => 'Registration required',
                        'data' => [
                            'user' => $userdata
                        ],
                    ], 203);
                } else {
                    $user = new User();
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->number = null;
                    $user->image = 'default.png';
                    $user->provider_id = $request->id;
                    $user->provider_name = $provider;
                    $user->is_block = 0;
                    $user->registration = 0;
                    $user->number_verify = 0;
                    $user->referral_code = Helpers_generate_referer_code();
                    $user->email_verified_at = now();
                    $user->save();

                    return response()->json([
                        'status' => true,
                        'Registration' => $user->registration,
                        'Number verification' => $user->number_verify,
                        'message' => 'New user Created',
                        'data' => [
                            'user' => $user
                        ],
                    ], 203);
                }
            } elseif ($provider == 'facebook') {

                $userdata = User::where('email', $request->email)->first();
                if (!is_null($userdata) && $userdata->registration == 1 && $userdata->number_verify == 1) {
                    $token = $userdata->createToken(($userdata->name) != null ? $userdata->name : $userdata->email)->plainTextToken;
                    return response()->json([
                        'status' => true,
                        'message' => 'logged in',
                        'data' => [
                            'token' => $token,
                            'user' => $userdata
                        ],
                    ], 202);
                } elseif (!is_null($userdata) && $userdata->registration == 0 && $userdata->number_verify == 0) {
                    return response()->json([
                        'status' => true,
                        'Registration' => $userdata->registration,
                        'Number verification' => $userdata->number_verify,
                        'message' => 'Number verification or registration required',
                        'data' => [
                            'user' => $userdata
                        ],
                    ], 203);
                } elseif (!is_null($userdata) && $userdata->registration == 1 && $userdata->number_verify == 0) {

                    $otp = rand(1000, 9999);
                    $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');
                    
                    $userdata->otp = $otp;
                    $userdata->otp_expired_at = $expired_at;
                    $userdata->save();

                    return response()->json([
                        'status' => true,
                        'Number verification' => $userdata->number_verify,
                        'message' => 'Number verification required',
                        'data' => [
                            'number' => $userdata->number,
                            'otp' => $otp,
                            'user' => $userdata
                        ],
                    ], 203);
                } elseif (!is_null($userdata) && $userdata->registration == 0 && $userdata->number_verify == 1) {
                    return response()->json([
                        'status' => true,
                        'Registration' => $userdata->registration,
                        'message' => 'Registration required',
                        'data' => [
                            'user' => $userdata
                        ],
                    ], 203);
                } else {
                    $user = new User();
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->number = null;
                    $user->image = 'default.png';
                    $user->provider_id = $request->id;
                    $user->provider_name = $provider;
                    $user->is_block = 0;
                    $user->registration = 0;
                    $user->number_verify = 0;
                    $user->referral_code = Helpers_generate_referer_code();
                    $user->email_verified_at = now();
                    $user->save();

                    return response()->json([
                        'status' => true,
                        'Registration' => $user->registration,
                        'Number verification' => $user->number_verify,
                        'message' => 'New user Created',
                        'data' => [
                            'user' => $user
                        ],
                    ], 201);
                }
            } elseif ($provider == 'apple') {

                // Find or create user
                $userdata = User::where('provider_id', $request->id)->first();
                if (!is_null($userdata) && $userdata->registration == 1 && $userdata->number_verify == 1) {
                    $token = $userdata->createToken(($userdata->name) != null ? $userdata->name : $userdata->email)->plainTextToken;
                    return response()->json([
                        'status' => true,
                        'message' => 'logged in',
                        'data' => [
                            'token' => $token,
                            'user' => $userdata
                        ],
                    ], 202);
                } elseif (!is_null($userdata) && $userdata->registration == 0 && $userdata->number_verify == 0) {
                    return response()->json([
                        'status' => true,
                        'Registration' => $userdata->registration,
                        'Number verification' => $userdata->number_verify,
                        'message' => 'Number verification or registration required',
                        'data' => [
                            'user' => $userdata
                        ],
                    ], 203);
                } elseif (!is_null($userdata) && $userdata->registration == 1 && $userdata->number_verify == 0) {

                    $otp = rand(1000, 9999);
                    $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');
                    
                    $userdata->otp = $otp;
                    $userdata->otp_expired_at = $expired_at;
                    $userdata->save();

                    return response()->json([
                        'status' => true,
                        'Number verification' => $userdata->number_verify,
                        'message' => 'Number verification required',
                        'data' => [
                            'number' => $userdata->number,
                            'otp' => $otp,
                            'user' => $userdata
                        ],
                    ], 203);
                } elseif (!is_null($userdata) && $userdata->registration == 0 && $userdata->number_verify == 1) {
                    return response()->json([
                        'status' => true,
                        'Registration' => $userdata->registration,
                        'message' => 'Registration required',
                        'data' => [
                            'user' => $userdata
                        ],
                    ], 203);
                } else {
                    $user = new User();
                    $user->provider_id = $request->id;
                    $user->provider_name = $provider;
                    $user->image = 'default.png';
                    $user->is_block = 0;
                    $user->registration = 0;
                    $user->number_verify = 0;
                    $user->referral_code = Helpers_generate_referer_code();
                    $user->save();

                    return response()->json([
                        'status' => true,
                        'Registration' => $user->registration,
                        'Number verification' => $user->number_verify,
                        'message' => 'New user Created',
                        'data' => [
                            'user' => $user
                        ],
                    ], 201);
                }
            }
        } catch (ClientException $exception) {
            return response()->json(
                ['error' => 'Invalid credentials provided.'
            ], 422);
        }
    }

    /**
     * @param $provider
     * @return JsonResponse
     */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'apple', 'google'])) {
            return response()->json(
                ['error' => 'Please login using facebook, apple or google'
            ], 422);
        }
    }
}
