<?php

namespace App\Http\Controllers\Api\user\amc;

use App\Http\Controllers\Controller;
use App\Models\{
    AMCPlan,
    AMCPlanServices,
    BusinessSetting,
    CouponApplied,
    CustomerAddresses,
    Notifications,
    Order,
    Order_details,
    Service
};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * 
     * @return JsonResponse
     */
    public function Checkout() : JsonResponse
    {
        try {
            $cod = Helpers_get_business_settings('cash_on_delivery');
            if($cod['status'] == 0)
            {
                $cod['max_cod_status'] = Helpers_get_business_settings('maximum_amount_for_cod_order_status');
                $cod['max_cod_amount'] = Helpers_get_business_settings('maximum_amount_for_cod_order');
            }

            $digital_payment = Helpers_get_business_settings('digital_payment');
            $partial_payment = Helpers_get_business_settings('partial_payment');
            
            $delivery['free_delivery_over_amount_status'] = Helpers_get_business_settings('free_delivery_over_amount_status');
            if(!is_null($delivery['free_delivery_over_amount_status']) && $delivery['free_delivery_over_amount_status'] == 0)
            {
                $delivery['free_delivery_over_amount'] = Helpers_get_business_settings('free_delivery_over_amount');
            }
        } catch (\Throwable $th) {
            $cod = [
                "status" => 1
            ];
            $delivery = [];
        }

        return response()->json([
            'status' => true,
            'message' => [
                'Active' => 0,
                'Inactive' => 1,
            ],
            'data' => [
                'wallet' => Auth::user()->wallet_balance,
                'cod' => $cod,
                'digital_payment' => $digital_payment,
                'partial_payment' => $partial_payment,
                'tax' => Helpers_get_business_settings('product_gst_tax_status'),
            ]
        ], 200);
    }

    /**
     * @param Request $request
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
            'coupon_applied' => 'required|in:0,1',
            'coupon_amount' => 'required|numeric|min:0',
            'coupon_code' => 'nullable',
            'plan_id' => 'required|numeric',
            'tax' => 'required|numeric',
            'discount' => 'required|numeric',
            'tax_type' => 'required|in:included,excluded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ],406);
        }

        if(Order::exists())
        {
            $id = Order::max('id') + 1;
        }else{
            $id = 100001;
        }

        $plan = AMCPlan::find($request->plan_id);
        
        $adminOrder = new Order();
        $adminOrder->id = $id;
        $adminOrder->user_id = Auth::user()->id;
        $adminOrder->order_type = 'amc';
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
        $adminOrder->checked = 1;
        $adminOrder->date = now();
        $adminOrder->delivery_date = $request->date;
        $adminOrder->delivery_timeslot_id = $request->time;
        $adminOrder->tax_type = $request->tax_type;
        $adminOrder->delivery_address = json_encode(CustomerAddresses::find($request->address_id));
        $adminOrder->total_tax_amount = $request->tax;
        $adminOrder->total_discount = $request->discount;
        $adminOrder->item_total = $request->price;
        $adminOrder->plan_id = $request->plan_id;
        $adminOrder->plan_activate = 1;
        if($request->coupon_applied == 0)
        {
            $adminOrder->coupon_amount = $request->coupon_amount;
            $adminOrder->coupon_code = $request->coupon_code;
        }
        if($request->tax_type == 'excluded')
        {
            $grand_total = ($request->price + $request->tax) - $request->discount;
        }else{
            $grand_total = $request->price - $request->discount;
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

        $plan_details = AMCPlanServices::where('plan_id', $request->plan_id)->get();

        foreach ($plan_details as $key => $value) 
        {
            $order_details = new Order_details();
            $order_details->order_id = $adminOrder->id;
            $order_details->product_id = $value->id;
            $order_details->product_details = json_encode($value);
            $order_details->quantity = $value->quantity;
            $order_details->save();
        }
        
        if($request->coupon_applied == 0)
        {
            $code = new CouponApplied();
            $code->user_id = Auth::user()->id;
            $code->coupon_code = $request->coupon_code;
            $code->save();
        }

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
     * @param Request $request
     * @return JsonResponse
     */
    public function BookOrder(Request $request) : JsonResponse
    {
        try {
            $order = Helpers_Orders_formatting(Order::where('id', $request->id)->with('OrderDetails')->first(), false, true, false);
            return response()->json([
                'status' => true,
                'message' => 'Order Status',
                'data' => $order
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ],408);
        }
    }
}
