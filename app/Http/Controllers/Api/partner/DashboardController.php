<?php

namespace App\Http\Controllers\Api\partner;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Vendor;
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
                $order1 = Order::where(['deliveryman_id' => $vendor->id, 'deliveryman_status' => 1,'order_approval' => 'accepted'])->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery'])->with('OrderDetails')->get();
                
                $orderservice = Order_details::where(['service_man_id' => $vendor->id,'serviceman_status' => 1])->with('OrderDetails' , function($q) {
                    $q->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery']);
                })->get();

                $order2 = [];
                foreach($orderservice as $key => $value)
                {
                    $order2[] = Order::select(
                        'id','order_type','order_status','order_approval','delivery_address','created_at'
                        )->where(['id' => $value->order_id,'order_approval' => 'accepted'])->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery'])->with('OrderDetails',function($q) use ($value) {
                        $q->where(['service_man_id' => $value->service_man_id,'serviceman_status' => 1, 'order_id' => $value->order_id]);
                    })->first();
                }

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

    /**
     * 
     * @return JsonResponse
     */
    public function PartnerData() : JsonResponse
    {
        try {
            $vendor = auth('sanctum')->user();

            $vendor->aadhar_document = json_decode($vendor->aadhar_document);
            $vendor->category = json_decode($vendor->category);
            $vendor->working_days = json_decode($vendor->working_days);

            // Today Calculations

            $todaydelivery = Order::where(['deliveryman_id' => $vendor->id, 'deliveryman_status' => 1,'order_approval' => 'accepted', 'order_status' => 'delivered'])->whereDate('updated_at', date('Y-m-d'))->get();

            $todayservice = Order_details::where(['service_man_id' => $vendor->id,'serviceman_status' => 1])->with('OrderDetails' , function($q) {
                return $q->where(['order_status' => 'delivered'])->whereDate('updated_at', date('Y-m-d'));
            })->get();

            if(!is_null($todaydelivery) && count($todaydelivery) > 0)
            {
                $todayd = $this->CalculateDeliveryAmount($todaydelivery);
            }else{
                $todayd = 0;
            }

            if(!is_null($todayservice) && count($todayservice) > 0)
            {
                $todays = $this->CalculateServiceAmount($todayservice);
            }else{
                $todays = 0;
            }

            $today = $todayd + $todays;

            // Month Calculation

            $monthdelivery = Order::where(['deliveryman_id' => $vendor->id, 'deliveryman_status' => 1,'order_approval' => 'accepted', 'order_status' => 'delivered'])->whereMonth('updated_at', date('Y-m'))->get();

            $monthservice = Order_details::where(['service_man_id' => $vendor->id,'serviceman_status' => 1])->with('OrderDetails' , function($q) {
                return $q->where(['order_status' => 'delivered'])->whereMonth('updated_at', date('Y-m'));
            })->get();

            if(!is_null($monthdelivery) && count($monthdelivery) > 0)
            {
                $monthd = $this->CalculateDeliveryAmount($monthdelivery);
            }else{
                $monthd = 0;
            }

            if(!is_null($monthservice) && count($monthservice) > 0)
            {
                $months = $this->CalculateServiceAmount($monthservice);
            }else{
                $months = 0;
            }

            $month = $monthd + $months;

            // Year Calculations

            $totaldelivery = Order::where(['deliveryman_id' => $vendor->id, 'deliveryman_status' => 1,'order_approval' => 'accepted', 'order_status' => 'delivered'])->get();

            $totalservice = Order_details::where(['service_man_id' => $vendor->id,'serviceman_status' => 1])->with('OrderDetails' , function($q) {
                return $q->where(['order_status' => 'delivered']);
            })->get();

            if(!is_null($totaldelivery) && count($totaldelivery) > 0)
            {
                $totald = $this->CalculateDeliveryAmount($totaldelivery);
            }else{
                $totald = 0;
            }

            if(!is_null($totalservice) && count($totalservice) > 0)
            {
                $totals = $this->CalculateServiceAmount($totalservice);
            }else{
                $totals = 0;
            }

            $total = $totald + $totals;

            return response()->json([
                'status' => true,
                'message' => 'Profile',
                'Todaysale' => $today,
                'Monthlysale' => $month,
                'TotalSale' => $total,
                'data' => $vendor
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
    public function PartnerUpdate(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'bank_holder_name' => 'required|string|max:255',
            'bank_ifsc' => 'required|string|max:255',
            'bank_account_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {

            $vendor = Vendor::find(auth('sanctum')->user()->id);
            
            $vendor->bank_name = $request->bank_name;
            $vendor->bank_holder_name = $request->bank_holder_name;
            $vendor->bank_ifsc = $request->bank_ifsc;
            $vendor->bank_account_no = $request->bank_account_no;
            $vendor->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'data' => $vendor
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
     * @param $order
     * @return int
     */
    private function CalculateDeliveryAmount($order)
    {
        $amount = 0;
        foreach ($order as $key => $value) 
        {
            $amount += $value->delivery_charge;
        }

        return $amount;
    }

    /**
     * @param $order
     * @return int
     */
    private function CalculateServiceAmount($order)
    {
        $amount = 0;
        foreach ($order as $key => $value) 
        {
            $amount += $value->installastion_amount;
        }

        return $amount;
    }
}
