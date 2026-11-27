<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollegeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('college')->check()) {
            return redirect()->route('college.login')
                ->with('error', 'Please sign in to access the College Portal.');
        }

        $college = Auth::guard('college')->user();

        if (!$college->isActive()) {
            Auth::guard('college')->logout();
            return redirect()->route('college.login')
                ->with('error', 'Your college account is not yet activated. Please contact Learning Curve admin.');
        }

        return $next($request);
    }
}
