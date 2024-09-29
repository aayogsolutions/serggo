<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Models\ProductCart;

class CartController extends Controller
{

    public function __construct(

        private Products $product,
        
    ){}

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function cart(Request $request) : JsonResponse
    {
         $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $product_data = $this->product->status()->where('id',$request->product_id)->first();

        if(!is_null($product_data)) {

            $cart = new ProductCart();
            $cart->userid = $request->user_id;
            $cart->productid = $request->product_id;
            $cart->quantity = $request->quantity;
            $cart->product_detail = json_encode($product_data);
            if (isset($request->variation)) {
                $cart->variation = $request->variation;
            }
            $cart->save();        
                
            return response()->json([
                'status' => true,
                'message' => 'Add to cart sucessfully',
                'data' => []
            ],202);
            
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Product Not exists',
                'data' => []
            ],406);
            
        }
    }
}