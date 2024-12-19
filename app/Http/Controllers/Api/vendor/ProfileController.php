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
     * @return JsonResponse
     */
    public function Profile() : JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Profile Data',
            'data' => Auth::user()
        ],200);
    }
}
