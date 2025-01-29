<?php

namespace App\Http\Controllers\Api\partner;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\OrderOTPS;
use App\Models\Products;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Arr;

class OrderController extends Controller
{
    public function __construct(
       
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function OrderList(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|numeric',
            'ItemCount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $vendor = auth('sanctum')->user();

        if (!$vendor) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found',
                'data' => []
            ], 404);
        }

        $limit = $request->ItemCount;

        try {
            $order1 = Order::where(['deliveryman_id' => $vendor->id, 'deliveryman_status' => 0,'order_approval' => 'accepted'])->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery'])->with('OrderDetails')->get();

            $order2 = Order_details::where(['service_man_id' => $vendor->id, 'serviceman_status' => 0])->with('OrderDetails' , function($q) {
                return $q->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery']);
            })->get();

            foreach ($order2 as $key => $value) {
                $order1[] = $value;
            }
            
            $ordercount = $order1->count();
            $orders = [];
            foreach ($order1 as $key => $value) {
                if($key > ($limit * ($request->page - 1)) && $key <= ($limit * $request->page)){
                    $orders[] = $value;
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Order Data',
                'totalorders' => $ordercount,
                'currentpage' => $request->page,
                'data' => Helpers_Orders_formatting($orders,true,true,true)
                
            ],200);
            

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
    public function OrderOngoingList() : JsonResponse
    {
        $vendor = auth('sanctum')->user();

        if (!$vendor) {
            return response()->json([
                'status' => false,
                'message' => 'Partner not found',
                'data' => []
            ], 404);
        }

        try {
            $order1 = Order::where(['deliveryman_id' => $vendor->id, 'deliveryman_status' => 0,'order_approval' => 'accepted'])->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery'])->with('OrderDetails')->get();

            $order2 = Order_details::where(['service_man_id' => $vendor->id, 'serviceman_status' => 0])->with('OrderDetails' , function($q) {
                return $q->whereIn('order_status' , ['pending','confirmed','packing','out_for_delivery']);
            })->get();

            foreach ($order2 as $key => $value) {
                $order1[] = $value;
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Order Data',
                'data' => Helpers_Orders_formatting($order1,true,true,true)
                
            ],200);
            

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function OrderDeliveryAccept(int $id) : JsonResponse
    {
        try {
            $order = Order::find($id);
            $order->deliveryman_status = 0;
            $order->save();
            return response()->json([
                'status' => true,
                'message' => 'Order Accepted',
                'data' => []
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function OrderServiceAccept(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|numeric|exists:orders,id',
            'service_id' => 'required|numeric|exists:order_details,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $order = Order_details::where(['id' => $request->service_id , 'order_id' => $request->order_id])->first();
            $order->serviceman_status = 0;
            $order->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Order Accepted',
                'data' => []
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function OrderOtpVerify(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|numeric|exists:orders,id',
            'type' => 'required|in:delivery_reached,delivery_completed,service',
            'otp' => 'required|numeric',
            'service_id' => 'nullable|required_if:type,service|exists:order_details,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $order = Order::find($request->order_id);
            
            if($request->type == 'delivery_reached') 
            {
                $otpverify = OrderOTPS::where(['order_id' => $request->order_id , 'otp_for' => 'delivery_reached'])->orderBy('id', 'desc')->first();

                if($otpverify->otp != $request->otp)
                {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid OTP',
                        'data' => []
                    ],401);
                }
                $otpverify->verified = 1;
                $otpverify->save();

                $order->order_status = 'out_for_delivery';
                $order->save();

                if (!BusinessSetting::where(['key' => 'out_for_delivery_message'])->first()) {
                    BusinessSetting::updateOrInsert(['key' => 'out_for_delivery_message'], [
                        'value' => json_encode([
                            'status'  => 0,
                            'message' => 'Order Out for Delivery',
                        ]),
                    ]);
                }
                
                $notifications = new Notifications();
                $notifications->user_id = $order->user_id;
                $notifications->title = helpers_get_business_settings('out_for_delivery_message')['message'];
                $notifications->description = 'Your Order No. '.$order->id.' out for delivery';
                $notifications->save();
                
                return response()->json([
                    'status' => true,
                    'message' => 'OTP Accepted',
                    'data' => []
                ],200);
            }

            if($request->type == 'delivery_completed')
            {
                $otpverify = OrderOTPS::where(['order_id' => $request->order_id , 'otp_for' => 'delivery_completed'])->orderBy('id', 'desc')->first();

                if($otpverify->otp != $request->otp) 
                {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid OTP',
                        'data' => []
                    ],401);
                }

                $otpverify->verified = 1;
                $otpverify->save();

                $order->order_status = 'delivered';
                $order->save();

                if (!BusinessSetting::where(['key' => 'order_delivered_message'])->first()) {
                    BusinessSetting::updateOrInsert(['key' => 'order_delivered_message'], [
                        'value' => json_encode([
                            'status'  => 0,
                            'message' => 'Order Delivered',
                        ]),
                    ]);
                }
                
                $notifications = new Notifications();
                $notifications->user_id = $order->user_id;
                $notifications->title = helpers_get_business_settings('order_delivered_message')['message'];
                $notifications->description = 'Your Order No. '.$order->id.' Delivered';
                $notifications->save();

                return response()->json([
                    'status' => true,
                    'message' => 'OTP Accepted',
                    'data' => []
                ],200);
            }

            if($request->type == 'service')
            {
                $otpverify = OrderOTPS::where(['order_id' => $request->order_id , 'otp_for' => 'delivery_completed','service_id' => $request->service_id])->orderBy('id', 'desc')->first();

                if($otpverify->otp != $request->otp) 
                {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid OTP',
                        'data' => []
                    ],401);
                }

                $otpverify->verified = 1;
                $otpverify->save();

                $order->order_status = 'delivered';
                $order->save();

                if (!BusinessSetting::where(['key' => 'order_delivered_message'])->first()) {
                    BusinessSetting::updateOrInsert(['key' => 'order_delivered_message'], [
                        'value' => json_encode([
                            'status'  => 0,
                            'message' => 'Order Delivered',
                        ]),
                    ]);
                }
                
                $notifications = new Notifications();
                $notifications->user_id = $order->user_id;
                $notifications->title = helpers_get_business_settings('order_delivered_message')['message'];
                $notifications->description = 'Your Order No. '.$order->id.' Delivered';
                $notifications->save();

                return response()->json([
                    'status' => true,
                    'message' => 'OTP Accepted',
                    'data' => []
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
     * @param Request $request
     * @return JsonResponse
     */
    public function OrderProductCompleteOtp(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|numeric|exists:orders,id',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $order = Order::find($request->order_id);

            if(!$order)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                    'data' => []
                ],404);
            }

            if($order->order_status == 'delivered')
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Order Already Delivered',
                    'data' => []
                ],200);
            }

            $otp = rand(1000, 9999);
            $verification = new OrderOTPS();
            $verification->order_id = $order->id;
            $verification->otp = $otp;
            $verification->otp_for = 'delivery_completed';
            $verification->save();

            return response()->json([
                'status' => true,
                'otp' => $otp,
                'message' => 'otp sended'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function OrderServiceCompleteOtp(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|numeric|exists:orders,id',
            'service_id' => 'required|numeric|exists:order_details,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $order = Order::find($request->order_id);

            if(!$order)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                    'data' => []
                ],404);
            }

            if($order->order_status == 'delivered')
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Order Already Delivered',
                    'data' => []
                ],200);
            }

            $otp = rand(1000, 9999);
            $verification = new OrderOTPS();
            $verification->order_id = $order->id;
            $verification->otp = $otp;
            $verification->otp_for = 'service';
            $verification->save();

            return response()->json([
                'status' => true,
                'otp' => $otp,
                'message' => 'otp sended'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
