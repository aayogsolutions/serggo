<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\{
    Request,
    JsonResponse
};
use Illuminate\Support\Facades\{
    Auth,
    Validator
};

class ProfileController extends Controller
{
    public function __construct(
        private Vendor $vendor,
    ){}

    /**
     * 
     * @return JsonResponse
     */
    public function Profile() : JsonResponse
    {
        try {
            $vendor = auth('sanctum')->user();
            return response()->json([
                'status' => true,
                'message' => 'Profile',
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
}
