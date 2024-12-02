<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function login(){
        if(Auth::guard('admins')->check())
        {
            return redirect()->route('admin.dashboard');
        }else{
            return view('Admin.views.auth.login');
        }
       
    }

    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if(Auth::guard('admins')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]))
        {
            return redirect()->route('admin.dashboard');
        }else{
            flash()->error('Username or Password should be Wrong');
            return redirect()->back();
        }
    }
  
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->guard('admins')->logout();
        return redirect()->route('admin.auth.logout');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function NewOrder(Request $request): JsonResponse
    {
        $from = Carbon::now();
        $to = new Carbon('-12 seconds');
        
        $neworder = Order::where([
            ['order_approval', '=', 'pending'],
            ['created_at', '<=' , $from->format('Y-m-d H:i:s')],
            ['created_at', '>=' , $to->format('Y-m-d H:i:s')],
        ])->count();

        $newvendorkyc = Vendor::where([
            ['role', '=', '0'],
            ['is_verify', '=', 1],
            ['created_at', '<=' , $from->format('Y-m-d H:i:s')],
            ['created_at', '>=' , $to->format('Y-m-d H:i:s')],
        ])->count();

        $newpartnerkyc = Vendor::where([
            ['role', '=', '1'],
            ['is_verify', '=', 1],
            ['created_at', '<=' , $from->format('Y-m-d H:i:s')],
            ['created_at', '>=' , $to->format('Y-m-d H:i:s')],
        ])->count();

        return response()->json([
            'data' => [
                'new_order' => $neworder,
                'new_vendor_kyc' => $newvendorkyc,
                'new_partner_kyc' => $newpartnerkyc
            ]
        ]);
    }
}
