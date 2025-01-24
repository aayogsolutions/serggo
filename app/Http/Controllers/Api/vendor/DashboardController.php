<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use App\Models\HomeSliderBanner;
use App\Models\Notifications;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WithdrawalRequests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;

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

            try {
                $homeslider = HomeSliderBanner::where(['status' => 0,'ui_type' => 'vendor'])->orderBy('priority', 'asc')->get();
            } catch (\Throwable $th) {
                $homeslider = [];
            }

            $vendor = auth('sanctum')->user();
            
            if($vendor->is_verify == 0)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'You Need to Submit KYC',
                    'data' => [
                        'banner' => $homeslider,
                        'vendor' => $vendor,
                    ]
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
                        'banner' => $homeslider,
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
                        'banner' => $homeslider,
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
                        'banner' => $homeslider,
                        'vendor' => $vendor,
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
    public function SaleReport(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'required|date',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $vendor = auth('sanctum')->user();
            if(!is_null($request->from_date))
            {
                $data = Order::where('vender_id', $vendor->id)->with(['OrderDetails'])->whereDate('created_at', '>=', Carbon::parse($request->from_date)->format('Y-m-d'))->whereDate('created_at', '<=', Carbon::parse($request->to_date)->format('Y-m-d'))->get();
            }
            else
            {
                $data = Order::where('vender_id', $vendor->id)->with(['OrderDetails'])->get();
            }

            if(count($data) != 0)
            {
                foreach ($data as $key => $value) {
                    foreach ($value->OrderDetails as $key => $value1) {
                        $list[] = [
                            'order_id' => $value->id,
                            'order_amount' => $value->order_amount,
                            'product_name' => json_decode($value1->product_details)->name,
                            'price' => $value1->price,
                            'quantity' => $value1->quantity,
                            'tax_amount' => $value1->tax_amount,
                            'discount_on_product' => $value1->discount_on_product,
                            'coupon_amount' => $value1->coupon_amount,
                            'coupon_code' => $value->coupon_code,
                            'advance_payment' => $value1->advance_payment,
                        ];
                    }
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'No data found',
                    'data' => []
                ],408); 
            }

            $FileName = str_replace(' ', '_', $vendor->name).'_'.Carbon::now()->format('Y-m-d_H-i-s').'.xlsx';

            (new FastExcel($list))->export(public_path('excel/vendor/salereport/'.$FileName));
            
            return response()->json([
                'status' => true,
                'message' => 'Sale Report',
                'data' => 'excel/vendor/salereport/'.$FileName
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


