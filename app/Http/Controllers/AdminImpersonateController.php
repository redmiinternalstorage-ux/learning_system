<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Admin Impersonation — "Login As User"
 * Allows an admin to temporarily log in as any non-admin user
 * to view their experience. Original admin session is stored
 * in the PHP session so they can return at any time.
 */
class AdminImpersonateController extends Controller
{
    /**
     * Start impersonating a user.
     * Admin session is preserved in session('impersonating_admin_id').
     */
    public function impersonate(User $user)
    {
        $admin = Auth::user();

        // Safety: only main admin can impersonate
        if (!$admin->isAdmin()) {
            return back()->with('error', '🚫 Only the Main Administrator can use impersonation.');
        }

        // Safety: cannot impersonate another admin
        if ($user->isAdmin()) {
            return back()->with('error', '🚫 Cannot impersonate another administrator account.');
        }

        // Safety: cannot impersonate blocked users (edge case)
        if ($user->is_blocked) {
            return back()->with('error', '⚠️ Cannot impersonate a blocked account.');
        }

        // Store original admin's ID and department in session
        session([
            'impersonating_admin_id'   => $admin->id,
            'impersonating_admin_dept' => session('active_department'),
        ]);

        // Clear admin department session so middleware doesn't interfere
        session()->forget('active_department');

        // Log the impersonation
        ActivityLogger::log(
            'admin.impersonate_start',
            'User',
            $user->id,
            $user->name,
            "Admin [{$admin->name}] started impersonating user [{$user->name} | {$user->email}]"
        );

        // Switch Auth to the target user
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('impersonation_notice', "👁️ You are viewing as <strong>{$user->name}</strong> ({$user->email}). <a href='" . route('admin.impersonate.stop') . "' class='underline font-bold'>Return to Admin Panel →</a>");
    }

    /**
     * Stop impersonation and return to original admin session.
     */
    public function stopImpersonation(Request $request)
    {
        $adminId = session('impersonating_admin_id');

        if (!$adminId) {
            // Not impersonating — just go to dashboard
            return redirect()->route('dashboard');
        }

        $admin = User::find($adminId);

        if (!$admin || !$admin->isAdmin()) {
            // Admin not found, clear everything and re-login
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('admin.login')->with('error', 'Session expired. Please log in again.');
        }

        $impersonatedUser = Auth::user();

        // Log the return
        ActivityLogger::log(
            'admin.impersonate_stop',
            'User',
            $impersonatedUser->id,
            $impersonatedUser->name,
            "Admin [{$admin->name}] stopped impersonating [{$impersonatedUser->name}]"
        );

        // Restore original admin session
        $dept = session('impersonating_admin_dept', 'admin');
        session()->forget(['impersonating_admin_id', 'impersonating_admin_dept']);

        Auth::login($admin);
        session(['active_department' => $dept]);

        return redirect()->route('admin.users')
            ->with('success', "✅ Returned to Admin Panel. You were viewing as [{$impersonatedUser->name}].");
    }
}
