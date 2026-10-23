<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please sign in to access the admin portal.');
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('admin.panel-select')
                ->with('error', 'This section is restricted to the Main Administrator only.');
        }

        return $next($request);
    }
}
