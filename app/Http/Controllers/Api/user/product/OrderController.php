<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    CustomerAddresses,
    Notifications,
    Order,
    Order_details,
    Products,
    User
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
     * @return JsonResponse
     */
    public function Checkout() : JsonResponse
    {
        try {
            $id = Auth::user()->id;

            $address = $this->customeraddress->where('user_id',$id)->get();

            $cod['status'] = Helpers_get_business_settings('cash_on_delivery');
            if($cod['status'] == 0)
            {
                $cod['max_cod_status'] = Helpers_get_business_settings('maximum_amount_for_cod_order_status');
                $cod['max_cod_amount'] = Helpers_get_business_settings('maximum_amount_for_cod_order');
            }

            $delivery['free_delivery_over_amount_status'] = Helpers_get_business_settings('free_delivery_over_amount_status');
            if(!is_null($delivery['free_delivery_over_amount_status']) && $delivery['free_delivery_over_amount_status'] == 0)
            {
                $delivery['free_delivery_over_amount'] = Helpers_get_business_settings('free_delivery_over_amount');
            }
            $delivery['delivery_management'] = Helpers_get_business_settings('delivery_management');

            if ($delivery['delivery_management']['status'] == 0) {
                
            }else{
                unset($delivery['delivery_management']['min_shipping_charge'], $delivery['delivery_management']['shipping_per_km']);
                $delivery['delivery_management']['default_delivery_charge'] = Helpers_get_business_settings('default_delivery_charge');
            }

            $digital_payment = Helpers_get_business_settings('digital_payment');
            $partial_payment = Helpers_get_business_settings('partial_payment');
        } catch (\Throwable $th) {
            $cod = [
                "status" => 1
            ];
            $digital_payment = 1;
            $partial_payment = 1;
            $delivery = [];
            $address = [];
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
                'delivery' => $delivery,
                'balance' => Auth::user()->wallet_balance,
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
            'product.*.tax' => 'required|numeric',
            'payment_method' => 'required|in:cod,online',
            'address_id' => 'required|numeric',
            'wallet_applied' => 'required|numeric',
            'item_total' => 'required|numeric',
            'total_installation_charges' => 'required|numeric',
            'total_delivery_charges' => 'required|numeric',
            'total_discount' => 'required|numeric',
            'total_taxs' => 'required|numeric',
            'tax_type' => 'required',
            'gst_invoice' => 'required|numeric',
            'gst_no' => 'required',
            'gst_name' => 'required',
            'mobile_no' => 'required|numeric',
            'grand_total' => 'required|numeric',
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
        
        $tax_type = Helpers_get_business_settings('product_gst_tax_status');
        
        try {
            $wallet_amount_per_product = 0;
            if(isset($request->wallet_applied) && $request->wallet_applied != 0)
            {
                $this->user->find(Auth::user()->id)->update(['wallet_balance' => $this->user->find(Auth::user()->id)->wallet_balance - $request->wallet_applied]);
                $number_of_product = count($request->product);
                $wallet_amount_per_product = $request->wallet_applied / $number_of_product;
            }
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
                    $adminOrder->payment_status = 'unpaid';
                    $adminOrder->payment_method = $request->payment_method;
                    $adminOrder->delivery_address_id = $request->address_id;
                    $adminOrder->grand_total = $request->grand_total;
                    $adminOrder->delivered_by = 0;
                    $adminOrder->checked = 1;
                    $adminOrder->date = now();
                    $adminOrder->delivery_address = json_encode($this->customeraddress->find($request->address_id));
                    $adminOrder->gst_invoice = $request->gst_invoice;
                    $adminOrder->save();
    
                    $tax_amount = 0;
                    $amount = 0;
                    $delivery = 0;
                    $partial_payment_amount = 0;
                    foreach ($products as $key1 => $value) 
                    {
                        $product = $this->product->find($value['id']);
    
                        if($value['selectedvariation'] != null)
                        {
                            $price = $value['selectedvariation']['price'];
                        }else{
                            $price = $product->price;
                        }
    
                        $this_tax_amount = Helpers_tax_calculate($product, $price);
    
                        $order_details = new Order_details();
                        $order_details->order_id = $adminOrder->id;
                        $order_details->product_id = $product->id;
                        $order_details->product_details = json_encode($product);
                        $order_details->price = $price;
                        $order_details->quantity = $value['quantity'];
                        $order_details->variation = json_encode($value['selectedvariation']);
                        $order_details->unit = $product->unit;
                        $order_details->discount_on_product = $value['discount'];
                        $order_details->is_stock_decreased = 1;
                        if($value['is_installation'] == 0)
                        {
                            $order_details->installastion_amount = $product->installation_charges;
                            $order_details->installation = 0;
                        }
                        if($tax_type == null || $tax_type == 'excluded')
                        {
                            $order_details->gst_status = 'excluded';
                        }else{
                            $order_details->gst_status = 'included';
                        }
                        $order_details->tax_amount = $this_tax_amount;
                        $order_details->save();
    
                        $tax_amount += $this_tax_amount * $value['quantity'];
                        $amount += $price * $value['quantity'];
                        $delivery += $value['delivery'] * $value['quantity'];
                        $partial_payment_amount += $wallet_amount_per_product;
                    }
    
                    $adminOrder = $this->order->find($adminOrder->id);
                    if(isset($request->wallet_applied) && $request->wallet_applied != 0)
                    {
                        $adminOrder->partial_payment = json_encode([
                            'wallet_applied' => $partial_payment_amount
                        ]);
                        Helpers_generate_wallet_transaction(Auth::user()->id,$adminOrder->id,'Order_Place',$partial_payment_amount,0,$partial_payment_amount);
                    }
                    $adminOrder->total_tax_amount = $this_tax_amount;
                    $adminOrder->order_amount = $amount;
                    $adminOrder->delivery_charge = $delivery;
                    $adminOrder->save();
    
                    $order_ids[] = $adminOrder->id;
                }else{
    
                    $adminOrder = new Order();
                    $adminOrder->id = $id;
                    $adminOrder->user_id = Auth::user()->id;
                    $adminOrder->order_type = 'goods';
                    $adminOrder->order_status = 'pending';
                    $adminOrder->order_approval = 'pending';
                    $adminOrder->payment_status = 'unpaid';
                    $adminOrder->payment_method = $request->payment_method;
                    $adminOrder->delivery_address_id = $request->address_id;
                    $adminOrder->grand_total = $request->grand_total;
                    $adminOrder->delivered_by = 1; // editable
                    $adminOrder->checked = 1;
                    $adminOrder->date = now();
                    $adminOrder->vender_id = $key;
                    $adminOrder->delivery_address = json_encode($this->customeraddress->find($request->address_id));
                    $adminOrder->gst_invoice = $request->gst_invoice;
                    $adminOrder->save();
    
                    $tax_amount = 0;
                    $amount = 0;
                    $delivery = 0;
                    $partial_payment_amount = 0;
                    foreach ($products as $key2 => $value) 
                    {
                        $product = $this->product->find($value['id']);
    
                        if($value['selectedvariation'] != null)
                        {
                            $price = $value['selectedvariation']['price'];
                        }else{
                            $price = $product->price;
                        }
    
                        $this_tax_amount = Helpers_tax_calculate($product, $price);
    
                        $order_details = new Order_details();
                        $order_details->order_id = $adminOrder->id;
                        $order_details->product_id = $product->id;
                        $order_details->product_details = json_encode($product);
                        $order_details->price = $price;
                        $order_details->quantity = $value['quantity'];
                        $order_details->variation = json_encode($value['selectedvariation']);
                        $order_details->unit = $product->unit;
                        $order_details->discount_on_product = $value['discount'];
                        $order_details->is_stock_decreased = 1;
                        if($value['is_installation'] == 0)
                        {
                            $order_details->installastion_amount = $product->installation_charges;
                            $order_details->installation = 0;
                        }
                        if($tax_type == null || $tax_type == 'excluded')
                        {
                            $order_details->gst_status = 'excluded';
                        }else{
                            $order_details->gst_status = 'included';
                        }
                        $order_details->tax_amount = $this_tax_amount;
                        $order_details->save();
    
                        $tax_amount += $this_tax_amount * $value['quantity'];
                        $amount += $price * $value['quantity'];
                        $delivery += $value['delivery'] * $value['quantity'];
                        $partial_payment_amount += $wallet_amount_per_product;
                    }
    
                    $adminOrder = $this->order->find($adminOrder->id);
                    if(isset($request->wallet_applied) && $request->wallet_applied != 0)
                    {
                        $adminOrder->partial_payment = json_encode([
                            'wallet_applied' => $partial_payment_amount
                        ]);
                        Helpers_generate_wallet_transaction(Auth::user()->id,$adminOrder->id,'Order_Place',$partial_payment_amount,0,$partial_payment_amount);
                    }
                    $adminOrder->total_tax_amount = $tax_amount;
                    $adminOrder->order_amount = $amount;
                    $adminOrder->delivery_charge = $delivery;
                    $adminOrder->save();
    
                    $order_ids[] = $adminOrder->id;
                }

                $notifications = new Notifications();
                $notifications->user_id = Auth::user()->id;
                $notifications->title = 'Order Placed Successfully';
                $notifications->description = 'Your Order No. '.$adminOrder->id.' Generated Successfully Approval Pending';
                $notifications->save();
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Something went wrong',
                'data' => []
            ], 404);
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
        
            if($orders->user_id != Auth::user()->id)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Order Not Found',
                    'data' => [],
                ], 404);
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
            ], 406);
        }
    }
}
