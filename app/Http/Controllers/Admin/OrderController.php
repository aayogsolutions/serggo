<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Branch,
    BusinessSetting,
    LoyaltyTransaction,
    Notifications,
    Order,
    Order_details,
    Products,
    OfflinePayment,
    OrderOTPS,
    OrderPartialPayment,
    Referral_setting,
    TimeSlot,
    User,
    Vendor,
};
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function __construct(
        private Branch $branch,
        private BusinessSetting $business_setting,
        private Order $order,
        private Order_details $order_detail,
        private Products $product,
        private User $user,
        private Vendor $vendor,
        private TimeSlot $timeslots
    ) {
    }

    /**
     * @param Request $request
     * @param $status
     * @return Factory|View|Application
     */
    public function list(Request $request, $status): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $this->order->where(['checked' => 0])->update(['checked' => 1]);

        $query = $this->order->where('order_approval' , '!=', 'pending')->with(['customer','TimeSlot','ServiceTimeSlot'])
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        if ($status != 'all') {
            $query->where(['order_status' => $status]);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "{$value}%");
                }
            });
            $queryParam['search'] = $search;
        }
        
        $orders = $query->orderBy('id', 'desc')->paginate(Helpers_getPagination())->appends($queryParam);

        $countData = [];
        $orderStatuses = ['pending', 'confirmed', 'packaging', 'out_for_delivery', 'delivered', 'canceled', 'returned', 'failed'];

        foreach ($orderStatuses as $orderStatus) {
            $countData[$orderStatus] = $this->order->where('order_status', $orderStatus)
                ->when(!is_null($startDate) && !is_null($endDate), function ($query) use ($startDate, $endDate) {
                    return $query->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
            })->count();
        }

        return view('Admin.views.order.list', compact('orders', 'status', 'search','startDate', 'endDate', 'countData'));
    }

    /**
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function details($id): Factory|View|Application|RedirectResponse
    {
        $order = $this->order->with('OrderDetails')->where(['id' => $id])->first();

        $servicemanlist = $this->vendor->where(['is_block' => 0, 'role' => '1','is_verify' => 2])->get();

        if (isset($order)) {
            return view('Admin.views.order.order-view', compact('order', 'servicemanlist'));
        } else {
            flash()->info(translate('No more orders!'));
            return back();
        }
    }

    /**
     * @param Request $request
     *
     * @return Factory|View|Application
     */
    public function ApprovalRequest(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $this->order->where(['checked' => 1])->update(['checked' => 0]);

        $query = $this->order->with(['customer','OrderDetails','vendororders'])->where(['order_status' => 'pending'])->where(['order_approval' => 'pending']);

        $queryParam = ['start_date' => $startDate, 'end_date' => $endDate];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "{$value}%");
                }
            });
            $queryParam['search'] = $search;
        }

        $orders = $query->orderBy('id', 'desc')->paginate(Helpers_getPagination())->appends($queryParam);
       
        return view('Admin.views.order.order-approval.list', compact('orders',  'search'));
    }

    /**
     * @param Request $request
     * @param $status
     * @return Factory|View|Application
     */
    public function ApprovalRequestView($id): View|Factory|Application
    {
        $order = $this->order->with(['customer','OrderDetails','vendororders'])->where(['id' => $id])->first();
        $timeslots = $this->timeslots->where(['status' => 1])->get();
        $serviceman = Vendor::where(['is_block' => 0])->get();
        return view('Admin.views.order.order-approval.approval_page', compact('order','timeslots', 'serviceman'));
    }

    /**
     * @param Request $request
     * @param $status
     * @return Factory|View|Application
     */
    public function ApprovalRequestServiceView($id): View|Factory|Application
    {
        $order = $this->order->with(['customer','OrderDetails','vendororders'])->where(['id' => $id])->first();
        $timeslots = $this->timeslots->where(['status' => 1])->get();
        $serviceman = Vendor::where(['role' => 1,'is_block' => 0])->get();
        return view('Admin.views.order.order-approval.approval_page', compact('order','timeslots', 'serviceman'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return jsonResponse
     */
    public function ApprovalRequestAction(Request $request,$id): jsonResponse
    {
        $request->validate([
            'status' => 'nullable',
        ]);
        $order = $this->order->where('id',$id)->with('OrderDetails')->first();

        if($request->status == 'accept') {
            if($order->order_type == 'amc') {
                $order->order_approval = 'accepted';
                $order->order_status = 'delivered';
            }else{
                $order->order_approval = 'accepted';
                $order->order_status = 'confirmed';
            }
            

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
            'status' => true
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function paymentStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->find($request->id);

        if ($request->payment_status == 'paid') 
        {
            $order->order_status = 'confirmed';
            $order->order_approval = 'accepted';

            $message = Helpers_order_status_update_message('confirmed');
            $customerFcmToken = null;
            
            $value = null;

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
                //
            }
        }

        $order->payment_status = $request->payment_status;
        $order->save();
        flash()->success(translate('Payment status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function OrderCategory(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->find($request->id);

        if($request->order_status == 'delivered' || $request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled' || $request->order_status == 'rejected') 
        {
            flash()->success(translate('Cannot do this update on '.$request->order_status.' status!'));
            return back();
        }
        $order->order_category = $request->order_status;
        $order->save();
        flash()->success(translate('Order Category updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function OrderDate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->find($request->id);

        if($request->order_status == 'delivered' || $request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled' || $request->order_status == 'rejected') 
        {
            flash()->success(translate('Cannot do this update on '.$request->order_status.' status!'));
            return back();
        }

        $order->delivery_date = Carbon::parse($request->date)->format('Y-m-d');
        $order->save();
        flash()->success(translate('Order Date updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function OrderTime(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->find($request->id);

        if($request->order_status == 'delivered' || $request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled' || $request->order_status == 'rejected') 
        {
            flash()->success(translate('Cannot do this update on '.$request->order_status.' status!'));
            return back();
        }

        $order->delivery_timeslot_id = $request->time;
        $order->save();
        flash()->success(translate('Order TimeSlot updated!'));
        return back();
    }


    /**
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function addServiceman(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'service_item' => 'required',
            'service_man' => 'required',
        ]);

        $order = $this->order_detail->where('id', $request->service_item)->with('OrderDetails')->first();
        
        if ($order->OrderDetails->order_status == 'delivered' || $order->OrderDetails->order_status == 'returned' || $order->OrderDetails->order_status == 'failed' || $order->OrderDetails->order_status == 'canceled' || $order->OrderDetails->order_status == 'rejected') {
            flash()->warning(translate('Can Not Assign Service Man on '. $order->OrderDetails->order_status .' order'));
            return response()->json(['status' => false], 200);
        }

        $order->service_man_id = $request->service_man;
        $order->save();
        
        // $notifications = new Notifications();
        // $notifications->type = 0;
        // $notifications->user_id = $order->OrderDetails->user_id;
        // $notifications->title = 'Service Man Assigned';
        // $notifications->description = Vendor::where('id', $request->service_man)->first()->name.' Assigned for '. Products::where('id', $order->product_id)->first()->name;
        // $notifications->save();

        $notifications = new Notifications();
        $notifications->type = 1;
        $notifications->user_id = $request->service_man;
        $notifications->title = 'New Order Assigned';
        $notifications->description = Products::where('id', $order->product_id)->first()->name. ' Installation has been assigned to you';
        $notifications->save();

        // $deliverymanMessage = Helpers_order_status_update_message('del_assign');
        // $deliverymanLanguageCode = $order->delivery_man ? $order->delivery_man->language_code : 'en';
        // $deliverymanFcmToken = $order->delivery_man ? $order->delivery_man->fcm_token : null;
        
        // $value = $this->dynamic_key_replaced_message(message: $deliverymanMessage, type: 'order', order: $order);

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

        //         $customerNotifyMessage = Helpers_order_status_update_message('customer_notify_message');
        //         $customerLanguageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
        //         $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
                
        //         $value = $this->dynamic_key_replaced_message(message: $customerNotifyMessage, type: 'order', order: $order);

        //         if ($customerNotifyMessage) {
        //             $data['description'] = $value;
        //             Helpers_send_push_notif_to_device($customerFcmToken, $data);
        //         }
        //     }
        // } catch (\Exception $e) {
        //     flash()->warning(translate('Push notification failed for DeliveryMan!'));
        // }

        flash()->success('Service Man successfully assigned/changed!');
        return response()->json(['status' => true], 200);
    }

    /**
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function addDeliveryman(Request $request, $order_id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'delivery_man' => 'required',
        ]);

        $order = $this->order->where('id',$order_id)->with('OrderDetails')->first();
        if ($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled' || $order->order_status == 'rejected') {
            flash()->warning(translate('Can Not Assign Service Man on '. $order->order_status .' order'));
            return response()->json(['status' => false], 200);
        }
        
        $order->deliveryman_id = $request->delivery_man;
        if($request->price != null){
            $order->order_amount = $request->price;
        }
        $order->save();
        
        // $notifications = new Notifications();
        // $notifications->type = 0;
        // $notifications->user_id = $order->OrderDetails->user_id;
        // $notifications->title = 'Service Man Assigned';
        // $notifications->description = Vendor::where('id', $request->service_man)->first()->name.' Assigned for '. Products::where('id', $order->product_id)->first()->name;
        // $notifications->save();

        $notifications = new Notifications();
        $notifications->type = 1;
        $notifications->user_id = $request->delivery_man;
        $notifications->title = 'New Order Assigned for Delivery';
        $notifications->description = 'new delivery has been assigned to you';
        $notifications->save();

        // $deliverymanMessage = Helpers_order_status_update_message('del_assign');
        // $deliverymanLanguageCode = $order->delivery_man ? $order->delivery_man->language_code : 'en';
        // $deliverymanFcmToken = $order->delivery_man ? $order->delivery_man->fcm_token : null;
        
        // $value = $this->dynamic_key_replaced_message(message: $deliverymanMessage, type: 'order', order: $order);

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

        //         $customerNotifyMessage = Helpers_order_status_update_message('customer_notify_message');
        //         $customerLanguageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
        //         $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
                
        //         $value = $this->dynamic_key_replaced_message(message: $customerNotifyMessage, type: 'order', order: $order);

        //         if ($customerNotifyMessage) {
        //             $data['description'] = $value;
        //             Helpers_send_push_notif_to_device($customerFcmToken, $data);
        //         }
        //     }
        // } catch (\Exception $e) {
        //     flash()->warning(translate('Push notification failed for DeliveryMan!'));
        // }

        flash()->success('Delivery Man successfully assigned/changed!');
        return response()->json(['status' => true], 200);
    }
    
    /**
     * @param Request $request
     * @param $status
     * @return JsonResponse
     */
    public function UpdateServiceMen($delivery_man_id, $order_id): JsonResponse
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }

        $order = $this->order->find($order_id);

        if ($order->order_status == 'pending' || $order->order_status == 'confirmed' || $order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
            return response()->json(['status' => false], 200);
        }

        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $deliverymanMessage = Helpers_order_status_update_message('del_assign');
        $deliverymanLanguageCode = $order->delivery_man ? $order->delivery_man->language_code : 'en';
        $deliverymanFcmToken = $order->delivery_man ? $order->delivery_man->fcm_token : null;
        $value = $this->dynamic_key_replaced_message(message: $deliverymanMessage, type: 'order', order: $order);

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];
                Helpers_send_push_notif_to_device($deliverymanFcmToken, $data);

                $customerNotifyMessage = Helpers_order_status_update_message('customer_notify_message');
                $customerLanguageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
                $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
                
                $value = $this->dynamic_key_replaced_message(message: $customerNotifyMessage, type: 'order', order: $order);

                if ($customerNotifyMessage) {
                    $data['description'] = $value;
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                }
            }
        } catch (\Exception $e) {
            flash()->warning(translate('Push notification failed for DeliveryMan!'));
        }

        flash()->success('Deliveryman successfully assigned/changed!');
        return response()->json(['status' => true], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function status(Request $request): JsonResponse|RedirectResponse
    {
        $order = $this->order->where('id',$request->id)->with('OrderDetails')->first();

        if (in_array($order->order_status, ['returned', 'delivered', 'failed', 'canceled', 'rejected'])) {
            flash()->warning(translate('you_can_not_change_the_status_of ' . $order->order_status . ' order'));
            return back();
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
        
        if ($request->order_status == 'packaging') 
        {
            foreach ($order->OrderDetails as $detail) 
            {
                if ($detail['is_stock_decreased'] == 1) {

                    $product = $this->product->find($detail['product_id']);

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
                        $this->product->where(['id' => $product['id']])->update([
                            'variations' => json_encode($variationStore),
                            'total_stock' => $product['total_stock'] - $detail['quantity'],
                            'total_sale' => $product['total_sale'] + $detail['quantity'],
                        ]);
                        $this->order_detail->where(['id' => $detail['id']])->update([
                            'is_stock_decreased' => 0,
                        ]);
                    } else {
                        flash()->warning(translate('Product_deleted'));
                        return response()->json(['status' => true]);
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
            if ($order['delivery_date'] == 'null' || $order['delivery_timeslot_id'] == 'null') 
            {
                flash()->warning(translate('Please assign delivery Information first!'));
                return response()->json(['status' => true]);
            }

            // if (!BusinessSetting::where(['key' => 'out_for_delivery_message'])->first()) {
            //     BusinessSetting::updateOrInsert(['key' => 'out_for_delivery_message'], [
            //         'value' => json_encode([
            //             'status'  => 0,
            //             'message' => 'Order Out for Delivery',
            //         ]),
            //     ]);
            // }
            
            // $notifications = new Notifications();
            // $notifications->user_id = $order->user_id;
            // $notifications->title = helpers_get_business_settings('out_for_delivery_message')['message'];
            // $notifications->description = 'Your Order No. '.$order->id.' out for delivery';
            // $notifications->save();

            $otp = rand(1000, 9999);
            $verification = new OrderOTPS();
            $verification->order_id = $order['id'];
            $verification->otp = $otp;
            $verification->otp_for = 'delivery_reached';
            $verification->save();

            return response()->json([
                'status' => true,
                'otp' => $otp,
                'message' => 'otp sended'
            ]);
        }

        if ($request->order_status == 'delivered' && $order['payment_status'] != 'paid') 
        {
            flash()->warning(translate('you_can_not_delivered_a_order_when_order_status_is_not_paid. please_update_payment_status_first'));
            return response()->json(['status' => true]);
        }

        if ($request->order_status == 'delivered') 
        {
            if($order['delivery_date'] == 'null' && $order['delivery_timeslot_id'] == 'null' && $order['delivery_man_id'] == 'null')
            {
                flash()->warning(translate('Please assign delivery Information first!'));
                return response()->json(['status' => true]);
            }

            foreach ($order->OrderDetails as $key => $value) {
                if($value['installation'] == 0 && $value['service_man_id'] != null)
                {
                    flash()->warning(translate('Please assign Service and Installation Information first!'));
                    return response()->json(['status' => true]);
                }
            }

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
        if ($request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled') {
            
            foreach ($order->OrderDetails as $detail) {
                if (!isset($detail->variant)) {
                    if ($detail['is_stock_decreased'] == 0) {
                        $product = $this->product->find($detail['product_id']);
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
                            $this->product->where(['id' => $product['id']])->update([
                                'variations' => json_encode($variationStore),
                                'total_stock' => $product['total_stock'] + $detail['quantity'],
                                'total_sale' => $product['total_sale'] - $detail['quantity'],
                            ]);
                            $this->order_detail->where(['id' => $detail['id']])->update([
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

        flash()->success(translate('Order status updated!'));
        return response()->json(['status' => true]);
    }

/**
     * @param Request $request
     * @return JsonResponse
     */
    public function statusService(Request $request): \Illuminate\Http\JsonResponse
    {
        $order = $this->order->where('id',$request->id)->with('OrderDetails')->first();

        if (in_array($order->order_status, ['delivered', 'canceled', 'rejected'])) {
            flash()->warning(translate('you_can_not_change_the_status_of ' . $order->order_status . ' order'));
            return back();
        }

        //refund amount to wallet
        if (in_array($request['order_status'], ['canceled', 'rejected'])) 
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
            
            $notifications = new Notifications();
            $notifications->user_id = $order->user_id;
            $notifications->title = 'Order Rejected';
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
        
        //editable
        if ($request->order_status == 'out_for_delivery') 
        {
            if ($order['delivery_date'] == 'null' || $order['delivery_timeslot_id'] == 'null') 
            {
                flash()->warning(translate('Please assign delivery Information first!'));
                return response()->json(['status' => true]);
            }

            $notifications = new Notifications();
            $notifications->user_id = $order->user_id;
            $notifications->title = 'Order Out for Delivery';
            $notifications->description = 'Your Order No. '.$order->id.' out for delivery';
            $notifications->save();
        }

        if ($request->order_status == 'delivered' && $order['payment_status'] != 'paid') 
        {
            flash()->warning(translate('you_can_not_delivered_a_order_when_order_status_is_not_paid. please_update_payment_status_first'));
            return response()->json(['status' => true]);
        }

        if ($request->order_status == 'delivered') 
        {
            if($order['delivery_date'] == 'null' && $order['delivery_timeslot_id'] == 'null' && $order['delivery_man_id'] == 'null')
            {
                flash()->warning(translate('Please assign delivery Information first!'));
                return response()->json(['status' => true]);
            }

            foreach ($order->OrderDetails as $key => $value) {
                if($value['installation'] == 0 && $value['service_man_id'] != null)
                {
                    flash()->warning(translate('Please assign Service and Installation Information first!'));
                    return response()->json(['status' => true]);
                }
            }

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

            $notifications = new Notifications();
            $notifications->user_id = $order->user_id;
            $notifications->title = 'Order Delivered';
            $notifications->description = 'Your Order No. '.$order->id.' Delivered';
            $notifications->save();
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

        flash()->success(translate('Order status updated!'));
        return response()->json(['status' => true]);
    }




























    /**
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function edit_item($id): Factory|View|Application|RedirectResponse
    {
        $order = $this->order->with(['details', 'offline_payment'])->where(['id' => $id])->first();
        // $deliverymanList = $this->delivery_man->where(['is_active' => 1])
            // ->where(function ($query) use ($order) {
            //     $query->where('branch_id', $order->branch_id)
            //         ->orWhere('branch_id', 0);
            // })->get();

        $all_product = Products::all();
        if (isset($order)) {
            return view('Admin.views.order.edit-order', compact('order', 'deliverymanList', 'all_product'));
        } else {
            flash()->info(translate('No more orders!'));
            return back();
        }
    }

    /**
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function edit_item_submit(Request $request, $id): Factory|View|Application|RedirectResponse
    {
        // dd(452 * (56 / 100));
        if (isset($request->direct)) {
            $amount = 0;
            $total_distribute = 0;
            $total_tax = 0;
            $order = $this->order->find($id);
            $status = $order->status;
            if ($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
                flash()->warning(translate('Order_can_not_edited_because_of its_status'));
                return back();
            }

            if($order->payment_method != 'cash_on_delivery'){
                flash()->warning(translate('Order_can_not_edited_directly_when_its_not_cash_on_delivery'));
                return back();
            }

            foreach ($order->details as $key => $value) {

                if ($value->product_id == $request->product[$key]) {

                    if($request->alternate[$key] != 0){

                        $alt_product_detail = Products::find($request->alternate[$key]);

                        $price = $alt_product_detail->price;
                        $tax_on_product = Helpers_tax_calculate($alt_product_detail, $price);

                        $category_id = null;
                        foreach (json_decode($alt_product_detail['category_ids'], true) as $cat) {
                            if ($cat['position'] == 1){
                                $category_id = ($cat['id']);
                            }
                        }

                        $category_discount = Helpers_category_discount_calculate($category_id, $price);
                        $product_discount = Helpers_discount_calculate($alt_product_detail, $price);
                        if ($category_discount >= $price){
                            $discount = $product_discount;
                            $discount_type = 'discount_on_product';
                        }else{
                            $discount = max($category_discount, $product_discount);
                            $discount_type = $product_discount > $category_discount ? 'discount_on_product' : 'discount_on_category';
                        }

                        // $distribute_on_product = Helpers_distribute_calculate($alt_product_detail, $price);


                        $value->product_id = $request->alternate[$key];
                        $value->quantity = $request->alternate_qyt[$key];
                        $value->product_details = $alt_product_detail;
                        $value->price = $price;
                        $value->discount_on_product = $discount;
                        $value->discount_type = $discount_type;
                        $value->tax_amount = $tax_on_product;
                        // $value->distributed_amount = $distribute_on_product;
                        $value->save();

                        // $total_distribute = $total_distribute + $distribute_on_product;
                        $total_tax = $total_tax + $tax_on_product;

                        $amount_price = $price * $request->alternate_qyt[$key];
                        $amount = $amount + $amount_price;
                    }else{

                        $total_distribute = $total_distribute + $value->distributed_amount;
                        $total_tax = $total_tax + $value->tax_amount;

                        $amount_price = $value->price * $value->quantity;
                        $amount = $amount + $amount_price;

                    }
                }
            }


            $order->order_amount = $amount;
            $order->total_distributed_amount = $total_distribute;
            $order->total_tax_amount = $amount;
            $order->save();
            

            try {

                $customerNotifyMessage = 'Your Order Is Edit Because Out of stock Issue! See This new Items';

                $customerLanguageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
                $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
                
                $value = $this->dynamic_key_replaced_message(message: $customerNotifyMessage, type: 'order', order: $order);

                $data = [
                    'title' => translate('Order Edited'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];

                if ($customerNotifyMessage) {
                    $data['description'] = $value;
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                }
            } catch (\Exception $e) {
                flash()->warning(translate('Notification failed for Customer!'));
            }

            flash()->success(translate('Order Edited Successfully!'));
            return redirect(route('admin.orders.list', 'all'));


        } else {

            // Response

            $order = $this->order->find($id);
            $status = $order->status;
            if ($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
                flash()->warning(translate('Order_can_not_edited_because_of its_status'));
                return back();
            }

            $order->editable = 1;
            $order->edit_status = 'pending';

            foreach ($order->details as $key => $value) {
                if ($value->product_id == $request->product[$key]) {
                    $value->alt_product_id = $request->alternate[$key];
                    $value->alt_product_qyt = $request->alternate_qyt[$key];
                    if($request->alternate[$key] != 0){
                        $value->alt_product_status = 'pending';
                    }
                    $product_detail = Products::find($request->alternate[$key]);
                    $value->alt_product_details = $product_detail;
                    $value->save();
                }
            }

            $order->save();

            try {

                $customerNotifyMessage = 'Your Order Is Edit Because Out of stock Issue! See This new Item and Accept it';

                $customerLanguageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
                $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
                
                $value = $this->dynamic_key_replaced_message(message: $customerNotifyMessage, type: 'order', order: $order);

                $data = [
                    'title' => translate('Order Edited'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];

                if ($customerNotifyMessage) {
                    $data['description'] = $value;
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                }
            } catch (\Exception $e) {
                flash()->warning(translate('Notification failed for Customer!'));
            }

            flash()->success(translate('Order Edited Successfully!'));
            return redirect(route('admin.orders.list', 'all'));
        }
    }

    

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateShipping(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'road' => $request->road,
            'house' => $request->house,
            'floor' => $request->floor,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        flash()->success(translate('Delivery Information updated!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function generateInvoice($id): View|Factory|Application
    {
        $order = $this->order->where('id', $id)->first();
        $footer_text = $this->business_setting->where(['key' => 'footer_text'])->first();
        return view('Admin.views.order.invoice', compact('order', 'footer_text'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function addPaymentReferenceCode(Request $request, $id): RedirectResponse
    {
        $this->order->where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference'],
        ]);

        flash()->success(translate('Payment reference code is added!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $status
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportOrders(Request $request, $status): StreamedResponse|string
    {
        $queryParam = [];
        $search = $request['search'];
        $branchId = $request['branch_id'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $query = $this->order->with(['customer', 'branch'])
            ->when((!is_null($branchId) && $branchId != 'all'), function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            });

        if ($status != 'all') {
            $query->where(['order_status' => $status]);
        }

        $queryParam = ['branch_id' => $branchId, 'start_date' => $startDate, 'end_date' => $endDate];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "{$value}%");
                }
            });
            $queryParam['search'] = $search;
        }

        $orders = $query->notPos()->orderBy('id', 'desc')->get();

        $storage = [];
        foreach ($orders as $order) {
            $branch = $order->branch ? $order->branch->name : '';
            $customer = $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'Customer Deleted';
            $deliveryman = $order->delivery_man ? $order->delivery_man->f_name . ' ' . $order->delivery_man->l_name : '';
            $timeslot = $order->time_slot ? $order->time_slot->start_time . ' - ' . $order->time_slot->end_time : '';

            $storage[] = [
                'order_id' => $order['id'],
                'customer' => $customer,
                'order_amount' => $order['order_amount'],
                'coupon_discount_amount' => $order['coupon_discount_amount'],
                'payment_status' => $order['payment_status'],
                'order_status' => $order['order_status'],
                'total_tax_amount' => $order['total_tax_amount'],
                'payment_method' => $order['payment_method'],
                'transaction_reference' => $order['transaction_reference'],
                'delivery_man' => $deliveryman,
                'delivery_charge' => $order['delivery_charge'],
                'coupon_code' => $order['coupon_code'],
                'order_type' => $order['order_type'],
                'branch' =>  $branch,
                'time_slot_id' => $timeslot,
                'date' => $order['date'],
                'delivery_date' => $order['delivery_date'],
                'extra_discount' => $order['extra_discount'],
            ];
        }
        return (new FastExcel($storage))->download('orders.xlsx');
    }

    public function ProductReplaceAjax(Request $request)
    {
        $request->validate([
            'data' => 'required'
        ]);

        $product = Products::where('id', $request->data)->first();

        $product['fullpath'] = $product->identityImageFullPath[0];

        return $product;
    }

    public function ProductDeleteAjax(Request $request)
    {
        $request->validate([
            'data' => 'required'
        ]);

        $order_detail = Order_details::where('id', $request->data)->first();
        $order = Order::find($order_detail->order_id);

        $qyt = $order_detail->quantity;
        $tax = $order_detail->tax_amount * $qyt;
        $price = $order_detail->price * $qyt;

        $order->order_amount = $order->order_amount - $price;
        $order->total_tax_amount = $order->total_tax_amount - $tax;

        $order->save();

        Order_details::where('id', $request->data)->first()->delete();

        return response()->json(['success' => true]);
    }

    public function dynamic_key_replaced_message($message, $type, $order = null, $customer = null)
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

    




















    /**
     * @param $order_id
     * @param $status
     * @return JsonResponse
     */
    public function verifyOfflinePayment($order_id, $status): JsonResponse
    {
        $offlineData = OfflinePayment::where(['order_id' => $order_id])->first();

        if (!isset($offlineData)) {
            return response()->json(['status' => false, 'message' => translate('offline data not found')], 200);
        }

        $order = Order::find($order_id);
        if (!isset($order)) {
            return response()->json(['status' => false, 'message' => translate('order not found')], 200);
        }

        if ($status == 1) {
            if ($order->order_status == 'canceled') {
                return response()->json(['status' => false, 'message' => translate('Canceled order can not be verified')], 200);
            }

            $offlineData->status = $status;
            $offlineData->save();

            $order->order_status = 'confirmed';
            $order->payment_status = 'paid';
            $order->save();

            $message = Helpers_order_status_update_message('confirmed');
            $languageCode = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
            $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
            
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
                //
            }
        } elseif ($status == 2) {
            $offlineData->status = $status;
            $offlineData->save();

            $customerFcmToken = null;
            if ($order->is_guest == 0) {
                $customerFcmToken = $order->customer ? $order->customer->cm_firebase_token : null;
            } elseif ($order->is_guest == 1) {
                $customerFcmToken = $order->guest ? $order->guest->fcm_token : null;
            }
            if ($customerFcmToken != null) {
                try {
                    $data = [
                        'title' => translate('Order'),
                        'description' => translate('Offline payment is not verified'),
                        'order_id' => $order->id,
                        'image' => '',
                        'type' => 'order',
                    ];
                    Helpers_send_push_notif_to_device($customerFcmToken, $data);
                } catch (\Exception $e) {
                }
            }
        }
        return response()->json(['status' => true, 'message' => translate("offline payment verify status changed")], 200);
    }
}
