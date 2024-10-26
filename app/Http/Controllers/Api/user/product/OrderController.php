<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    CustomerAddresses,
    Products
};
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private Products $product,
        private CustomerAddresses $customeraddress,
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
                'address' => $address,
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
            'payment_method' => 'required|in:cod,online',
            'address_id' => 'required|numeric',
            'wallet_amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $vendor_ids = [];
        $orderedproducts = [];

        try {
            
            foreach ($request->product as $key => $value) {
                $product = $this->product->find($value['id']);
                if(is_numeric($product->vender_id))
                {
                    $vendor_ids[] = $product->vender_id;
                    $orderedproducts[$product->vender_id][] = product_data_formatting($product,false,false,true);
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
        
        // dd($request->product);

        return response()->json([
            'status' => true,
            'message' => 'Order Placed',
            'data' => [
                $vendor_ids,
                $orderedproducts
            ]
        ], 201);
    }
}
