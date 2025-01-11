<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Products;
use App\Models\TimeSlot;
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

        $limit = 5;

        try {
            $ordercount = Order::where(['vender_id' => $vendor->id, 'order_type' => 'goods'])->count();
            $totalpage = ceil($ordercount / $limit);

            if($request->page == 1)
            {
                $orders = Order::where(['vender_id' => $vendor->id, 'order_type' => 'goods'])->with(['customer','OrderDetails'])->limit($limit)->get();
                return response()->json([
                    'status' => true,
                    'message' => 'Order Data',
                    'totalpages' => $totalpage,
                    'currentpage' => $request->page,
                    'data' => Helpers_Orders_formatting($orders,true,true,true)
                    
                ],200);
            }elseif ($request->page > 1) {
                $current = $limit * ($request->page - 1);

                $orders = Order::where(['vender_id' => $vendor->id, 'order_type' => 'goods'])->with(['customer','OrderDetails'])->offset($current)->limit($limit)->get();
                return response()->json([
                    'status' => true,
                    'message' => 'Order Data',
                    'totalpages' => $totalpage,
                    'currentpage' => $request->page,
                    'data' => Helpers_Orders_formatting($orders,true,true,true)
                    
                ],200);
            }else {
                return response()->json([
                    'status' => false,
                    'message' => 'First page should be 1',
                ],408);
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
     * @param int $id
     * @return JsonResponse
     */
    public function OrderDetail(int $id) : JsonResponse
    {
        try {
            if(Order::where(['id' => $id, 'vender_id' => auth('sanctum')->user()->id])->exists())
            {
                $order = Helpers_Orders_formatting(Order::where(['id' => $id, 'vender_id' => auth('sanctum')->user()->id])->with('OrderDetails')->first(), false, true, false);

                return response()->json([
                    'status' => true,
                    'message' => 'Order Details',
                    'data' => $order
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Order Not Found',
                    'data' => []
                ],408);
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
    public function OrderApproval(Request $request, $id) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accept,reject',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            if(Order::where(['id' => $id, 'vender_id' => auth('sanctum')->user()->id])->exists())
            {
                $order = Order::where(['id' => $id, 'vender_id' => auth('sanctum')->user()->id])->with('OrderDetails')->first();

                if($request->status == 'accept') {
                    $order->order_approval = 'accepted';
                    $order->order_status = 'confirmed';
                    $order->accepted_by = 1;
        
                    if (!BusinessSetting::where(['key' => 'order_approval_message'])->first()) {
                        BusinessSetting::updateOrInsert(['key' => 'order_approval_message'], [
                            'value' => json_encode([
                                'status'  => 0,
                                'message' => 'Order Accepted',
                            ]),
                        ]);
                    }
        
                    $notifications = new Notifications();
                    $notifications->user_id = $order->user_id;
                    $notifications->title = helpers_get_business_settings('order_approval_message')['message'];
                    $notifications->description = 'Your Order No. '.$order->id.' Approved';
                    $notifications->save();
                    
                    flash()->success(translate('Order Accepted'));
                }elseif ($request->status == 'reject') {
                    $order->order_approval = 'rejected';
                    $order->order_status = 'rejected';
        
                    if($order->payment_status == 'paid') 
                    {
                        $total = $order->order_amount - $order->coupon_amount;
        
                        Helpers_generate_wallet_transaction($order['user_id'], $order['id'], 'refund', 0, $total, $total);
                    }
                    else
                    {
                        $total = 0;
                        foreach ($order->OrderDetails as $key => $value) {
                            $total += $value->advance_payment;
                        }
        
                        if($order->partial_payment != null) {
                            $total += json_decode($order['partial_payment'], true)['wallet_applied'];
                        }
        
                        if($total > 0) {
                            Helpers_generate_wallet_transaction($order['user_id'], $order['id'], 'refund', 0, $total, $total);
                        }
                    }
                    
                    if (!BusinessSetting::where(['key' => 'order_rejected_message'])->first()) {
                        BusinessSetting::updateOrInsert(['key' => 'order_rejected_message'], [
                            'value' => json_encode([
                                'status'  => 0,
                                'message' => 'Order Rejected',
                            ]),
                        ]);
                    }
        
                    $notifications = new Notifications();
                    $notifications->user_id = $order->user_id;
                    $notifications->title = helpers_get_business_settings('order_rejected_message')['message'];
                    $notifications->description = 'Your Order No. '.$order->id.' Rejected';
                    $notifications->save();
        
                    flash()->warning(translate('Order Rejected'));
                }
                $order->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Order Status Updated',
                    'data' => Helpers_Orders_formatting($order, false, true, false)
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Order Not Found',
                    'data' => []
                ],408);
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
     * @param int $id
     * @return JsonResponse
     */
    public function OrderStatus(Request $request, int $id) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_status' => 'required|in:pending,confirmed,packing,out_for_delivery,delivered,failed,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            if(Order::where(['id' => $id, 'vender_id' => auth('sanctum')->user()->id, 'delivered_by' => 1])->exists())
            {
                $order = Order::where(['id' => $id, 'vender_id' => auth('sanctum')->user()->id])->with('OrderDetails')->first();

                if (in_array($order->order_status, ['returned', 'delivered', 'failed', 'canceled', 'rejected'])) {
                    return response()->json([
                        'status' => false,
                        'message' => translate('you_can_not_change_the_status_of ' . $order->order_status . ' order'),
                        'data' => []
                    ], 408);
                }
        
                //refund amount to wallet
                if (in_array($request['order_status'], ['returned', 'failed', 'canceled', 'rejected'])) 
                {
                    if($order['partial_payment'] == 'paid') 
                    {
                        $total = $order['order_amount'] - $order['coupon_amount'];
        
                        Helpers_generate_wallet_transaction($order['user_id'], $order['id'], 'refund', 0, $total, $total);
                    }
                    else
                    {
                        $total = 0;
                        foreach ($order['OrderDetails'] as $key => $value) {
                            $total += $value['advance_payment'];
                        }
        
                        if($order['partial_payment'] != null) {
                            $total += json_decode($order['partial_payment'], true)['wallet_applied'];
                        }
        
                        if($total > 0) {
                            Helpers_generate_wallet_transaction($order['user_id'], $order['id'], 'refund', 0, $total, $total);
                        }
                    }
                    
                    if (!BusinessSetting::where(['key' => 'add_fund_wallet_message'])->first()) {
                        BusinessSetting::updateOrInsert(['key' => 'add_fund_wallet_message'], [
                            'value' => json_encode([
                                'status'  => 0,
                                'message' => 'Amount added to your wallet',
                            ]),
                        ]);
                    }
                    
                    $notifications = new Notifications();
                    $notifications->user_id = $order->user_id;
                    $notifications->title = helpers_get_business_settings('add_fund_wallet_message')['message'];
                    $notifications->description = 'Your Order No. '.$order->id.' Rejected';
                    $notifications->save();
                }
        
                if ($request->order_status == 'pending') 
                {
                    $notifications = new Notifications();
                    $notifications->user_id = $order->user_id;
                    $notifications->title = 'Order Pending';
                    $notifications->description = 'Your Order No. '.$order->id.' is transfered to Pending';
                    $notifications->save();
                }
        
                if ($request->order_status == 'confirmed')
                {
                    $notifications = new Notifications();
                    $notifications->user_id = $order->user_id;
                    $notifications->title = 'Order Confirmed';
                    $notifications->description = 'Your Order No. '.$order->id.' Confirmed';
                    $notifications->save();
                }
               
                if ($request->order_status == 'packing') 
                {
                    foreach ($order->OrderDetails as $detail) 
                    {
                        if ($detail['is_stock_decreased'] == 1) {
                            
                            $product = Products::find($detail['product_id']);
                            
                            if ($product != null) 
                            {
                                $type = json_decode($detail['variation'])->type;
                                $variationStore = [];
                                foreach (json_decode($product['variations'], true) as $var) 
                                {
                                    if ($type == $var['type']) 
                                    {
                                        $var['stock'] = $var['stock'] - $detail['quantity'];
                                    }
                                    $variationStore[] = $var;
                                }
                                Products::where(['id' => $product['id']])->update([
                                    'variations' => json_encode($variationStore),
                                    'total_stock' => $product['total_stock'] - $detail['quantity'],
                                    'total_sale' => $product['total_sale'] + $detail['quantity'],
                                ]);
                                Order_details::where(['id' => $detail['id']])->update([
                                    'is_stock_decreased' => 0,
                                ]);
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Product Not Found',
                                    'data' => []
                                ],408);
                            }
                        }
                    }
                    
        
                    // $deliverymanFcmToken = $order->delivery_man->fcm_token;
                    // $message = Helpers_order_status_update_message('deliveryman_order_processing');
                    
                    // $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);
        
                    // try {
                    //     if ($value) {
                    //         $data = [
                    //             'title' => translate('Order'),
                    //             'description' => $value,
                    //             'order_id' => $order['id'],
                    //             'image' => '',
                    //             'type' => 'order'
                    //         ];
                    //         Helpers_send_push_notif_to_device($deliverymanFcmToken, $data);
                    //     }
                    // } catch (\Exception $e) {
                    //     flash()->warning(translate('Push notification failed for DeliveryMan!'));
                    // }
        
                    if (!BusinessSetting::where(['key' => 'order_processing_message'])->first()) {
                        BusinessSetting::updateOrInsert(['key' => 'order_processing_message'], [
                            'value' => json_encode([
                                'status'  => 0,
                                'message' => 'Your order is packing',
                            ]),
                        ]);
                    }
        
                    $notifications = new Notifications();
                    $notifications->user_id = $order->user_id;
                    $notifications->title = helpers_get_business_settings('order_processing_message')['message'];
                    $notifications->description = 'Your Order No. '.$order->id.' Packing';
                    $notifications->save();
                }
                
                //editable
                if ($request->order_status == 'out_for_delivery') 
                {
                    if ($order['delivery_date'] == null || $order['delivery_timeslot_id'] == null) 
                    {
                        return response()->json([
                            'status' => false,
                            'message' => translate('Please assign delivery Information first!'),
                            'data' => []
                        ], 408);
                    }
        
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
                }
        
                if ($request->order_status == 'delivered' && $order['payment_status'] != 'paid') 
                {
                    return response()->json([
                        'status' => false,
                        'message' => translate('you_can_not_delivered_a_order_when_order_status_is_not_paid. please_update_payment_status_first'),
                        'data' => []
                    ], 408);
                }
        
                if ($request->order_status == 'delivered') 
                {
                    if($order['delivery_date'] == null && $order['delivery_timeslot_id'] == null && $order['delivery_man_id'] == null)
                    {
                        return response()->json([
                            'status' => false,
                            'message' => translate('Please assign delivery Information first!'),
                            'data' => []
                        ], 408);
                    }
        
                    // foreach ($order->OrderDetails as $key => $value) {
                    //     if($value['installation'] == 0 && $value['service_man_id'] != null)
                    //     {
                    //         return response()->json([
                    //             'status' => false,
                    //             'message' => translate('Please assign Service and Installation Information first!'),
                    //             'data' => []
                    //         ], 408);
                    //     }
                    // }
        
                    // if ($order['payment_method'] == 'cash_on_delivery') {
                    //     $partialData = OrderPartialPayment::where(['order_id' => $order->id])->first();
                    //     if ($partialData) {
                    //         $partial = new OrderPartialPayment;
                    //         $partial->order_id = $order['id'];
                    //         $partial->paid_with = 'cash_on_delivery';
                    //         $partial->paid_amount = $partialData->due_amount;
                    //         $partial->due_amount = 0;
                    //         $partial->save();
                    //     }
                    // }
        
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
                }
        
                //stock adjust
                if ($request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled') 
                {
                    
                    foreach ($order->OrderDetails as $detail) {
                        if (!isset($detail->variant)) {
                            if ($detail['is_stock_decreased'] == 0) {
                                $product = Products::find($detail['product_id']);
                                if (!isset($detail->variant)) {
                                    dd('ache');
                                }
        
                                if ($product != null) {
                                    $type = json_decode($detail['variation'])[0]->type;
                                    $variationStore = [];
                                    foreach (json_decode($product['variations'], true) as $var) {
                                        if ($type == $var['type']) {
                                            $var['stock'] += $detail['quantity'];
                                        }
                                        $variationStore[] = $var;
                                    }
                                    Products::where(['id' => $product['id']])->update([
                                        'variations' => json_encode($variationStore),
                                        'total_stock' => $product['total_stock'] + $detail['quantity'],
                                        'total_sale' => $product['total_sale'] - $detail['quantity'],
                                    ]);
                                    Order_details::where(['id' => $detail['id']])->update([
                                        'is_stock_decreased' => 1,
                                    ]);
                                } else {
                                    flash()->warning(translate('Product_deleted'));
                                }
                            }
                        }
                    }
                }
        
                $order->order_status = $request->order_status;
                $order->save();
                
                $message = Helpers_order_status_update_message($request->order_status);
                $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->fmc_token : null) : ($order->guest ? $order->guest->fcm_token : null);
                
                $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);
        
                try {
                    if ($value) {
                        $data = [
                            'title' => translate('Order'),
                            'description' => $value,
                            'order_id' => $order['id'],
                            'image' => '',
                            'type' => 'order'
                        ];
                        Helpers_send_push_notif_to_device($customerFcmToken, $data);
                    }
                } catch (\Exception $e) {
                    flash()->warning(translate('Push notification failed for Customer!'));
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Order Status Updated',
                    'data' => Helpers_Orders_formatting($order, false, true, false)
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
     * @param int $id
     * @return JsonResponse
     */
    public function OrderDate(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            if(Order::where(['id' => $id, 'vender_id' => auth('sanctum')->user()->id, 'delivered_by' => 1])->exists())
            {
                $order = Order::find($id);
                $order->delivery_date = $request->date;
                $order->save();
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                    'data' => []
                ],408);
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
     * 
     * @return JsonResponse
     */
    public function OrderGetTimeSlots(): JsonResponse
    {
        try {
            $slots = TimeSlot::where('status' , 1)->get();
        
            return response()->json([
                'status' => true,
                'message' => 'TimeSlots',
                'data' => $slots
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
     * @param int $id
     * @return JsonResponse
     */
    public function OrderTimeSlots(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'timeslot' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            if(Order::where(['id' => $id, 'vender_id' => auth('sanctum')->user()->id, 'delivered_by' => 1])->exists())
            {
                $order = Order::find($id);
                $order->delivery_timeslot_id = $request->timeslot;
                $order->save();
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                    'data' => []
                ],408);
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
    public function OrderSearch(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {

            $keys = explode(' ', $request->key);

            // Finding Orders
            $order1 = Order::where('vender_id', auth('sanctum')->user()->id)->where(function ($q) use ($keys) 
            {
                foreach ($keys as $value)
                {
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            })->get();

            $order2 = Order::where('vender_id', auth('sanctum')->user()->id)->with(['OrderDetails','customer' => function($qurey) use ($keys) {
                foreach ($keys as $value)
                {
                    $qurey->orWhere('name', 'like', "%{$value}%");
                }
            }])->get();

            $orders = Arr::collapse([$order1,$order2]);

            return response()->json([
                'status' => true,
                'message' => 'Orders',
                'data' => Helpers_Orders_formatting($orders, true, true, false)
            ],200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error'.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    private function dynamic_key_replaced_message($message, $type, $order = null, $customer = null)
    {
        $customerName = '';
        $deliverymanName = '';
        $order_id = $order ? $order->id : '';

        if ($type == 'order'){
            $deliverymanName = $order->delivery_man ? $order->delivery_man->f_name. ' '. $order->delivery_man->l_name : '';
            $customerName = $order->is_guest == 0 ? ($order->customer ? $order->customer->f_name. ' '. $order->customer->l_name : '') : 'Guest User';
        }
        if ($type == 'wallet'){
            $customerName = $customer->f_name. ' '. $customer->l_name;
        }
        $storeName = Helpers_get_business_settings('app_name');
        $value = Helpers_text_variable_data_format(value:$message, user_name: $customerName, store_name: $storeName, delivery_man_name: $deliverymanName, order_id: $order_id);
        return $value;
    }
}
