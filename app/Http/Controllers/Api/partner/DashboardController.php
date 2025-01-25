<?php

namespace App\Http\Controllers\Api\partner;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\WithdrawalRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
                    'data' => [
                        'vendor' => $vendor
                    ]
                ],408);
            }elseif ($vendor->is_verify == 1) 
            {
                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'KYC Approval Pending',
                    'data' => [
                        'vendor' => $vendor
                    ]
                ],200);
            }elseif ($vendor->is_verify == 2) 
            {
                $order1 = Order::where(['deliveryman_id' => $vendor->id])->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery'])->with('OrderDetails')->get();

                $order2 = Order_details::where(['service_man_id' => $vendor->id])->with('OrderDetails' , function($q) {
                    return $q->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery']);
                })->get();

                $orders = Arr::collapse([$order1, $order2]);
                
                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'Dashboard',
                    'data' => [
                        'vendor' => $vendor,
                        'order' => Helpers_Orders_formatting($orders,true,true)
                    ]
                ],200);
            }elseif ($vendor->is_verify == 3) 
            {
                return response()->json([
                    'status' => true,
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
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * 
     * @return JsonResponse
     */
    public function NotificationList() : JsonResponse
    {
        try {
            $data = Notifications::where(['user_id' => auth('sanctum')->user()->id, 'type' => 1])->orderBy('id', 'desc')->get();
            return response()->json([
                'status' => true,
                'message' => 'Notification List',
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
            $data = Order::select('id','delivery_charge','payment_method','commission','created_at')->where('deliveryman_id', auth('sanctum')->user()->id)->orderBy('id', 'desc')->get();

            $data2 = Order::select('id','payment_method','created_at')->with('OrderDetails', function ($query) {
                return $query->where('service_man_id', auth('sanctum')->user()->id); 
            })->get();

            
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
}
