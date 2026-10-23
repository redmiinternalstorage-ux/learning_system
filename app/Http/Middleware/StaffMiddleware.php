<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please sign in to access the Staff Portal.');
        }

        $user = Auth::user();

        if (!$user->isStaff() && !$user->isAdmin()) {
            return redirect()->route('admin.login')
                ->with('error', 'Access denied. Staff Portal is restricted to authorised staff accounts.');
        }

        if ($user->is_blocked) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your account has been blocked. Please contact the administrator.');
        }

        // Department isolation check: User must have verified and entered 'staff' department
        if (session('active_department') !== 'staff') {
            return redirect()->route('admin.panel-select')
                ->with('error', 'Access denied. Please authenticate into the Staff Department portal first.');
        }

        return $next($request);
    }
}

