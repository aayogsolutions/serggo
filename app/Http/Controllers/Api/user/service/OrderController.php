<?php

namespace App\Http\Controllers\Api\user\service;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Notifications;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSetting,
    ){}

    /**
     * 
     * @return JsonResponse
     */
    public function CheckOut() : JsonResponse
    {
        try {
            $id = Auth::user()->id;

            $cod = Helpers_get_business_settings('cash_on_delivery');
            if($cod['status'] == 0)
            {
                $cod['max_cod_status'] = Helpers_get_business_settings('maximum_amount_for_cod_order_status');
                $cod['max_cod_amount'] = Helpers_get_business_settings('maximum_amount_for_cod_order');
            }
            $digital_payment = Helpers_get_business_settings('digital_payment');
            $partial_payment = Helpers_get_business_settings('partial_payment');

        } catch (\Throwable $th) {
            $cod = [
                "status" => 1
            ];
            $digital_payment = 1;
            $partial_payment = 1;
        }
        
        

        return response()->json([
            'status' => true,
            'message' => [
                'Active' => 0,
                'Inactive' => 1,
            ],
            'data' => [
                'cod' => $cod,
                'digital_payment' => $digital_payment,
                'partial_payment' => $partial_payment,
                'balance' => Auth::user()->wallet_balance,
            ]
        ], 200);
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
            'services.*.id' => 'required|numeric',
            'services.*.quantity' => 'required|numeric',
            'services.*.discount' => 'required|numeric',
            'services.*.tax' => 'required|numeric',
            'services.*.tax_type' => 'required|in:included,excluded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ],406);
        }

        
        $adminOrder = new Order();
        $adminOrder->id = $id;
        $adminOrder->user_id = Auth::user()->id;
        $adminOrder->order_type = 'goods';
        $adminOrder->order_status = 'pending';
        $adminOrder->order_approval = 'pending';
        $adminOrder->payment_method = $request->payment_method;
        if($request->payment_method == 'digital_payment')
        {
            $adminOrder->payment_by = $request->payment_by;
            $adminOrder->transaction_reference = $request->transaction_reference;
        }
        $adminOrder->payment_method = $request->payment_method;
        $adminOrder->delivery_address_id = $request->address_id;
        $adminOrder->free_delivery = $request->free_delivery;
        $adminOrder->vender_id = $key;
        $adminOrder->delivered_by = Vendor::find($key)->delivery_choice == 0 ? 1 : 0 ;
        $adminOrder->checked = 1;
        $adminOrder->date = now();
        $adminOrder->delivery_address = json_encode($this->customeraddress->find($request->address_id));
        $adminOrder->gst_invoice = $request->gst_invoice;
        if($request->gst_invoice == 0)
        {
            $adminOrder->gst_no = $request->gst_no;
            $adminOrder->gst_name = $request->gst_name; 
            $adminOrder->mobile_no = $request->mobile_no;
        }
        $adminOrder->save();

        $tax_amount = 0;
        $amount = 0;
        $delivery = 0;
        $discount = 0;
        $installation = 0;
        $partial_payment_amount = 0;
        $tax_type = $request->tax_type;
        foreach ($products as $key1 => $value) 
        {
            $product = $this->product->find($value['id']);

            if($value['selectedvariation'] != null)
            {
                $price = $value['selectedvariation']['price'];
            }else{
                $price = $product->price;
            }

            $order_details = new Order_details();
            $order_details->order_id = $adminOrder->id;
            $order_details->product_id = $product->id;
            $order_details->product_details = json_encode($product);
            $order_details->price = $price;
            $order_details->quantity = $value['quantity'];
            $order_details->variation = json_encode($value['selectedvariation']);
            $order_details->unit = $product->unit;
            $order_details->discount_on_product = $value['discount'];
            $order_details->delivery_charges = $value['delivery'];
            $order_details->is_stock_decreased = 1;
            if($value['is_installation'] == 0)
            {
                $order_details->installastion_amount = $product->installation_charges;
                $order_details->installation = $value['is_installation'];
                $installation += $product->installation_charges;
            }
            if($tax_type == null || $tax_type == 'excluded')
            {
                $order_details->gst_status = 'excluded';
            }else{
                $order_details->gst_status = 'included';
            }
            $order_details->tax_amount = $value['tax'];
            $order_details->save();

            $tax_amount += $value['tax'] * $value['quantity'];
            $amount += $price * $value['quantity'];
            $delivery = $value['delivery'];
            $discount += $value['discount'] * $value['quantity'];
        }

        $adminOrder = $this->order->find($adminOrder->id);
        
        $adminOrder->total_tax_amount = $tax_amount;
        $adminOrder->delivery_charge = $delivery;
        $adminOrder->total_discount = $discount;
        $adminOrder->total_installation	 = $installation;
        $adminOrder->item_total = $amount;
        if($request->free_delivery == 0)
        {
            $adminOrder->free_delivery_amount = $delivery;
        }
        if($request->tax_type == 'excluded')
        {
            $grand_total = ($amount + $tax_amount + ($request->free_delivery == 1 ? $delivery : 0) + $installation) - $discount;
        }else{
            $grand_total = ($amount + ($request->free_delivery == 1 ? $delivery : 0) + $installation) - $discount;
        }
        $adminOrder->order_amount = $grand_total;

        if($request->partial_payment == 0)
        {
            $per_order_wallet = (count($orderedproducts) - $no_of_order) == 0 ? $remaining_wallet_amount : $remaining_wallet_amount / (count($orderedproducts) - $no_of_order);

            dump($per_order_wallet ,(count($orderedproducts) - $no_of_order) , $remaining_wallet_amount , $remaining_wallet_amount / (count($orderedproducts) - $no_of_order));
            if(Auth::user()->wallet_balance >= $per_order_wallet)
            {
                if($grand_total <= $per_order_wallet)
                {
                    $remaining_wallet_amount = $remaining_wallet_amount - $grand_total;

                    $adminOrder->partial_payment = json_encode([
                        'wallet_applied' => $grand_total
                    ]);

                    Helpers_generate_wallet_transaction(Auth::user()->id,$adminOrder->id,'Order_Place',$grand_total,0,$grand_total);
                    $adminOrder->payment_status = 'paid';
                }else{
                    $remaining_wallet_amount = $remaining_wallet_amount - $per_order_wallet;

                    $adminOrder->partial_payment = json_encode([
                        'wallet_applied' => $per_order_wallet
                    ]);

                    Helpers_generate_wallet_transaction(Auth::user()->id,$adminOrder->id,'Order_Place',$per_order_wallet,0,$per_order_wallet);
                    $adminOrder->payment_status = $request->payment_method == 'digital_payment' ? 'paid' : 'unpaid';
                }
            }else{

                if(Auth::user()->wallet_balance > 0)
                {
                    $remaining_wallet_amount = $remaining_wallet_amount - Auth::user()->wallet_balance;

                    $adminOrder->partial_payment = json_encode([
                        'wallet_applied' => Auth::user()->wallet_balance
                    ]);

                    Helpers_generate_wallet_transaction(Auth::user()->id,$adminOrder->id,'Order_Place',Auth::user()->wallet_balance,0,Auth::user()->wallet_balance);
                    $adminOrder->payment_status = $request->payment_method == 'digital_payment' ? 'paid' : 'unpaid';
                }else{
                    $adminOrder->payment_status = $request->payment_method == 'digital_payment' ? 'paid' : 'unpaid';
                }
            }
        }else{
            if($request->payment_method == 'wallet_amount')
            {
                if(Auth::user()->wallet_balance >= $grand_total)
                {
                    $remaining_wallet_amount = $remaining_wallet_amount - $grand_total;

                    $adminOrder->partial_payment = json_encode([
                        'wallet_applied' => $grand_total
                    ]);

                    Helpers_generate_wallet_transaction(Auth::user()->id,$adminOrder->id,'Order_Place',$grand_total,0,$grand_total);
                    $adminOrder->payment_status = 'paid';
                }else{

                    if(Auth::user()->wallet_balance > 0)
                    {
                        $remaining_wallet_amount = $remaining_wallet_amount - Auth::user()->wallet_balance;

                        $adminOrder->partial_payment = json_encode([
                            'wallet_applied' => Auth::user()->wallet_balance
                        ]);

                        Helpers_generate_wallet_transaction(Auth::user()->id,$adminOrder->id,'Order_Place',Auth::user()->wallet_balance,0,Auth::user()->wallet_balance);
                        $adminOrder->payment_status = $request->payment_method == 'digital_payment' ? 'paid' : 'unpaid';
                    }else{
                        $adminOrder->payment_status = $request->payment_method == 'digital_payment' ? 'paid' : 'unpaid';
                    }
                }
            }else{
                $adminOrder->payment_status = $request->payment_method == 'digital_payment' ? 'paid' : 'unpaid';
            }
        }
        $adminOrder->save();

        $order_ids[] = $adminOrder->id;
        

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

        // $no_of_order++;
        return response()->json([
            'status' => true,
            'message' => 'Order Placed Successfully',
            'data' => $request->all()
        ],200);
    }
}
