<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
}
