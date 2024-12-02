<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
                    'data' => []
                ],200);
            }elseif ($vendor->is_verify == 1) 
            {
                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'KYC Approval Pending',
                    'data' => [
                        'vendor' => $vendor
                    ]
                ],200);
            }elseif ($vendor->is_verify == 2) 
            {
                
                // Partner Orders
                return response()->json([
                    'status' => true,
                    'is_verify' => $vendor->is_verify,
                    'message' => 'Dashboard',
                    'data' => [
                        'vendor' => $vendor,
                        'order' => []
                    ]
                ],200);
            }elseif ($vendor->is_verify == 3) 
            {
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
