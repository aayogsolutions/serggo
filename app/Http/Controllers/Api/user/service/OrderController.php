<?php

namespace App\Http\Controllers\Api\user\service;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSetting,
    ){}

    /**
     * 
     * @return JsonResponse
     */
    public function CheckOut() : JsonResponse
    {
        try {
            $id = Auth::user()->id;

            $cod = Helpers_get_business_settings('cash_on_delivery');
            if($cod['status'] == 0)
            {
                $cod['max_cod_status'] = Helpers_get_business_settings('maximum_amount_for_cod_order_status');
                $cod['max_cod_amount'] = Helpers_get_business_settings('maximum_amount_for_cod_order');
            }
            $digital_payment = Helpers_get_business_settings('digital_payment');
            $partial_payment = Helpers_get_business_settings('partial_payment');

        } catch (\Throwable $th) {
            $cod = [
                "status" => 1
            ];
            $digital_payment = 1;
            $partial_payment = 1;
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
                'balance' => Auth::user()->wallet_balance,
            ]
        ], 200);
    }

    /**
     * Request $request
     * @return JsonResponse
     */
    public function PlaceOrder(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|numeric',
            'partial_payment' => 'required|numeric',
            'wallet_applied' => 'required|numeric',
            'payment_method' => 'required',
            'payment_by' => 'required',
            'transaction_reference' => 'required',
            'due_amount' => 'required|numeric',
            'services.*.id' => 'required|numeric',
            'services.*.quantity' => 'required|numeric',
            'services.*.discount' => 'required|numeric',
            'services.*.tax' => 'required|numeric',
            'services.*.tax_type' => 'required|in:included,excluded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ],406);
        }

        
        return response()->json([
            'status' => true,
            'message' => 'Order Placed Successfully',
            'data' => $request->all()
        ],200);
    }
}
