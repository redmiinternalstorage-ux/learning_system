<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Show the dedicated admin / staff / finance login page.
     * Redirects away if already authenticated with admin access.
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return redirect()->route('admin.panel-select');
        }
        return view('admin.auth.login');
    }

    /**
     * Process admin / staff / finance login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');
        $authenticated = Auth::attempt($request->only('email', 'password'), $remember);

        if (!$authenticated) {
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user && $user->checkDatePassword($request->password)) {
                Auth::login($user, $remember);
                $authenticated = true;
            }
        }

        if ($authenticated) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Block check
            if ($user->is_blocked) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been blocked. Please contact the system administrator.',
                ]);
            }

            // Only allow admin-access roles through this gate
            if (! $user->hasAdminAccess()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. This portal is restricted to authorised staff and administrators only.',
                ]);
            }

            // Auto-grant active department for primary role on login
            if ($user->isAdmin()) {
                session(['active_department' => 'admin']);
            } elseif ($user->isStaff()) {
                session(['active_department' => 'staff']);
            } elseif ($user->isFinance()) {
                session(['active_department' => 'finance']);
            }

            return redirect()->route('admin.panel-select')
                ->with('welcome', 'Welcome back, ' . $user->name . '!');
        }

        return back()
            ->withErrors(['email' => 'Invalid email address or password.'])
            ->onlyInput('email');
    }

    /**
     * Verify department authentication and grant access to department session.
     */
    public function verifyDepartment(Request $request)
    {
        $request->validate([
            'department' => 'required|in:admin,staff,finance',
            'password'   => 'nullable|string',
        ]);

        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please log in again.');
        }

        $user = Auth::user();
        $dept = $request->department;

        // Check permission for target department
        if ($dept === 'admin' && !$user->isAdmin()) {
            return back()->with('error', '🚫 Access Denied: Only Main Administrators can enter Main Admin panel.');
        }

        if ($dept === 'staff' && !($user->isStaff() || $user->isAdmin())) {
            return back()->with('error', '🚫 Access Denied: You do not have permissions for the Staff Department.');
        }

        if ($dept === 'finance' && !($user->isFinance() || $user->isAdmin())) {
            return back()->with('error', '🚫 Access Denied: You do not have permissions for the Finance Department.');
        }

        // Check if password verification is required
        $isPrimaryRole = ($dept === 'admin' && $user->isAdmin()) 
                      || ($dept === 'staff' && $user->isStaff())
                      || ($dept === 'finance' && $user->isFinance());

        if ($request->filled('password')) {
            $validPassword = \Illuminate\Support\Facades\Hash::check($request->password, $user->password) 
                            || $user->checkDatePassword($request->password);

            if (!$validPassword) {
                return back()->with('error', '❌ Incorrect password for department verification.');
            }
        }

        // Grant active department session
        session(['active_department' => $dept]);

        $deptNames = [
            'admin'   => 'Main Admin Panel',
            'staff'   => 'Staff Department',
            'finance' => 'Finance Department',
        ];

        $redirectRoutes = [
            'admin'   => 'admin.dashboard',
            'staff'   => 'staff.dashboard',
            'finance' => 'finance.dashboard',
        ];

        return redirect()->route($redirectRoutes[$dept])
            ->with('success', "🔓 Welcome to the {$deptNames[$dept]}.");
    }

    /**
     * Switch out of active department session.
     */
    public function switchDepartment()
    {
        session()->forget('active_department');
        return redirect()->route('admin.panel-select')
            ->with('info', 'Switched department session. Select a department to authenticate.');
    }

    /**
     * Logout and redirect to admin login.
     */
    public function logout(Request $request)
    {
        session()->forget('active_department');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')
            ->with('success', 'You have been securely logged out.');
    }
}
