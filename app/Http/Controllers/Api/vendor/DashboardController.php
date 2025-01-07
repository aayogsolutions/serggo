<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use App\Models\HomeSliderBanner;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WithdrawalRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function __construct(
       
    ){}

    /**
     * 
     * @return JsonResponse
     */
    public function Index() : JsonResponse
    {
        try {
            $vendor = auth('sanctum')->user();
            
            if($vendor->is_verify == 0)
            {
                return response()->json([
                    'status' => false,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'You Need to Submit KYC',
                    'data' => $vendor
                ],200);
            }
            elseif ($vendor->is_verify == 1) 
            {
                $vendor->aadhar_document =  gettype($vendor->aadhar_document) == 'array' ? $vendor->aadhar_document : json_decode($vendor->aadhar_document, true);
                $vendor->category =  gettype($vendor->category) == 'array' ? $vendor->category : json_decode($vendor->category, true);
                $vendor->working_days =  gettype($vendor->working_days) == 'array' ? $vendor->working_days : json_decode($vendor->working_days, true);
                

                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'KYC Approval Pending',
                    'data' => [
                        'vendor' => $vendor,
                    ]
                ],200);
            }
            elseif ($vendor->is_verify == 2) 
            {
                $banner = HomeSliderBanner::where(['ui_type' => 'vender_service','status' => 0])->orderBy('priority', 'asc')->get();

                $orders = Helpers_Orders_formatting(Order::where(['vender_id' => $vendor->id , 'order_type' => 'goods'])->whereNotIn('order_status' , ['delivered,canceled,returned,failed,rejected'])->orderby('id','desc')->with(['customer','OrderDetails'])->get(), true, true, false);
                
                $vendor->aadhar_document =  gettype($vendor->aadhar_document) == 'array' ? $vendor->aadhar_document : json_decode($vendor->aadhar_document, true);
                $vendor->category =  gettype($vendor->category) == 'array' ? $vendor->category : json_decode($vendor->category, true);
                $vendor->working_days =  gettype($vendor->working_days) == 'array' ? $vendor->working_days : json_decode($vendor->working_days, true);
                

                // Vender Orders
                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'Dashboard',
                    'data' => [
                        'banner' => $banner,
                        'vendor' => $vendor,
                        'order' => $orders
                    ]
                ],200);
            }
            elseif ($vendor->is_verify == 3) 
            {
                $vendor->aadhar_document =  gettype($vendor->aadhar_document) == 'array' ? $vendor->aadhar_document : json_decode($vendor->aadhar_document, true);
                $vendor->category =  gettype($vendor->category) == 'array' ? $vendor->category : json_decode($vendor->category, true);
                $vendor->working_days =  gettype($vendor->working_days) == 'array' ? $vendor->working_days : json_decode($vendor->working_days, true);
                

                return response()->json([
                    'status' => false,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'KYC Rejected by Admin',
                    'data' => [
                        'vendor' => $vendor
                    ]
                ],200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error'.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function WithdrawalRequest(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        
        try {

            if($request->amount > auth('sanctum')->user()->wallet_balance)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient Balance',
                    'data' => []
                ],200);
            }

            $data = new WithdrawalRequests();
            $data->vendor_id = auth('sanctum')->user()->id;
            $data->amount = $request->amount;
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Withdrawal Request',
                'data' => []
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error'.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @return JsonResponse
     */
    public function WithdrawalList() : JsonResponse
    {
        try {
            $data = WithdrawalRequests::where('vendor_id', auth('sanctum')->user()->id)->orderBy('id', 'desc')->get();
            return response()->json([
                'status' => true,
                'message' => 'Withdrawal Request List || 0 = pending, 1 = approved, 2 = rejected',
                'data' => $data
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error'.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * 
     * @return JsonResponse
     */
    public function TransactionList() : JsonResponse
    {
        try {
            $data = Order::select('id','order_amount','payment_method','commission','created_at')->where('vender_id', auth('sanctum')->user()->id)->orderBy('id', 'desc')->get();
            return response()->json([
                'status' => true,
                'message' => 'Transaction List',
                'data' => $data
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error'.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function FcmUpdate(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required',
            'user_id' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            if($request->role == 'vendor')
            {
                $data = Vendor::find($request->user_id);
            }
            else
            {
                $data = User::find($request->user_id);
            }

            if(!$data)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'User Not Found',
                    'data' => []
                ],408);
            }
            $data->fmc_token = $request->fcm_token;
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Added FCM Token',
                'data' => []
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error'.$th->getMessage(),
                'data' => []
            ],408);
        }
    }
}


