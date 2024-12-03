<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    Admin,
    BusinessSetting,
    CustomerAddresses,
    Notifications,
    Order,
    Order_details,
    Products,
    User,
    Vendor
};
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private Products $product,
        private Order $order,
        private Order_details $order_detail,
        private CustomerAddresses $customeraddress,
        private User $user,
        private Notifications $notifications,
    ){}
    
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function Checkout(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required',
            'langitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        
        try {
            $admin_lat_long = Admin::select('latitude','longitude')->where('id',1)->first();

            $products = product_data_formatting($this->product->status()->WhereIn('id',json_decode($request->products))->get(),true,false,true);
            
            foreach ($products as $key => $value) {
                if($value->vender_id != null)
                {
                    $vendor_lat_lang = Vendor::select('latitude','longitude')->where('id',$value->vender_id)->first();
                    $value->distance = $this->point2point_distance($request->latitude, $request->langitude , $vendor_lat_lang->latitude , $vendor_lat_lang->longitude);
                    $value->delivery_charge = $this->find_delivery_charge($value->distance);
                }else{
                    $value->distance = $this->point2point_distance($request->latitude, $request->langitude , $admin_lat_long->latitude , $admin_lat_long->longitude);
                    $value->delivery_charge = $this->find_delivery_charge($value->distance);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 409);
        }

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
                'products' => $products,
                'wallet' => Auth::user()->wallet_balance,
                'cod' => $cod,
                'digital_payment' => $digital_payment,
                'partial_payment' => $partial_payment,
                'delivery' => $delivery,
                'tax' => Helpers_get_business_settings('product_gst_tax_status'),
            ]
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function PlaceOrder(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product.*.id' => 'required',
            'product.*.selectedvariation' => 'required',
            'product.*.quantity' => 'required|numeric',
            'product.*.is_installation' => 'required|numeric',
            'product.*.delivery' => 'required|numeric',
            'product.*.discount' => 'required|numeric',
            'product.*.is_advance' => 'required|numeric',
            'product.*.advance' => 'nullable|numeric',
            'product.*.tax' => 'required|numeric',
            'address_id' => 'required|numeric',
            'partial_payment' => 'required|numeric',
            'wallet_applied' => 'required|numeric',
            'payment_method' => 'required|in:cod,digital_payment,wallet_amount',
            'tax_type' => 'required|in:included,excluded',
            'gst_invoice' => 'required|numeric',
            'gst_no' => 'required',
            'gst_name' => 'required',
            'mobile_no' => 'required|numeric',
            'transaction_reference' => 'required',
            'due_amount' => 'required|numeric',
            'free_delivery' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $vendor_ids = [];
        $order_ids = [];
        $orderedproducts = [];

        try {
            
            foreach ($request->product as $key => $value) {
                $product = $this->product->find($value['id']);
                if(is_numeric($product->vender_id))
                {
                    $vendor_ids[] = $product->vender_id;
                    $orderedproducts[$product->vender_id][] = $value;
                }else{
                    $orderedproducts['admin'][] = $value;
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 409);
        }
        
        try {
            $remaining_wallet_amount = $request->wallet_applied;
            $no_of_order = 0;

            foreach ($orderedproducts as $key => $products) {
                if($this->order->exists())
                {
                    $id = $this->order->max('id') + 1;
                }else{
                    $id = 100001;
                }
                if($key == 'admin')
                {
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
                    $adminOrder->delivery_address_id = $request->address_id;
                    $adminOrder->free_delivery = $request->free_delivery;
                    $adminOrder->delivered_by = 0;
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
                    $advance = 0;
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
                        if($value['is_advance'] == 0)
                        {
                            $order_details->advance_payment = $value['tax'];
                            $advance = $advance + $value['advance'];
                        }
                        
                        $order_details->save();
    
                        $tax_amount += $value['tax'] * $value['quantity'];
                        $amount += $price * $value['quantity'];
                        $delivery = $delivery + $value['delivery'];
                        $discount += $value['discount'] * $value['quantity'];
                    }
    
                    $adminOrder = $this->order->find($adminOrder->id);
                    
                    $adminOrder->total_tax_amount = $tax_amount;
                    $adminOrder->delivery_charge = $delivery;
                    $adminOrder->total_discount = $discount;
                    $adminOrder->total_installation	 = $installation;
                    $adminOrder->item_total = $amount;
                    $adminOrder->advance_payment = $advance;
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
                }else{
    
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
                    $advance = 0;
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
                        if($value['is_advance'] == 0)
                        {
                            $order_details->advance_payment = $value['tax'];
                            $advance = $advance + $value['advance'];
                        }
                        $order_details->save();
    
                        $tax_amount += $value['tax'] * $value['quantity'];
                        $amount += $price * $value['quantity'];
                        $delivery = $delivery + $value['delivery'];
                        $discount += $value['discount'] * $value['quantity'];
                    }
    
                    $adminOrder = $this->order->find($adminOrder->id);
                    
                    $adminOrder->total_tax_amount = $tax_amount;
                    $adminOrder->delivery_charge = $delivery;
                    $adminOrder->total_discount = $discount;
                    $adminOrder->total_installation	 = $installation;
                    $adminOrder->item_total = $amount;
                    $adminOrder->advance_payment = $advance;
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

                $no_of_order++;
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Something went wrong'.$th->getMessage(),
                'data' => []
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order Placed',
            'data' => $order_ids
        ], 201);
    }

    /**
     * @return JsonResponse
     */
    public function OrderHistory() : JsonResponse
    {
        try {
            $orders = $this->order->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->with('OrderDetails')->get();

            $orders = Helpers_Orders_formatting($orders, true, true, false);
            return response()->json([
                'status' => true,
                'message' => 'Order History',
                'data' => $orders,
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'errors' => 'Unexpected Error',
            ], 406);
        }
    }

    /**
     * @return JsonResponse
     */
    public function OrderItems($id) : JsonResponse
    {
        try {
            $orders = $this->order->where('id', $id)->with('OrderDetails')->first();
        
            $orders = Helpers_Orders_formatting($orders, false, true, false);

            if($orders->user_id != Auth::user()->id)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Order Not Found',
                    'data' => [],
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => 'Order Status',
                'data' => $orders,
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'errors' => 'Unexpected Error',
            ], 401);
        }
    }

    private function point2point_distance($lat1, $lon1, $lat2, $lon2, $unit='K') 
    { 
        $theta = $lon1 - $lon2; 
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
        $dist = acos($dist); 
        $dist = rad2deg($dist); 
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") 
        {
            return ($miles * 1.609344); 
        } 
        else if ($unit == "N") 
        {
            return ($miles * 0.8684);
        } 
        else 
        {
            return $miles;
        }
    } 

    private function find_delivery_charge($distance)
    {
        if (!BusinessSetting::where(['key' => 'delivery_management'])->first()) {
            $value = [];
            for ($i=0; $i < 14; $i++) { 
                $data = [
                    'minimum' => 0,
                    'maximum' => 0,
                    'charge' => 0
                ];
                array_push($value, $data);
            }
            
            BusinessSetting::updateOrInsert(['key' => 'delivery_management'], [
                'value' => json_encode($value),
            ]);
        }

        $delivery_management = json_decode(BusinessSetting::where(['key' => 'delivery_management'])->first()->value);
        foreach ($delivery_management as $key => $value) {
            if ($distance >= $value->minimum && $distance <= $value->maximum) {
                return number_format($value->charge * $distance,2,'.','');
            }
        }    
        return 0;
    }
}
