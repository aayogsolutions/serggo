<?php

namespace App\Http\Controllers\Api\partner;

use App\Http\Controllers\Controller;
use App\Models\{
    PartnerCategory,
    Vendor
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private PartnerCategory $partnercategory,
        private Vendor $vendor,
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function LogIn(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
            'number' => 'required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            if(Auth::guard('vendors')->attempt([
                'number' => $request->number,
                'password' => $request->password,
                'role' => '1'
            ]))
            {
                $vendor = $this->vendor->find(Auth::guard('vendors')->user()->id);
                
                if($vendor->number_verfiy == 1)
                {
                    $otp = rand(1000, 9999);
                    $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');

                    $vendor->otp = $otp;
                    $vendor->otp_expired_at = $expired_at;
                    $vendor->save();
                    
                    return response()->json([
                        'status' => true,
                        'required' => 'number_verification',
                        'message' => 'Otp Send Successfully',
                        'is_verify' => $vendor->is_verify,
                        'data' => [
                            'otp' => $otp,
                            'number' => $vendor->number
                        ]
                    ],200);
                }
                elseif($vendor->is_verify == 0) 
                {
                    $vendor->fmc_token = $request->fmc_token;
                    $vendor->save();

                    return response()->json([
                        'status' => true,
                        'required' => 'kyc_verification',
                        'message' => 'KYC Pending',
                        'data' => [
                            'user' => $vendor,
                        ]
                    ],200);
                }
                else{
                    $vendor->fmc_token = $request->fmc_token;
                    $vendor->save();
                    $token = $vendor->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'status' => true,
                        'required' => false,
                        'message' => 'Logged in successfully',
                        'data' => [
                            'token' => $token,
                            'user' => $vendor,
                        ]
                    ],200);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Number Or Password Not Match',
                    'data' => []
                ],401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Enter Valid Information'.$th->getMessage(),
                'data' => []
            ],401);
        }
    }

    /**
     * 
     * @return JsonResponse
     */
    public function Category() : JsonResponse
    {
        try {
            $category = $this->partnercategory->where('status' , 0)->orderBy('priority' , 'ASC')->get();
        } catch (\Throwable $th) {
            $category = [];
        }

        return response()->json([
            'status' => true,
            'message' => 'Category Info',
            'data' => [
                'delivery' => [
                    'Small Delivery',
                    'Large Delivery'
                ],
                'category' => $category
            ]
        ],200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function SignUp(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',  
            'password' => 'required|confirmed|min:8',
            'number' => 'required|numeric|digits:10',
            'dob' => 'required|date',
            'delivery' => 'required|in:small,large',
            'category' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        
        try {
            if(!$this->vendor->where('number' , $request->number)->exists()) {

                if (!$this->vendor->where('email' , $request->email)->exists()) {

                    $otp = rand(1000, 9999);
                    $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');
                    
                    $vendor = $this->vendor;
                    $vendor->name = $request->name;
                    $vendor->email = $request->email;
                    $vendor->password = $request->password;
                    $vendor->number = $request->number;
                    $vendor->otp = $otp;
                    $vendor->otp_expired_at = $expired_at;
                    $vendor->dob = $request->dob;
                    $vendor->delivery_type = $request->delivery;
                    $vendor->category = json_encode($this->partnercategory->WhereIn('id' , $request->category)->pluck('name')->toArray());
                    $vendor->registration = 0;
                    $vendor->role = '1';
                    $vendor->save();

                    return response()->json([
                        'status' => true,
                        'required' => 'number_verification',
                        'message' => 'Number Verification Required',
                        'data' => [
                            'otp' => $otp,
                            'number' => $request->number
                        ]
                    ],200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Email Already Exists',
                        'data' => [],
                    ],401);
                }
            }else{
                $vendor = $this->vendor->where('number' , $request->number)->first();

                if ($vendor->number_verfiy == 1) {

                    $otp = rand(1000, 9999);
                    $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');

                    $vendor->otp = $otp;
                    $vendor->otp_expired_at = $expired_at;
                    $vendor->save();

                    return response()->json([
                        'status' => true,
                        'required' => 'number_verification',
                        'message' => 'Otp Sended',
                        'data' => [
                            'otp' => $otp,
                            'number' => $request->number
                        ]
                    ],200);
                }else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Phone Already Exists',
                        'data' => [],
                    ],401);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Enter Valid Informationb '.$th->getMessage(),
                'data' => [],
            ],401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function OtpSubmit(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric|digits:10',
            'otp' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $vendor = $this->vendor->where('number' , $request->number)->first();
        
            if (Carbon::parse($vendor->otp_expired_at)->format('Y/m/d H:i:s') >= Carbon::now()->format('Y/m/d H:i:s')) {
                if ($request->otp == $vendor->otp) {
                    $vendor->number_verfiy = 0;
                    $vendor->save();

                    return response()->json([
                        'status' => true,
                        'required' => 'kyc_verification',
                        'message' => 'KYC Pending',
                        'data' => [],
                    ],200);
                }else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Otp Not Matched',
                        'data' => [],
                    ],401);
                }
            }else {
                return response()->json([
                    'status' => false,
                    'message' => 'Otp Expired',
                    'data' => [],
                ],401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Enter Valid Information',
                'data' => [],
            ],401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ResendOTP(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        
        try {
            if ($this->vendor->where('number' , $request->number)->exists()) {
                $vendor = $this->vendor->where('number' , $request->number)->first();

                $otp = rand(1000, 9999);
                $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');

                $vendor->otp = $otp;
                $vendor->otp_expired_at = $expired_at;
                $vendor->save();

                return response()->json([
                    'status' => true,
                    'required' => 'number_verification',
                    'message' => 'Otp Sended',
                    'data' => [
                        'otp' => $otp,
                        'number' => $request->number
                    ]
                ],200);
            }else {
                return response()->json([
                    'status' => false,
                    'message' => 'Number Not Exists',
                    'data' => [],
                ],401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Enter Valid Information'.$th->getMessage(),
                'data' => [],
            ],401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function KYCSubmit(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'aadhar_no' => 'required|numeric|digits:12',
            'number' => 'required|numeric|digits:10',
            'bank_name' => 'required|string|max:255',
            'bank_holder_name' => 'required|string|max:255',
            'bank_ifsc' => 'required|string|max:255',
            'bank_account_no' => 'required|max:255',
            'aadhar_front' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'aadhar_back' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            if($vendor = $this->vendor->where('number' , $request->number)->exists())
            {
                $vendor = $this->vendor->where('number' , $request->number)->first();
                
                $vendor->aadhar_no = $request->aadhar_no;

                $vendor->aadhar_document = json_encode([
                    'aadhar_front' => Helpers_upload('Images/partners/kyc/', $request->file('aadhar_front')->getClientOriginalExtension(), $request->file('aadhar_front')),
                    'aadhar_back' => Helpers_upload('Images/partners/kyc/', $request->file('aadhar_back')->getClientOriginalExtension(), $request->file('aadhar_back'))
                ]);

                $vendor->image = Helpers_upload('Images/partners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                
                $vendor->is_verify = 1;
                $vendor->fmc_token = $request->fmc_token;
                $vendor->bank_name = $request->bank_name;
                $vendor->bank_holder_name = $request->bank_holder_name;
                $vendor->bank_ifsc = $request->bank_ifsc;
                $vendor->bank_account_no = $request->bank_account_no;
                $vendor->save();

                $token = $vendor->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'required' => false,
                    'message' => 'Logged in successfully',
                    'data' => [
                        'token' => $token,
                        'user' => $vendor
                    ],
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Number Not Exists',
                    'data' => [],
                ],401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Enter Valid Information',
                'data' => [],
            ],401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ForgetPasswordNumber(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {

            $vendor = $this->vendor->where('number' , $request->number)->first();

            if ($vendor) {
                $vendor->otp = rand(1000, 9999);
                $vendor->otp_expired_at = Carbon::now()->addMinutes(10);
                $vendor->save();

                return response()->json([
                    'status' => true,
                    'required' => 'number_verification',
                    'message' => 'Otp Send Successfully',
                    'data' => [
                        'otp' => $vendor->otp,
                        'number' => $request->number
                    ]
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Number Not Found',
                    'data' => [],
                ],401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Enter Valid Information',
                'data' => [],
            ],401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ForgetPasswordOTP(Request $request) : JsonResponse
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

        try {

            $vendor = $this->vendor->where('number' , $request->number)->first();

            if ($vendor) {
                if (Carbon::parse($vendor->otp_expired_at)->format('Y/m/d H:i:s') >= Carbon::now()->format('Y/m/d H:i:s')) {
                    if ($request->otp == $vendor->otp) {
                        return response()->json([
                            'status' => true,
                            'required' => 'password change',
                            'message' => 'Otp Matched',
                            'data' => [
                                'number' => $request->number
                            ]
                        ],200);
                    }else{
                        return response()->json([
                            'status' => false,
                            'message' => 'Otp Not Matched',
                            'data' => [],
                        ],401);
                    }
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Otp Expired',
                        'data' => [],
                    ],401);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Enter Valid Information',
                'data' => [],
            ],401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ForgetPasswordSubmit(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric|digits:10',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }        

        try {            

            $vendor = $this->vendor->where('number' , $request->number)->first();

            if ($vendor) {
                $vendor->password = $request->password;
                $vendor->fmc_token = $request->fmc_token;
                $vendor->save();

                $token = $vendor->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'required' => false,
                    'message' => 'Password Changed Successfully',
                    'data' => [
                        'token' => $token,
                        'user' => $vendor
                    ],
                ],200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Enter Valid Information',
                'data' => [],
            ],401);
        }
    }
}
