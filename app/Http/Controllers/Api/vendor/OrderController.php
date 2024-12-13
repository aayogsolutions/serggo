<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;

class OrderController extends Controller
{
    public function __construct(
       
    ){}

    /**
     * 
     * @return JsonResponse
     */
    public function OrderList() : JsonResponse
    {
        try {
            $vendor = auth('sanctum')->user();
            
            if ($vendor != null) 
            {
                $orders = Helpers_Orders_formatting(Order::where(['vender_id' => $vendor->id])->orderby('id','desc')->with(['customer','OrderDetails'])->get(), true, true, false);

                // Vender Orders
                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'Dashboard',
                    'data' => [
                        'order' => $orders,
                    ]
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Vendor Not Found',
                    'data' => []
                ],401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error'.$th->getMessage(),
                'data' => []
            ],401);
        }
    }
}
