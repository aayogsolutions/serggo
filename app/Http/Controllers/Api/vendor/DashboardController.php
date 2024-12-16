<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use App\Models\HomeSliderBanner;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function __construct(
       
    ){}

    /**
     * 
     * @return JsonResponse
     */
    public function Index() : JsonResponse
    {
        try {
            $vendor = auth('sanctum')->user();
            
            if($vendor->is_verify == 0)
            {
                return response()->json([
                    'status' => false,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'You Need to Submit KYC',
                    'data' => $vendor
                ],200);
            }
            elseif ($vendor->is_verify == 1) 
            {
                $vendor->aadhar_document =  gettype($vendor->aadhar_document) == 'array' ? $vendor->aadhar_document : json_decode($vendor->aadhar_document, true);
                $vendor->category =  gettype($vendor->category) == 'array' ? $vendor->category : json_decode($vendor->category, true);
                $vendor->working_days =  gettype($vendor->working_days) == 'array' ? $vendor->working_days : json_decode($vendor->working_days, true);
                

                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'KYC Approval Pending',
                    'data' => [
                        'banner' => [],
                        'vendor' => $vendor,
                        'order' => []
                    ]
                ],200);
            }
            elseif ($vendor->is_verify == 2) 
            {
                $banner = HomeSliderBanner::where(['ui_type' => 'vender_service','status' => 0])->orderBy('priority', 'asc')->get();

                $orders = Helpers_Orders_formatting(Order::where(['vender_id' => $vendor->id , 'order_type' => 'goods'])->whereNotIn('order_status' , ['delivered,canceled,returned,failed,rejected'])->orderby('id','desc')->with(['customer','OrderDetails'])->get(), true, true, false);
                
                $vendor->aadhar_document =  gettype($vendor->aadhar_document) == 'array' ? $vendor->aadhar_document : json_decode($vendor->aadhar_document, true);
                $vendor->category =  gettype($vendor->category) == 'array' ? $vendor->category : json_decode($vendor->category, true);
                $vendor->working_days =  gettype($vendor->working_days) == 'array' ? $vendor->working_days : json_decode($vendor->working_days, true);
                

                // Vender Orders
                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'Dashboard',
                    'data' => [
                        'banner' => $banner,
                        'vendor' => $vendor,
                        'order' => $orders
                    ]
                ],200);
            }
            elseif ($vendor->is_verify == 3) 
            {
                $vendor->aadhar_document =  gettype($vendor->aadhar_document) == 'array' ? $vendor->aadhar_document : json_decode($vendor->aadhar_document, true);
                $vendor->category =  gettype($vendor->category) == 'array' ? $vendor->category : json_decode($vendor->category, true);
                $vendor->working_days =  gettype($vendor->working_days) == 'array' ? $vendor->working_days : json_decode($vendor->working_days, true);
                

                return response()->json([
                    'status' => false,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'KYC Rejected by Admin',
                    'data' => [
                        'vendor' => $vendor
                    ]
                ],200);
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
