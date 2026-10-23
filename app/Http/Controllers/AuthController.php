<?php

namespace App\Http\Controllers;

use App\Helpers\MailLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function landing()
    {
        if (Auth::check()) {
            return $this->redirectForUser(Auth::user());
        }
        return view('auth.index');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectForUser(Auth::user());
        }
        return view('auth.index');
    }

    private function redirectForUser($user)
    {
        if ($user->hasAdminAccess()) {
            return redirect()->route('admin.panel-select');
        }
        return redirect()->route('dashboard');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectForUser(Auth::user());
        }
        return view('auth.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');
        $authenticated = Auth::attempt($request->only('email', 'password'), $remember);

        if (!$authenticated) {
            $user = User::where('email', $request->email)->first();
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
                return back()->withErrors(['email' => 'Your account has been blocked. Please contact admin.']);
            }

            // Admin/Staff/Finance → redirect to admin portal
            if ($user->hasAdminAccess()) {
                return redirect()->route('admin.panel-select')
                    ->with('welcome', 'Welcome back, ' . $user->name . '!');
            }

            return redirect()->route('dashboard')
                ->with('welcome', 'Welcome back, ' . $user->name . '! 👋');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:student'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Send welcome email and log it
        MailLogger::log(
            'welcome',
            $user->email,
            $user->name,
            $user->id,
            fn() => \Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user)),
            'registration'
        );

        return redirect()->route('login')
            ->with('success', 'Account created successfully! Please log in to continue.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        $enrolledCourses = $user->courses()->orderBy('courses.created_at', 'desc')->take(8)->get();
        $enrolledCount   = $user->courses()->count();
        $hoursLearned    = (int) $user->courses()->sum('courses.duration_hours');
        $completedCount  = \Illuminate\Support\Facades\DB::table('course_user')
            ->where('user_id', $user->id)
            ->where('progress', '>=', 100)
            ->count();

        $stats = [
            'enrolled'     => $enrolledCount,
            'completed'    => $completedCount,
            'certificates' => $completedCount,
            'hours'        => $hoursLearned,
        ];

        $announcements = \Illuminate\Support\Facades\Cache::remember('active_announcements_top5', 300, function () {
            return \App\Models\Announcement::active()->latest()->take(5)->get();
        });

        return view('dashboard', compact('user', 'stats', 'enrolledCourses', 'announcements'));
    }
}
