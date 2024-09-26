<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveBranchCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('branch')->user();

        if (isset($user) && $user->status == 0){
            auth()->guard('branch')->logout();
            return redirect()->route('branch.auth.login');
        }
        return $next($request);
    }
}
