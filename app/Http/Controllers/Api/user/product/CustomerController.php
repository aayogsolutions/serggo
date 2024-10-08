<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTranscation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function __construct(

        private User $user,
        private WalletTranscation $wallettranscation,
    ){}

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function Profile(Request $request) : JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json([
                'status' => true,
                'message' => 'User Details',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
                'data' => [

                ]
            ], 406);
        }
    }

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function ProfileSubmit(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'number' => 'required|numeric',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            $id = Auth::user()->id;

            $user = $this->user->find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            if(Auth::user()->number != $request->number)
            {
                $otp = rand(1000, 9999);
                $expired_at = Carbon::now()->addMinutes(10)->format('Y/m/d H:i:s');


                $user->otp = $otp;
                $user->otp_expired_at = $expired_at;
                $user->number_verify = 0;
                $user->number = $request->number;
                if($request->has('image') && !empty($request->file('image')))
                {
                    $user->image = Helpers_update('Images/avtor/', $user->image, $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                }
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Number Verification required',
                    'number verify' => 0,
                    'data' => $user
                ],205);
            }
            $user->number = $request->number;
            if($request->has('image') && !empty($request->file('image')))
            {
                $user->image = Helpers_update('Images/avtor/', $user->image, $request->file('image')->getClientOriginalExtension(), $request->file('image'));
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile Updated',
                'data' => $user
            ],205);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Issue',
                'data' => []
            ],409);
        }
    }

      /**
     * 
     * @return JsonResponse
     * 
     */
    public function transaction() : JsonResponse
    {   
        try {

            $user_id  = Auth::user()->id;
            $transaction_data = $this->wallettranscation->where('user_id',$user_id)->get();
    
            if(!empty($transaction_data))
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Get Wallet Transaction data',
                    'data' => $transaction_data
                ],200); 
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found',
                    'data' => []
                ],404); 
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Data not found',
                'data' => []
            ],409);
        }          
    }
}
