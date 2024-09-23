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
            $user->save();

            return response()->json([
                'Registration' => 1,
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
            $user->save();

            return response()->json([
                'status' => true,
                'Registration' => 0,
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
            'Registration' => 'required|boolean',
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
                    if ($request->Registration == 0) {
                        return response()->json([
                            'message' => 'Registration is pending',
                            'data' => [
                                'user' => $user
                            ],
                        ], 203);
                    } else {
                        if ($user->number == $request->number && $user->is_block == 0) {
                            $token = $user->createToken(($user->name) != null ? $user->name : $user->number)->plainTextToken;

                            return response()->json([
                                'message' => 'login Successfully',
                                'data' => [
                                    'token' => $token
                                ],
                            ], 200);
                        } else {
                            return response()->json([
                                'message' => 'User is Blocked by Admin',
                                'data' => [],
                            ], 401);
                        }
                    }
                } else {
                    return response()->json([
                        'message' => 'Otp Is inCorrect',
                        'data' => [],
                    ], 401);
                }
            } else {
                return response()->json([
                    'message' => 'Otp Is Expired',
                    'data' => [],
                ], 408);
            }
        } else {
            return response()->json([
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
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
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

                if (!is_null($request->image)) {
                    $imageName = $request->name . time() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move(public_path('Images/UserProfile'), $imageName);
                    $user->image = $imageName;
                }

                if (!is_null($request->referred_by)) {
                    $referred = User::where('referral_code', $request->referred_by)->first();
                    $user->referred_by = $referred->id;
                }
                $user->referral_code = Helpers_generate_referer_code();
                $user->save();

                $token = $user->createToken($user->name)->plainTextToken;

                return response()->json([
                    'message' => 'login Successfully',
                    'data' => [
                        'token' => $token
                    ],
                ], 201);
            }elseif (User::where('provider_id', $request->id)->exists()) {
                $otp = rand(1000, 9999);
                $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');
                
                $user = User::where('provider_id', $request->id)->first();

                $user->name = $request->name;
                $user->email = $request->email;
                $user->number = $request->number;

                if (!is_null($request->image)) {
                    $imageName = $request->name . time() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move(public_path('Images/UserProfile'), $imageName);
                    $user->image = $imageName;
                }

                if (!is_null($request->referred_by)) {
                    $referred = User::where('referral_code', $request->referred_by)->first();
                    $user->referred_by = $referred->id;
                }
                $user->referral_code = Helpers_generate_referer_code();
                $user->otp = $otp;
                $user->otp_expired_at = $expired_at;
                $user->save();

                $token = $user->createToken($user->name)->plainTextToken;

                return response()->json([
                    'Registration' => 1,
                    'message' => 'Otp Sended',
                    'data' => [
                        'number' => $request->number,
                    ],
                ], 202);
            }
            
        } catch (\Throwable $th) {
            $error = $th->getMessage();
            return response()->json([
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
                if (!is_null($userdata)) {
                    $token = $userdata->createToken(($userdata->name) != null ? $userdata->name : $userdata->email)->plainTextToken;
                    return response()->json([
                        'message' => 'logged in',
                        'data' => [
                            'token' => $token
                        ],
                    ], 202);
                } else {
                    $user = new User();
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->number = null;
                    $user->image = 'default.png';
                    $user->provider_id = $request->id;
                    $user->provider_name = $provider;
                    $user->is_block = 0;
                    $user->referral_code = Helpers_generate_referer_code();
                    $user->email_verified_at = now();
                    $user->save();

                    return response()->json([
                        'message' => 'New user Created',
                        'required' => 'number verification',
                        'data' => [
                            'user' => $user
                        ],
                    ], 202);
                }
            } elseif ($provider == 'facebook') {

                $userdata = User::where('email', $request->email)->first();
                if (!is_null($userdata)) {
                    $token = $userdata->createToken(($userdata->name) != null ? $userdata->name : $userdata->email)->plainTextToken;
                    return response()->json([
                        'message' => 'logged in',
                        'data' => [
                            'token' => $token
                        ],
                    ], 202);
                } else {
                    $user = new User();
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->number = null;
                    $user->image = 'default.png';
                    $user->provider_id = $request->id;
                    $user->provider_name = $provider;
                    $user->is_block = 0;
                    $user->referral_code = Helpers_generate_referer_code();
                    $user->email_verified_at = now();
                    $user->save();

                    return response()->json([
                        'message' => 'New user Created',
                        'required' => 'number verification',
                        'data' => [
                            'user' => $user
                        ],
                    ], 202);
                }
            } elseif ($provider == 'apple') {

                // Find or create user
                $userdata = User::where('provider_id', $request->id)->first();
                if (!is_null($userdata)) {
                    $token = $userdata->createToken(($userdata->name) != null ? $userdata->name : $userdata->email)->plainTextToken;
                    return response()->json([
                        'message' => 'logged in',
                        'data' => [
                            'token' => $token
                        ],
                    ], 202);
                } else {
                    $user = new User();
                    $user->provider_id = $request->id;
                    $user->provider_name = $provider;
                    $user->image = 'default.png';
                    $user->is_block = 0;
                    $user->referral_code = Helpers_generate_referer_code();
                    $user->save();

                    return response()->json([
                        'message' => 'New user Created',
                        'required' => 'number verification',
                        'data' => [
                            'user' => $user
                        ],
                    ], 202);
                }
            }
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }
    }

    /**
     * @param $provider
     * @return JsonResponse
     */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'apple', 'google'])) {
            return response()->json(['error' => 'Please login using facebook, apple or google'], 422);
        }
    }
}
