<?php

namespace App\Http\Controllers\Api\user\service;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\CustomerAddresses;
use App\Models\Notifications;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Service;
use App\Models\ServiceReview;
use App\Models\ServiceTimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSetting,
        private Order $order,
        private CustomerAddresses $customeraddress,
        private Service $service,
        private ServiceReview $servicereview,
    ){}

    /**
     * @param $id
     * @return JsonResponse
     */
    public function CheckOut($id) : JsonResponse
    {
        try {
            $service = Service::where('id' , $id)->with('Subcategory')->first();

            $timeslot = ServiceTimeSlot::select('id','time')->where('status' , 1)->orderby('priority' , 'asc')->get();

            $cod = Helpers_get_business_settings('cash_on_delivery');
            if($cod['status'] == 0)
            {
                $cod['max_cod_status'] = Helpers_get_business_settings('maximum_amount_for_cod_order_status');
                $cod['max_cod_amount'] = Helpers_get_business_settings('maximum_amount_for_cod_order');
            }
            $digital_payment = Helpers_get_business_settings('digital_payment');
            $partial_payment = Helpers_get_business_settings('partial_payment');

            return response()->json([
                'status' => true,
                'message' => [
                    'Active' => 0,
                    'Inactive' => 1,
                ],
                'data' => [
                    'service' => $service,
                    'timeslot' => $timeslot,
                    'cod' => $cod,
                    'digital_payment' => $digital_payment,
                    'partial_payment' => $partial_payment,
                    'balance' => Auth::user()->wallet_balance,
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'data' => []
            ], 401);
        }
    }

    /**
     * Request $request
     * @return JsonResponse
     */
    public function PlaceOrder(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|numeric',
            'partial_payment' => 'required|numeric',
            'wallet_applied' => 'required|numeric',
            'payment_method' => 'required',
            'payment_by' => 'required',
            'transaction_reference' => 'required',
            'due_amount' => 'required|numeric',
            'date' => 'required|date',
            'time' => 'required|numeric',
            'services.id' => 'required|numeric',
            'services.fees' => 'required|numeric',
            'services.discount' => 'required|numeric',
            'services.tax' => 'required|numeric',
            'services.tax_type' => 'required|in:included,excluded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ],406);
        }

        if($this->order->exists())
        {
            $id = $this->order->max('id') + 1;
        }else{
            $id = 100001;
        }

        $service = $this->service->find($request->services['id']);
        
        $adminOrder = new Order();
        $adminOrder->id = $id;
        $adminOrder->user_id = Auth::user()->id;
        $adminOrder->order_type = 'service';
        $adminOrder->order_status = 'pending';
        $adminOrder->order_approval = 'pending';
        $adminOrder->payment_method = $request->payment_method;
        if($request->payment_method == 'digital_payment')
        {
            $adminOrder->payment_by = $request->payment_by;
            $adminOrder->transaction_reference = $request->transaction_reference;
            $adminOrder->payment_status = 'paid';
        }
        $adminOrder->delivery_address_id = $request->address_id;
        // $adminOrder->free_delivery = $request->free_delivery;
        // $adminOrder->vender_id = $key;
        // $adminOrder->delivered_by = 0;
        $adminOrder->checked = 1;
        $adminOrder->date = now();
        $adminOrder->delivery_date = $request->date;
        $adminOrder->delivery_timeslot_id = $request->time;
        $adminOrder->tax_type = $request->services['tax_type'];
        $adminOrder->delivery_address = json_encode($this->customeraddress->find($request->address_id));
        // $adminOrder->gst_invoice = $request->gst_invoice;
        // if($request->gst_invoice == 0)
        // {
        //     $adminOrder->gst_no = $request->gst_no;
        //     $adminOrder->gst_name = $request->gst_name; 
        //     $adminOrder->mobile_no = $request->mobile_no;
        // }

        $adminOrder->total_tax_amount = $request->services['tax_type'];
        $adminOrder->delivery_charge = $request->services['fees'];
        $adminOrder->total_discount = $request->services['discount'];
        // $adminOrder->total_installation	 = $installation;
        $adminOrder->item_total = $service->price;

        if($request->tax_type == 'excluded')
        {
            $grand_total = ($service->price + $request->services['tax'] + $request->services['fees']) - $request->services['discount'];
        }else{
            $grand_total = ($service->price + $request->services['fees']) - $request->services['discount'];
        }
        $adminOrder->order_amount = $grand_total;

        if($request->partial_payment == 0)
        {
            $adminOrder->partial_payment = json_encode([
                'wallet_applied' => $request->wallet_applied
            ]);

            Helpers_generate_wallet_transaction(Auth::user()->id, $id ,'Order_Place' , $request->wallet_applied ,0 , Auth::user()->wallet_balance);
        }else{
            if($request->payment_method == 'wallet_amount')
            {
                $adminOrder->partial_payment = json_encode([
                    'wallet_applied' => $request->wallet_applied
                ]);
                Helpers_generate_wallet_transaction(Auth::user()->id, $id ,'Order_Place' , $request->wallet_applied ,0 , Auth::user()->wallet_balance);
                $adminOrder->payment_status = 'paid';
            }
        }

        $adminOrder->save();

        

        $order_details = new Order_details();
        $order_details->order_id = $adminOrder->id;
        $order_details->product_id = $service->id;
        $order_details->product_details = json_encode($service);
        $order_details->price = $service->price;
        $order_details->quantity = 0;
        // $order_details->variation = json_encode($value['selectedvariation']);
        // $order_details->unit = $product->unit;
        // $order_details->is_stock_decreased = 1;
        // if($value['is_installation'] == 0)
        // {
        //     $order_details->installastion_amount = $product->installation_charges;
        //     $order_details->installation = $value['is_installation'];
        //     $installation += $product->installation_charges;
        // }
        $order_details->discount_on_product = $request->services['discount'];
        $order_details->delivery_charges = $request->services['fees'];
        $order_details->gst_status = $request->services['tax_type'];
        $order_details->tax_amount = $request->services['tax'];
        $order_details->save();
        

        if (!BusinessSetting::where(['key' => 'order_place_message'])->first()) {
            BusinessSetting::updateOrInsert(['key' => 'order_place_message'], [
                'value' => json_encode([
                    'status'  => 0,
                    'message' => 'Order Placed Successfully',
                ]),
            ]);
        }

        $notifications = new Notifications();
        $notifications->type = 0;
        $notifications->user_id = Auth::user()->id;
        $notifications->title = helpers_get_business_settings('order_place_message')['message'];
        $notifications->description = 'Your Order No. '.$adminOrder->id.' Generated Successfully Approval Pending';
        $notifications->save();
        
        return response()->json([
            'status' => true,
            'message' => 'Order Placed Successfully',
            'data' => $adminOrder->id
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function OrderHistory()
    {
        try {
            $order = Helpers_Orders_formatting(Order::where(['order_type' => 'service','user_id' => Auth::user()->id])->orderBy('id', 'desc')->get(), true, true, false);
            return response()->json([
                'status' => true,
                'message' => 'Order History',
                'data' => $order
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ],401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function OrderItems($id)
    {
        try {
            $order = Helpers_Orders_formatting(Order::where('id', $id)->with('OrderDetails')->first(), false, true, false);
            return response()->json([
                'status' => true,
                'message' => 'Order Status',
                'data' => $order
            ],200);
    } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ],401);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function OrderProductReview(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        
        try {
            $review = $this->servicereview;
            $review->service_id = $request->service_id;
            $review->user_id = Auth::user()->id;
            $review->order_id = $request->order_id;
            $review->comment = $request->rating;
            $review->rating = $request->comment;
            $review->save();
            return response()->json([
                'status' => true,
                'message' => 'Service Review created successfully',
                'data' => [],
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'errors' => 'Unexpected Error '.$th->getMessage(),
            ], 401);
        }
    }
}
