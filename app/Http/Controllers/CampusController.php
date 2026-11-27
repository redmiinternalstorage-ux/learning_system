<?php

namespace App\Http\Controllers;

use App\Models\CampusDomain;
use App\Models\CampusEnrollment;
use App\Models\CampusStudent;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Helpers\GoogleMailHelper;

class CampusController extends Controller
{
    // ── Landing / Register ─────────────────────────────────────
    public function landing()
    {
        if (Auth::guard('campus')->check()) {
            return redirect()->route('campus.dashboard');
        }
        return view('campus.auth.index');
    }

    public function showRegister()
    {
        if (Auth::guard('campus')->check()) {
            return redirect()->route('campus.dashboard');
        }
        return view('campus.auth.index', ['mode' => 'register']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'college_email' => 'required|email|unique:campus_students,college_email',
            'password'      => 'required|min:8|confirmed',
            'student_id'    => 'nullable|string|max:50',
            'department'    => 'nullable|string|max:100',
            'semester'      => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
        ], [
            'college_email.unique' => 'This college email is already registered.',
        ]);

        // Check if domain is allowed
        if (!CampusDomain::isAllowed($request->college_email)) {
            return back()->withErrors(['college_email' => 'Your college email domain is not registered. Please contact admin to add your institution.'])->withInput();
        }

        $domain = CampusDomain::findByEmail($request->college_email);

        // Generate OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $student = CampusStudent::create([
            'name'          => $request->name,
            'college_email' => strtolower($request->college_email),
            'password'      => Hash::make($request->password),
            'college_name'  => $domain?->college_name,
            'student_id'    => $request->student_id,
            'department'    => $request->department,
            'semester'      => $request->semester,
            'phone'         => $request->phone,
            'status'        => 'pending',
            'otp'           => $otp,
            'otp_expires_at'=> now()->addMinutes(10),
            'email_verified'=> false,
        ]);

        // Send verification OTP
        $this->sendOtpMail($student->college_email, $student->name, $otp);

        session(['campus_otp_email' => $student->college_email]);

        return redirect()->route('campus.verify-email')
            ->with('success', 'OTP sent to ' . $student->college_email . '. Please verify your email.');
    }

    // ── Email Verification ─────────────────────────────────────
    public function showVerifyEmail()
    {
        if (!session('campus_otp_email')) {
            return redirect()->route('campus.login');
        }
        return view('campus.auth.verify-email', ['email' => session('campus_otp_email')]);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        $email = session('campus_otp_email');

        if (!$email) {
            return redirect()->route('campus.login');
        }

        $student = CampusStudent::where('college_email', $email)->first();

        if (!$student || $student->otp !== $request->otp || now()->isAfter($student->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $student->update([
            'email_verified' => true,
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        session()->forget('campus_otp_email');

        return redirect()->route('campus.login')
            ->with('success', '✅ Email verified! Your application is pending admin approval. You will be notified once approved.');
    }

    public function resendOtp(Request $request)
    {
        $email = session('campus_otp_email');
        if (!$email) return redirect()->route('campus.login');

        $student = CampusStudent::where('college_email', $email)->first();
        if (!$student) return redirect()->route('campus.login');

        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $student->update(['otp' => $otp, 'otp_expires_at' => now()->addMinutes(10)]);
        $this->sendOtpMail($student->college_email, $student->name, $otp);

        return back()->with('success', 'New OTP sent!');
    }

    // ── Login ──────────────────────────────────────────────────
    public function showLogin()
    {
        if (Auth::guard('campus')->check()) {
            return redirect()->route('campus.dashboard');
        }
        return view('campus.auth.index', ['mode' => 'login']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'college_email' => 'required|email',
            'password'      => 'required',
        ]);

        $student = CampusStudent::where('college_email', strtolower($request->college_email))->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return back()->withErrors(['college_email' => 'Invalid email or password.'])->onlyInput('college_email');
        }

        if (!$student->email_verified) {
            session(['campus_otp_email' => $student->college_email]);
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $student->update(['otp' => $otp, 'otp_expires_at' => now()->addMinutes(10)]);
            $this->sendOtpMail($student->college_email, $student->name, $otp);
            return redirect()->route('campus.verify-email')->with('info', 'Please verify your email first.');
        }

        if ($student->status === 'pending') {
            return back()->withErrors(['college_email' => 'Your application is pending admin approval. Please wait.']);
        }

        if ($student->status === 'rejected') {
            return back()->withErrors(['college_email' => 'Your application was rejected. Reason: ' . ($student->rejection_reason ?? 'Contact admin.')]);
        }

        Auth::guard('campus')->login($student, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('campus.dashboard')
            ->with('welcome', 'Welcome back, ' . $student->name . '! 🎓');
    }

    public function logout(Request $request)
    {
        Auth::guard('campus')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('campus.login');
    }

    // ── Dashboard ──────────────────────────────────────────────
    public function dashboard()
    {
        $student     = Auth::guard('campus')->user();
        try {
            $enrollments = CampusEnrollment::with('course')
                ->where('campus_student_id', $student->id)
                ->latest()->get();
        } catch (\Throwable $e) {
            $enrollments = collect();
        }

        $totalCourses    = $enrollments->count();
        $completed       = $enrollments->where('progress', 100)->count();
        $inProgress      = $enrollments->where('progress', '>', 0)->where('progress', '<', 100)->count();
        $totalProgress   = $totalCourses > 0 ? round($enrollments->avg('progress')) : 0;

        try {
            $availableCourses = Course::where('is_published', true)
                ->whereNotIn('id', $enrollments->pluck('course_id'))
                ->latest()->take(6)->get();
        } catch (\Throwable $e) {
            $availableCourses = collect();
        }

        return view('campus.dashboard', compact(
            'student', 'enrollments', 'totalCourses', 'completed',
            'inProgress', 'totalProgress', 'availableCourses'
        ));
    }

    // ── Courses ────────────────────────────────────────────────
    public function courses()
    {
        $student     = Auth::guard('campus')->user();
        try {
            $enrolledIds = CampusEnrollment::where('campus_student_id', $student->id)->pluck('course_id');
            $courses     = Course::where('is_published', true)->latest()->paginate(12);
        } catch (\Throwable $e) {
            $enrolledIds = collect();
            $courses     = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 12);
        }

        return view('campus.courses', compact('student', 'courses', 'enrolledIds'));
    }

    public function enroll(Request $request, Course $course)
    {
        $student = Auth::guard('campus')->user();

        try {
            $exists = CampusEnrollment::where('campus_student_id', $student->id)
                ->where('course_id', $course->id)->exists();

            if ($exists) {
                return back()->with('info', 'Already enrolled in this course.');
            }

            CampusEnrollment::create([
                'campus_student_id' => $student->id,
                'course_id'         => $course->id,
                'progress'          => 0,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Enrollment failed. Please try again.');
        }

        return back()->with('success', '✅ Enrolled in "' . $course->title . '" successfully!');
    }

    public function courseDetail(Course $course)
    {
        $student    = Auth::guard('campus')->user();
        try {
            $enrollment = CampusEnrollment::where('campus_student_id', $student->id)
                ->where('course_id', $course->id)->first();

            if (!$enrollment) {
                return redirect()->route('campus.courses')->with('error', 'Please enroll first.');
            }

            $tasks = $course->tasks()->latest()->get();
        } catch (\Throwable $e) {
            return redirect()->route('campus.courses')->with('error', 'Could not load course. Please try again.');
        }

        return view('campus.course-detail', compact('student', 'course', 'enrollment', 'tasks'));
    }

    public function updateProgress(Request $request, Course $course)
    {
        $request->validate(['progress' => 'required|integer|min:0|max:100']);
        $student = Auth::guard('campus')->user();

        $enrollment = CampusEnrollment::where('campus_student_id', $student->id)
            ->where('course_id', $course->id)->firstOrFail();

        $data = ['progress' => $request->progress];
        if ($request->progress >= 100) {
            $data['completed_at'] = now();
        }

        $enrollment->update($data);

        return response()->json(['success' => true, 'progress' => $request->progress]);
    }

    // ── Profile ────────────────────────────────────────────────
    public function profile()
    {
        $student = Auth::guard('campus')->user();
        return view('campus.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = Auth::guard('campus')->user();
        $request->validate([
            'name'       => 'required|string|max:255',
            'student_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'semester'   => 'nullable|string|max:20',
            'phone'      => 'nullable|string|max:20',
        ]);

        $student->update($request->only('name', 'student_id', 'department', 'semester', 'phone'));

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $student->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    // ── Progress ───────────────────────────────────────────────
    public function progress()
    {
        $student     = Auth::guard('campus')->user();
        try {
            $enrollments = CampusEnrollment::with('course')
                ->where('campus_student_id', $student->id)
                ->latest()->get();
        } catch (\Throwable $e) {
            $enrollments = collect();
        }

        return view('campus.progress', compact('student', 'enrollments'));
    }

    // ── Leaderboard ────────────────────────────────────────────
    public function leaderboard()
    {
        $student = Auth::guard('campus')->user();

        try {
            $leaders = CampusStudent::where('status', 'approved')
                ->withCount(['enrollments as completed_count' => fn($q) => $q->where('progress', 100)])
                ->withCount('enrollments as total_enrollments')
                ->orderByDesc('completed_count')
                ->take(20)->get();
        } catch (\Throwable $e) {
            $leaders = collect();
        }

        return view('campus.leaderboard', compact('student', 'leaders'));
    }

    // ── Helper: Send OTP Mail ──────────────────────────────────
    private function sendOtpMail(string $to, string $name, string $otp): void
    {
        try {
            $html = "
            <div style='font-family:sans-serif;max-width:480px;margin:auto;padding:32px;background:#f0fdf4;border-radius:16px;'>
                <h2 style='color:#065f46;'>🎓 Campus Learning — Email Verification</h2>
                <p style='color:#374151;'>Hi <strong>{$name}</strong>,</p>
                <p style='color:#374151;'>Your OTP for email verification is:</p>
                <div style='font-size:36px;font-weight:900;color:#059669;letter-spacing:8px;text-align:center;padding:16px;background:#fff;border-radius:12px;border:2px solid #a7f3d0;margin:16px 0;'>{$otp}</div>
                <p style='color:#6b7280;font-size:13px;'>This OTP expires in 10 minutes. Do not share it.</p>
                <p style='color:#6b7280;font-size:12px;margin-top:24px;'>— Learning Curve Campus Program</p>
            </div>";

            GoogleMailHelper::sendRaw($to, '🎓 Campus OTP Verification — Learning Curve', $html);
        } catch (\Throwable $e) {
            Log::warning("Campus OTP mail failed to {$to}: " . $e->getMessage());
        }
    }
}
