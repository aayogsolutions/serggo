<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\{
    Request,
    JsonResponse
};
use Illuminate\Support\Facades\{
    Auth,
    Validator
};
use stdClass;

class ProfileController extends Controller
{
    public function __construct(
        private Vendor $vendor,
    ){}

    /**
     * 
     * @return JsonResponse
     */
    public function VendorData() : JsonResponse
    {
        try {
            $vendor = auth('sanctum')->user();

            $vendor->aadhar_document = json_decode($vendor->aadhar_document);
            $vendor->category = json_decode($vendor->category);
            $vendor->working_days = json_decode($vendor->working_days);

            $today = Order::where(['vender_id' => $vendor->id , 'order_status' => 'delivered'])->whereDate('updated_at', date('Y-m-d'))->get();

            if(!is_null($today) && count($today) > 0)
            {
                $today = $this->CalculateOrderAmount($today);
            }else{
                $today = 0;
            }

            $month = Order::where(['vender_id' => $vendor->id , 'order_status' => 'delivered'])->whereMonth('updated_at', date('Y-m'))->get();

            if(!is_null($month) && count($month) > 0)
            {
                $month = $this->CalculateOrderAmount($month);
            }else{
                $month = 0;
            }

            $total = Order::where(['vender_id' => $vendor->id , 'order_status' => 'delivered'])->get();

            if(!is_null($total) && count($total) > 0)
            {
                $total = $this->CalculateOrderAmount($total);
            }else{
                $total = 0;
            }

            return response()->json([
                'status' => true,
                'message' => 'Profile',
                'Todaysale' => $today,
                'Monthlysale' => $month,
                'TotalSale' => $total,
                'data' => $vendor
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error'.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param $order
     * @return int
     */
    private function CalculateOrderAmount($order)
    {
        $amount = 0;
        
        $amount = $order->order_amount - ($order->commission * $order->order_amount / 100);

        return $amount;
    }
}
