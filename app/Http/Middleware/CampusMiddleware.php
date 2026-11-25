<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampusMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('campus')->check()) {
            return redirect()->route('campus.login')->with('error', 'Please login to access the Campus Portal.');
        }

        $student = Auth::guard('campus')->user();

        if (!$student->isApproved()) {
            Auth::guard('campus')->logout();
            return redirect()->route('campus.login')
                ->with('error', 'Your campus account is not approved yet. Please wait for admin approval.');
        }

        return $next($request);
    }
}
