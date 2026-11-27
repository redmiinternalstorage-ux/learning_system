<?php

namespace App\Http\Controllers;

use App\Models\CollegeAdmin;
use App\Models\CampusStudent;
use App\Models\CampusDomain;
use App\Models\CampusEnrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CollegeAdminController extends Controller
{
    // ── Auth: Show Login ───────────────────────────────────────
    public function showLogin()
    {
        if (Auth::guard('college')->check()) {
            return redirect()->route('college.dashboard');
        }
        return view('college.auth.login');
    }

    // ── Auth: Login ────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $college = CollegeAdmin::where('email', strtolower($request->email))->first();

        if (!$college || !Hash::check($request->password, $college->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        if ($college->isPending()) {
            return back()->withErrors(['email' => 'Your college account is pending activation. Please wait for Learning Curve admin approval.']);
        }

        if (!$college->isActive()) {
            return back()->withErrors(['email' => 'Your college account has been deactivated. Contact Learning Curve support.']);
        }

        Auth::guard('college')->login($college, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('college.dashboard')
            ->with('welcome', 'Welcome back, ' . $college->college_name . '! 🏛️');
    }

    // ── Auth: Logout ───────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::guard('college')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('college.login');
    }

    // ── Auth: Register (College applies for access) ────────────
    public function showRegister()
    {
        if (Auth::guard('college')->check()) {
            return redirect()->route('college.dashboard');
        }
        return view('college.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'college_name'   => 'required|string|max:255',
            'email'          => 'required|email|unique:college_admins,email',
            'password'       => 'required|min:8|confirmed',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'website'        => 'nullable|url|max:255',
            'domain'         => 'nullable|string|max:100',
        ], [
            'email.unique' => 'This email is already registered as a college account.',
        ]);

        CollegeAdmin::create([
            'college_name'   => $request->college_name,
            'email'          => strtolower($request->email),
            'password'       => Hash::make($request->password),
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'website'        => $request->website,
            'domain'         => $request->domain ? strtolower($request->domain) : null,
            'status'         => 'pending',
        ]);

        return redirect()->route('college.login')
            ->with('success', '✅ College account application submitted! You will be notified once activated by Learning Curve admin.');
    }

    // ── Dashboard ──────────────────────────────────────────────
    public function dashboard()
    {
        $college  = Auth::guard('college')->user();
        $students = $college->students()->latest()->get();

        $stats = [
            'total'    => $students->count(),
            'approved' => $students->where('status', 'approved')->count(),
            'pending'  => $students->where('status', 'pending')->count(),
            'rejected' => $students->where('status', 'rejected')->count(),
        ];

        $recentStudents = $students->take(5);

        return view('college.dashboard', compact('college', 'stats', 'recentStudents'));
    }

    // ── Students: List ─────────────────────────────────────────
    public function students(Request $request)
    {
        $college = Auth::guard('college')->user();
        $query   = $college->students();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('college_email', 'like', "%{$s}%")
                  ->orWhere('student_id', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $students    = $query->latest()->paginate(15)->withQueryString();
        $departments = $college->students()->distinct()->pluck('department')->filter()->sort();

        return view('college.students', compact('college', 'students', 'departments'));
    }

    // ── Students: Add Manually ─────────────────────────────────
    public function addStudent(Request $request)
    {
        $college = Auth::guard('college')->user();

        $request->validate([
            'name'          => 'required|string|max:255',
            'college_email' => 'required|email|unique:campus_students,college_email',
            'student_id'    => 'nullable|string|max:50',
            'department'    => 'nullable|string|max:100',
            'semester'      => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'required|min:6',
        ], [
            'college_email.unique' => 'This email is already registered.',
        ]);

        CampusStudent::create([
            'name'             => $request->name,
            'college_email'    => strtolower($request->college_email),
            'password'         => Hash::make($request->password),
            'college_name'     => $college->college_name,
            'student_id'       => $request->student_id,
            'department'       => $request->department,
            'semester'         => $request->semester,
            'phone'            => $request->phone,
            'status'           => 'approved',          // auto-approved by college admin
            'email_verified'   => true,                // college admin vouches
            'college_admin_id' => $college->id,
            'approved_at'      => now(),
        ]);

        return back()->with('success', '✅ Student added and approved successfully!');
    }

    // ── Students: Bulk Import (CSV) ────────────────────────────
    public function importStudents(Request $request)
    {
        $college = Auth::guard('college')->user();
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file  = $request->file('csv_file');
        $rows  = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($rows); // remove header row
        $header = array_map('strtolower', array_map('trim', $header));

        $added   = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($rows as $i => $row) {
            if (count($row) < 2) continue;
            $data = array_combine($header, array_pad($row, count($header), ''));

            $email = strtolower(trim($data['email'] ?? $data['college_email'] ?? ''));
            $name  = trim($data['name'] ?? '');

            if (!$email || !$name) { $skipped++; continue; }
            if (CampusStudent::where('college_email', $email)->exists()) { $skipped++; continue; }

            try {
                CampusStudent::create([
                    'name'             => $name,
                    'college_email'    => $email,
                    'password'         => Hash::make($data['password'] ?? 'password123'),
                    'college_name'     => $college->college_name,
                    'student_id'       => trim($data['student_id'] ?? $data['roll_no'] ?? ''),
                    'department'       => trim($data['department'] ?? $data['dept'] ?? ''),
                    'semester'         => trim($data['semester'] ?? $data['sem'] ?? ''),
                    'phone'            => trim($data['phone'] ?? ''),
                    'status'           => 'approved',
                    'email_verified'   => true,
                    'college_admin_id' => $college->id,
                    'approved_at'      => now(),
                ]);
                $added++;
            } catch (\Throwable $e) {
                $errors[] = "Row " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        $msg = "Import done: {$added} added, {$skipped} skipped.";
        if ($errors) $msg .= ' Errors: ' . implode(' | ', array_slice($errors, 0, 3));

        return back()->with('success', $msg);
    }

    // ── Students: Edit ─────────────────────────────────────────
    public function editStudent(CampusStudent $student)
    {
        $college = Auth::guard('college')->user();
        abort_if($student->college_admin_id !== $college->id, 403);
        return view('college.student-edit', compact('college', 'student'));
    }

    public function updateStudent(Request $request, CampusStudent $student)
    {
        $college = Auth::guard('college')->user();
        abort_if($student->college_admin_id !== $college->id, 403);

        $request->validate([
            'name'       => 'required|string|max:255',
            'student_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'semester'   => 'nullable|string|max:20',
            'phone'      => 'nullable|string|max:20',
            'status'     => 'required|in:pending,approved,rejected',
        ]);

        $student->update($request->only('name', 'student_id', 'department', 'semester', 'phone', 'status'));

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $student->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('college.students')
            ->with('success', 'Student updated successfully.');
    }

    // ── Students: Delete ───────────────────────────────────────
    public function deleteStudent(CampusStudent $student)
    {
        $college = Auth::guard('college')->user();
        abort_if($student->college_admin_id !== $college->id, 403);
        $student->delete();
        return back()->with('success', 'Student removed.');
    }

    // ── Students: Toggle Approve/Reject ────────────────────────
    public function toggleStudentStatus(Request $request, CampusStudent $student)
    {
        $college = Auth::guard('college')->user();
        abort_if($student->college_admin_id !== $college->id, 403);

        $request->validate(['status' => 'required|in:approved,rejected,pending']);
        $student->update([
            'status'      => $request->status,
            'approved_at' => $request->status === 'approved' ? now() : null,
        ]);

        return back()->with('success', 'Student status updated to ' . $request->status . '.');
    }

    // ── Students: Courses Access ───────────────────────────────
    public function studentCourses(CampusStudent $student)
    {
        $college = Auth::guard('college')->user();
        abort_if($student->college_admin_id !== $college->id, 403);

        $enrollments   = CampusEnrollment::with('course')->where('campus_student_id', $student->id)->get();
        $allCourses    = Course::where('is_published', true)->get();
        $enrolledIds   = $enrollments->pluck('course_id');

        return view('college.student-courses', compact('college', 'student', 'enrollments', 'allCourses', 'enrolledIds'));
    }

    public function enrollStudentCourse(Request $request, CampusStudent $student)
    {
        $college = Auth::guard('college')->user();
        abort_if($student->college_admin_id !== $college->id, 403);

        $request->validate(['course_id' => 'required|exists:courses,id']);

        CampusEnrollment::firstOrCreate([
            'campus_student_id' => $student->id,
            'course_id'         => $request->course_id,
        ], ['progress' => 0]);

        return back()->with('success', 'Student enrolled in course.');
    }

    public function unenrollStudentCourse(Request $request, CampusStudent $student)
    {
        $college = Auth::guard('college')->user();
        abort_if($student->college_admin_id !== $college->id, 403);

        CampusEnrollment::where('campus_student_id', $student->id)
            ->where('course_id', $request->course_id)
            ->delete();

        return back()->with('success', 'Student unenrolled from course.');
    }

    // ── Profile: Update College Info ───────────────────────────
    public function profile()
    {
        $college = Auth::guard('college')->user();
        return view('college.profile', compact('college'));
    }

    public function updateProfile(Request $request)
    {
        $college = Auth::guard('college')->user();
        $request->validate([
            'college_name'   => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'website'        => 'nullable|url|max:255',
        ]);

        $college->update($request->only('college_name', 'contact_person', 'phone', 'address', 'website'));

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $college->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'College profile updated successfully.');
    }
}
