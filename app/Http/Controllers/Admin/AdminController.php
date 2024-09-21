<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends Controller
{
    public function login(){
        return view('Admin.views.auth.login');
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

    public function dashboard()
    {
        $data['customer'] = 1;
        $data['product'] = 1;
        $data['order'] = 1;
        $data['category'] = 1;
        $data['branch'] = 1;

        $data['pending_count'] =1;
        $data['ongoing_count'] = 1;
        $data['delivered_count'] = 1;
        $data['canceled_count'] = 1;
        $data['returned_count'] = 1;
        $data['failed_count'] = 1;

        $data['recent_orders'] = 1;


        $data['top_sell'] = 1;
        $data['most_rated_products'] = 1;
        $data['top_customer'] = 1;
        $data['canceled'] = 1;
        $data['returned'] = 1;
        $data['failed'] = 1;
        $data['top_customer'] = 1;
        $data['top_customer'] = 1;

        $earning = [1,2,3,4,5,6,7,8,9,10,11,12,13];

        return view('Admin.views.dashboard', compact('data', 'earning'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->guard('admins')->logout();
        return redirect()->route('admin.auth.login');
    }
}
