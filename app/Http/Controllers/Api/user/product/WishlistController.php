<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Models\Service;
use App\Models\ServiceFavorite;
use App\Models\UserWishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct(

        private Products $product,
        private Service $service,
        private UserWishlist $productwishlist,
        private ServiceFavorite $servicefavorite,
    ){}

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function Favorite(Request $request) : JsonResponse
    {
    
        $validator = Validator::make($request->all(), [
            'status' => 'required|numeric',
            'product_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $user_id = Auth::user()->id;

        if($this->product->where('id', $request->product_id)->exists()){

            if($request->status == 0)
            {
                if(!$this->productwishlist->where([['user_id','=',$user_id],['product_id','=',$request->product_id]])->exists()){

                    $wishlist = new UserWishlist();
                    $wishlist->user_id = $user_id;
                    $wishlist->product_id = $request->product_id;
                    $wishlist->save();   
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Add to wishlist sucessfully',
                    'data' => []
                ]);
            }else{
                if($this->productwishlist->where([['user_id','=',$user_id],['product_id','=',$request->product_id]])->exists()){
                    $this->productwishlist->where([['user_id','=',$user_id],['product_id','=',$request->product_id]])->delete();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Remove to wishlist sucessfully',
                    'data' => []
                ]);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Product Not exists',
                'data' => []
            ]);
        }
    }

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function FavoriteList(Request $request) : JsonResponse
    {
        try {
            $user_id = Auth::user()->id;

            $list = $this->productwishlist->where(['user_id' => $user_id])->pluck('product_id')->toArray();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => [$th->getMessage(),$list]
            ],408);
        }

        if($list == [])
        {
            return response()->json([
                'status' => true,
                'message' => 'List Is empty',
                'data' => []
            ],200);
        }

        $product = product_data_formatting($this->product->whereIn('id' , $list)->Orderby('total_sale','DESC')->get(),true,false,true);

        return response()->json([
            'status' => true,
            'message' => 'Favorite List',
            'data' => $product
        ]);
    }

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function ServiceFavorite(Request $request) : JsonResponse
    {
    
        $validator = Validator::make($request->all(), [
            'status' => 'required|numeric',
            'service_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $user_id = Auth::user()->id;

        if($this->service->where('id', $request->service_id)->exists()){

            if($request->status == 0)
            {
                if(!$this->servicefavorite->where([['user_id','=',$user_id],['service_id','=',$request->service_id]])->exists()){

                    $wishlist = new ServiceFavorite();
                    $wishlist->user_id = $user_id;
                    $wishlist->service_id = $request->service_id;
                    $wishlist->save();   
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Add to wishlist sucessfully',
                    'data' => []
                ]);
            }else{
                if($this->servicefavorite->where([['user_id','=',$user_id],['service_id','=',$request->service_id]])->exists()){
                    $this->servicefavorite->where([['user_id','=',$user_id],['service_id','=',$request->service_id]])->delete();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Remove to wishlist sucessfully',
                    'data' => []
                ]);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Service Not exists',
                'data' => []
            ]);
        }
    }

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function ServiceFavoriteList(Request $request) : JsonResponse
    {
        try {
            $user_id = Auth::user()->id;

            $list = $this->servicefavorite->where(['user_id' => $user_id])->pluck('service_id')->toArray();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => [$th->getMessage(),$list]
            ],408);
        }

        if($list == [])
        {
            return response()->json([
                'status' => true,
                'message' => 'List Is empty',
                'data' => []
            ],200);
        }

        $product = Service_data_formatting($this->service->whereIn('id' , $list)->get(),true,false);

        return response()->json([
            'status' => true,
            'message' => 'Favorite List',
            'data' => $product
        ]);
    }
}
