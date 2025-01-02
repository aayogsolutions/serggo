<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateways;
use App\Models\PaymentTransactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     * @param Request $request
     * @return View|Application|Factory
     */
    public function PaymentGateway(Request $request): View|Application|Factory|JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
            'user_id' => 'required|numeric',
            'reference' => 'required',
        ]);

        $amount = $request->amount;
        $user_id = $request->user_id;
        $reference = $request->reference;
        $gateway = PaymentGateways::where('is_active', 0)->first();

        if (!$gateway) {
            return response()->json([
                'status' => false,
                'message' => translate('payment_gateway_not_available')
            ], 406);
        }

        if($gateway->key_name == 'razor_pay') 
        {
            if($gateway->mode == 'test'){
                $data = json_decode($gateway->test_values);
            }else{
                $data = json_decode($gateway->live_values);
            }
            $id = $data->api_key;
            $secret = $data->api_secret;

            return view('user.payment.razorpay', compact('amount','id','secret','user_id','reference'));
        }
    }

    /**
     * Create a new controller instance.
     * @param Request $request
     * @return JsonResponse
     */
    public function PaymentGatewayOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'user_id' => 'required|numeric',
            'reference' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $gateway = PaymentGateways::where('is_active', 0)->first();
        $user = User::find($request->user_id);

        if (!$gateway) {
            return response()->json([
                'status' => false,
                'message' => translate('payment_gateway_not_available')
            ], 406);
        }

        if($gateway->key_name == 'razor_pay') 
        {
            if($gateway->mode == 'test'){
                $data = json_decode($gateway->test_values);
            }else{
                $data = json_decode($gateway->live_values);
            }
            $api = new Api($data->api_key, $data->api_secret);
            $transaction = new PaymentTransactions();
            // $transaction->user_id = Auth::user()->id; // For Live
            $transaction->user_id = $request->user_id; // For testing
            $transaction->amount = $request->amount;
            $transaction->reference = $request->reference;
            $transaction->save();
            $id = $transaction->id;
            $transactionid = rand(10000,99999).$id;
            $transaction->transaction_id = $transactionid;
            $transaction->save();

            $order = $api->order->create([
                'receipt' => $transactionid,
                'amount' => $request->amount*100, // amount in the smallest currency unit (e.g., 5000 paise = ₹50)
                'currency' => 'INR'
            ]);

            return response()->json([
                'status' => 'success',
                'order' => $order['id'],
                'amount' => $request->amount,
                'site_transaction_id' => $transactionid,
                'key' => $data->api_key,
                'image' => json_decode($gateway->additional_data)->gateway_image,
                'name' => $user->name,
                'email' => $user->email,
                'number' => $user->number,
            ], 200);
        }
    }

    /**
     * Create a new controller instance.
     * @param Request $request
     * @return View|Application|Factory|JsonResponse
     */
    public function PaymentGatewayResponse(Request $request): View|Application|Factory|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'site_transactionid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 200);
        }

        $transaction = PaymentTransactions::where('transaction_id','=',$request->site_transactionid);
        $transaction->update([
            'transaction_status' =>"1",
            'payment_id' => $request->razorpay_payment_id
        ]);

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * Create a new controller instance.
     * @param Request $request
     * @return View|Application|Factory|JsonResponse
     */
    public function PaymentGatewaySuccess(Request $request): View|Application|Factory|JsonResponse
    {
        dump('Payment Success');die;
        return response()->json([
            'status' => 'success',
        ], 200);

    }

    /**
     * Create a new controller instance.
     * @param Request $request
     * @return View|Application|Factory|JsonResponse
     */
    public function PaymentGatewayFailed(Request $request): View|Application|Factory|JsonResponse
    {
        dump('Payment Failed');die;
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required',
            'site_transactionid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 406);
        }

        $transaction = PaymentTransactions::where('transaction_id','=',$request->site_transactionid);
        $transaction->update([
            'transaction_status' =>"2",
            'payment_id' => $request->razorpay_payment_id
        ]);

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
