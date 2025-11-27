<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if logged in under customer guard
        if (!Auth::guard('customer')->check()) {
            return redirect()->back()
                ->with('error', 'You must be logged in as a customer to access this page.');
        }
        //  Check role column in users table
        if (Auth::guard('customer')->user()->role !== 'Customer') {
            Auth::guard('customer')->logout();
            return redirect()->back()
                ->with('error', 'Access denied. Only customers can access this page.');
        }

        return $next($request);
    }
}
