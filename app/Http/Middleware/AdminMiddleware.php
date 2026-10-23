<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please sign in to access the admin portal.');
        }

        $user = Auth::user();

        if (!$user->hasAdminAccess()) {
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. This portal is restricted to authorised personnel only.');
        }

        if ($user->is_blocked && !$user->isAdmin()) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your account has been blocked. Please contact the administrator.');
        }

        // Auto-default active department if not set
        if (!session()->has('active_department') && $user->isAdmin()) {
            session(['active_department' => 'admin']);
        }

        // Department isolation check: User must have verified and entered 'admin' department
        if (session('active_department') !== 'admin') {
            return redirect()->route('admin.panel-select')
                ->with('error', 'Access denied. Please authenticate into the Main Admin department first.');
        }

        return $next($request);
    }
}

