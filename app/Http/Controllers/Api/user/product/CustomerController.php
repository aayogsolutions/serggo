<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use App\Models\BulkEnquriey;
use App\Models\BusinessSetting;
use App\Models\CouponApplied;
use App\Models\Coupons;
use App\Models\Notifications;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTranscation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function __construct(

        private User $user,
        private WalletTranscation $wallettranscation,
    ){}

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function Profile(Request $request) : JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json([
                'status' => true,
                'message' => 'User Details',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
                'data' => []
            ], 406);
        }
    }

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function ProfileSubmit(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'number' => 'required|numeric',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $id = Auth::user()->id;

            $user = $this->user->find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            if(isset($request->gst_name) && !is_null($request->gst_name))
            {
                $user->gst_name = $request->gst_name;
            }
            if(isset($request->gst_number) && !is_null($request->gst_number))
            {
                $user->gst_number = $request->gst_number;
            }
            if(Auth::user()->number != $request->number)
            {
                $otp = rand(1000, 9999);
                $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');


                $user->otp = $otp;
                $user->otp_expired_at = $expired_at;
                $user->number_verify = 0;
                $user->number = $request->number;
                if($request->has('image') && !empty($request->file('image')))
                {
                    $user->image = Helpers_update('Images/avtor/', $user->image, $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                }
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Number Verification required',
                    'number verify' => 0,
                    'data' => $user
                ],205);
            }
            $user->number = $request->number;
            if($request->has('image') && !empty($request->file('image')))
            {
                $user->image = Helpers_update('Images/avtor/', $user->image, $request->file('image')->getClientOriginalExtension(), $request->file('image'));
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile Updated',
                'data' => $user
            ],205);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Issue',
                'data' => []
            ],409);
        }
    }

      /**
     * 
     * @return JsonResponse
     * 
     */
    public function transaction() : JsonResponse
    {   
        try {

            $user_id  = Auth::user()->id;
            $transaction_data = $this->wallettranscation->where('user_id',$user_id)->get();
    
            if(!empty($transaction_data))
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Get Wallet Transaction data',
                    'data' => [
                        'wallet_transaction' => Auth::user()->wallet_balance,
                        'transactions' => Auth::user()->transaction_data,
                    ]
                ],200); 
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found',
                    'data' => []
                ],404); 
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Data not found',
                'data' => []
            ],409);
        }          
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function UserLocation(Request $request) : JsonResponse
    {   
        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        
        try {
            $id = Auth::user()->id;
            $user = User::find($id);

            if($user->exists())
            {
                $user->latitude = $request->latitude;
                $user->longitude = $request->longitude;
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Data updated Successfully',
                    'data' => $user
                ],200); 
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                    'data' => []
                ],404); 
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Unexpected error',
                'data' => []
            ],409);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ReferralInfo(Request $request) : JsonResponse
    {
        if (!BusinessSetting::where(['key' => 'refferal_info'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'refferal_info'], [
                'value' => json_encode([
                    "bonus" => 0,
                    "content" => '',
                ]),
            ]);
        }

        $content = json_decode(BusinessSetting::where(['key' => 'refferal_info'])->first()->value)->content;

        return response()->json([
            'status' => true,
            'message' => 'Referral Info',
            'data' => [
                'code' => Auth::user()->referral_code,
                'content' => $content,
            ]
        ],200);
    }

    /**
     * 
     * @return JsonResponse
     */
    public function Notification() : JsonResponse
    {
        try {

            $notification = Notifications::where('user_id',Auth::user()->id)->get();

            return response()->json([
                'status' => true,
                'message' => 'Referral Info',
                'data' => [
                    'notification' => $notification,
                ]
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'unexpected error',
                'data' => []
            ],408);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function Coupon(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|exists:coupons,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $code = Coupons::where('code',$request->code)->first();
            $user_id = Auth::user()->id;
            
            if($code->status != 0)
            {
                return response()->json([
                    'status' => false,
                    'code' => 2,
                    'message' => 'Coupon Code Expired',
                    'data' => []
                ],408);
            }

            if($code->coupon_type == 'customer_wise')
            {
                if($code->customer_id == $user_id)
                {
                    if(Carbon::parse($code->start_date)->format('Y-m-d') <= Carbon::now()->format('Y-m-d') && Carbon::parse($code->expire_date)->format('Y-m-d') >= Carbon::now()->format('Y-m-d'))
                    {
                        $count = CouponApplied::where(['user_id' => $user_id,'coupon_code' => $code->code])->count();

                        if($count < $code->limit)
                        {
                            return response()->json([
                                'status' => true,
                                'code' => 1,
                                'message' => 'Coupon Applied Successfully',
                                'data' => [
                                    'code' => $code->code,
                                    'discount_type' => $code->discount_type,
                                    'discount' => $code->discount,
                                    'min_purchase' => $code->min_purchase,
                                    'max_discount' => $code->max_discount
                                ]
                            ],200);
                        }else{
                            return response()->json([
                                'status' => false,
                                'code' => 2,
                                'message' => 'Coupon Code Expired',
                                'data' => []
                            ],408);
                        }
                    }
                    else
                    {
                        return response()->json([
                            'status' => false,
                            'code' => 2,
                            'message' => 'Coupon Code Expired',
                            'data' => []
                        ],408);
                    }
                }else{
                    return response()->json([
                        'status' => false,
                        'code' => 1,
                        'message' => 'Invalid Coupon Code',
                        'data' => []
                    ],408);
                }
            }else{
                if(Carbon::parse($code->start_date)->format('Y-m-d') <= Carbon::now()->format('Y-m-d') && Carbon::parse($code->expire_date)->format('Y-m-d') >= Carbon::now()->format('Y-m-d'))
                {
                    $count = CouponApplied::where(['user_id' => $user_id,'coupon_code' => $code->code])->count();

                    if($count < $code->limit)
                    {
                        return response()->json([
                            'status' => true,
                            'code' => 1,
                            'message' => 'Coupon Applied Successfully',
                            'data' => [
                                'code' => $code->code,
                                'discount_type' => $code->discount_type,
                                'discount' => $code->discount,
                                'min_purchase' => $code->min_purchase,
                                'max_discount' => $code->max_discount
                            ]
                        ],200);
                    }else{
                        return response()->json([
                            'status' => false,
                            'code' => 2,
                            'message' => 'Coupon Code Expired',
                            'data' => []
                        ],408);
                    }
                }
                else
                {
                    return response()->json([
                        'status' => false,
                        'code' => 2,
                        'message' => 'Coupon Code Expired',
                        'data' => []
                    ],408);
                }
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Unexpected error',
                'data' => []
            ],409);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function AddWalletBalance(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'transaction_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            
            $status = Helpers_generate_wallet_transaction(Auth::user()->id,$request->transaction_id,'Add_Amount_In_Wallet',0,$request->amount,$request->amount);

            if($status)
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Wallet Balance Added Successfully',
                    'data' => Auth::user()->wallet_balance
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Unexpected error',
                    'data' => []
                ],409);
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Unexpected error '.$th->getMessage(),
                'data' => []
            ],409);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function BulkEnquiry(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|numeric',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $data = new BulkEnquriey();
            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->mobile;
            $data->description = $request->description;
            $data->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Data Inserted Successfully',
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Unexpected error '.$th->getMessage(),
            ],409);
        }
    }
}
