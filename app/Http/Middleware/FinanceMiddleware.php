<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please sign in to access the Finance Department portal.');
        }

        $user = Auth::user();

        // Finance and main admin can access finance panel
        if (!in_array($user->role, ['finance', 'admin'])) {
            return redirect()->route('admin.login')
                ->with('error', 'Access denied. Finance Department portal is restricted.');
        }

        // Blocked finance users cannot enter
        if ($user->is_blocked) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your account has been blocked. Please contact the administrator.');
        }

        // Department isolation check: User must have verified and entered 'finance' department
        if (session('active_department') !== 'finance') {
            return redirect()->route('admin.panel-select')
                ->with('error', 'Access denied. Please authenticate into the Finance Department portal first.');
        }

        return $next($request);
    }
}

