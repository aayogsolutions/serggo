<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Models\UserWishlist;

class WishlistController extends Controller
{
    public function __construct(

        private Products $product,
        
    ){}

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function wishlist(Request $request) : JsonResponse
    {
    
         $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'product_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        if($this->product->where('id', $request->product_id)->exists()){

            $wishlist = new UserWishlist();
            $wishlist->user_id = $request->user_id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();   
                
            return response()->json([
                'status' => true,
                'message' => 'Add to wishlist sucessfully',
                'data' => []
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Product Not exists',
                'data' => []
            ]);
        }
        
    }
}
