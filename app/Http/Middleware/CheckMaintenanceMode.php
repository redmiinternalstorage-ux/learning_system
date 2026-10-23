<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        $isMaintenance = (bool) SiteSetting::get('maintenance_mode', false);

        if ($isMaintenance) {
            $user = Auth::user();

            // Admins, Staff, Finance roles can access the platform & admin panel
            if ($user && ($user->hasAdminAccess() || $user->isAdmin() || $user->isStaff() || $user->isFinance())) {
                return $next($request);
            }

            // Allow login / admin auth routes so staff/admins can sign in
            if ($request->is('admin*') || $request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // Render Under Maintenance page for public visitors & students
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
