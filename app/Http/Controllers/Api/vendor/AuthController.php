<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use App\Models\{
    Vendor,
    VendorCategory
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private VendorCategory $vendorcategory,
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
            if(Vendor::where('number' , $request->number)->exists())
            {
                $vendor = Vendor::where('number' , $request->number)->first();
                if($vendor->number_verfiy != 0)
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
                        'data' => [
                            'otp' => $otp,
                            'number' => $vendor->number
                        ]
                    ],200);
                }
                if($vendor->registration != 0)
                {
                    return response()->json([
                        'status' => true,
                        'required' => 'business_verification',
                        'message' => 'Registration Required',
                        'data' => $vendor
                    ],200);
                }
                if($vendor->is_verify == 0)
                {
                    return response()->json([
                        'status' => true,
                        'required' => 'kyc_verification',
                        'message' => 'KYC Required',
                        'data' => $vendor
                    ],200);
                }

                if(Auth::guard('vendors')->attempt([
                    'number' => $request->number,
                    'password' => $request->password,
                    'role' => '0'
                ]))
                {
                    $vendor->fmc_token = $request->fmc_token;
                    $vendor->save();
                    $token = $vendor->createToken('auth_token')->plainTextToken;
    
                    return response()->json([
                        'status' => true,
                        'required' => false,
                        'is_verify' => $vendor->is_verify,
                        'message' => 'Logged in successfully',
                        'data' => [
                            'token' => $token,
                            'user' => $vendor,
                        ]
                    ],200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Number Or Password Not Match',
                        'data' => []
                    ],401);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'User Not Found, Create Your New Account',
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
            $category = $this->vendorcategory->where('status' , 0)->orderBy('priority' , 'ASC')->get();
        } catch (\Throwable $th) {
            $category = [];
        }

        return response()->json([
            'status' => true,
            'message' => 'Category Info',
            'data' => [
                'category' => $category
            ]
        ],200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function SignUpPersonal(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',  
            'password' => 'required|confirmed|min:8',
            'number' => 'required|numeric|digits:10',
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
                    $vendor->category = json_encode($this->vendorcategory->WhereIn('id' , $request->category)->pluck('title')->toArray());
                    $vendor->role = '0';
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
                'message' => 'Enter Valid Information'.$th->getMessage(),
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
                        'required' => 'business_verification',
                        'message' => 'Number Verified',
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
                'message' => 'Enter Valid Information'.$th->getMessage(),
                'data' => [],
            ],401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function SignUpBusiness(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required',
            'gst_no' => 'required',
            'address' => 'required',
            'number' => 'required|numeric|digits:10',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'delivery' => 'required|numeric',
            'working_days' => 'required',
            'open_time' => 'required',
            'close_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        
        try {
            if($this->vendor->where('number' , $request->number)->exists()) {

                $vendor = $this->vendor->where('number' , $request->number)->first();

                $vendor->business_name = $request->business_name;
                $vendor->gst_no = $request->gst_no;
                $vendor->address = $request->address;
                $vendor->longitude = $request->longitude;
                $vendor->latitude = $request->latitude;
                $vendor->working_days = $request->working_days;
                $vendor->open_time = $request->open_time;
                $vendor->close_time = $request->close_time;
                $vendor->delivery_choice = $request->delivery;
                $vendor->registration = 0;
                $vendor->save();

                return response()->json([
                    'status' => true,
                    'required' => 'kyc_verification',
                    'message' => 'Business Verified',
                    'data' => []
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Phone Not Exists',
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
            'aadhar_front' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'aadhar_back' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'gst_document' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'bank_name' => 'required|string|max:255',
            'bank_holder_name' => 'required|string|max:255',
            'bank_ifsc' => 'required|string|max:255',
            'bank_account_no' => 'required|max:255',
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
                    'aadhar_front' => Helpers_upload('Images/vendor/kyc/', $request->aadhar_front->extension(), $request->aadhar_front),
                    'aadhar_back' => Helpers_upload('Images/vendor/kyc/', $request->aadhar_back->extension(), $request->aadhar_back),
                    'gst_document' => Helpers_upload('Images/vendor/kyc/', $request->gst_document->extension(), $request->gst_document),
                ]);
    
                $vendor->image = Helpers_upload('Images/vendor/', $request->image->extension(), $request->image);
                
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
                    'is_verify' => $vendor->is_verify,
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
                'message' => 'Enter Valid Information'.$th->getMessage(),
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
                'message' => 'Enter Valid Information'.$th->getMessage(),
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
                'message' => 'Enter Valid Information'.$th->getMessage(),
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
                'message' => 'Enter Valid Information'.$th->getMessage(),
                'data' => [],
            ],401);
        }
    }
}
