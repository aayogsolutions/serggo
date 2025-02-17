<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::guard('admins')->check())
        {
            $user = auth('admins')->user();
            if($user->role_id == 1){
                return $next($request);
            }
            else{
                if (isset($user) && $user->status == 1) {
                    auth()->guard('admins')->logout();
                    flash()->success(translate('Your Status is off'));
                    return redirect()->route('admin.login');
                }
                return $next($request);
            }
            
        }else{
            return redirect(route('admin.login'));
        }
        
    }
}
