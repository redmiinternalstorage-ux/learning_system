<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\GoogleMailHelper;
use App\Models\CampusDomain;
use App\Models\CampusEnrollment;
use App\Models\CampusStudent;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminCampusController extends Controller
{
    // ── Dashboard ──────────────────────────────────────────────
    public function index()
    {
        $stats = [
            'total'    => CampusStudent::count(),
            'pending'  => CampusStudent::where('status', 'pending')->count(),
            'approved' => CampusStudent::where('status', 'approved')->count(),
            'rejected' => CampusStudent::where('status', 'rejected')->count(),
            'domains'  => CampusDomain::where('is_active', true)->count(),
            'enrolled' => CampusEnrollment::count(),
            'completed'=> CampusEnrollment::where('progress', 100)->count(),
        ];

        $recent = CampusStudent::latest()->take(10)->get();

        return view('admin.campus.index', compact('stats', 'recent'));
    }

    // ── Students List ──────────────────────────────────────────
    public function students(Request $request)
    {
        $query = CampusStudent::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('college_email', 'like', "%{$s}%")
                  ->orWhere('college_name', 'like', "%{$s}%");
            });
        }

        $students = $query->paginate(15)->withQueryString();
        return view('admin.campus.students', compact('students'));
    }

    public function showStudent(CampusStudent $student)
    {
        $enrollments = CampusEnrollment::with('course')
            ->where('campus_student_id', $student->id)->get();
        return view('admin.campus.student-detail', compact('student', 'enrollments'));
    }

    // ── Approve ────────────────────────────────────────────────
    public function approve(CampusStudent $student)
    {
        $student->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        ActivityLogger::log('campus.approved', 'CampusStudent', $student->id, $student->name, "College: {$student->college_name}");

        $this->sendStatusMail($student, 'approved');

        return back()->with('success', "✅ {$student->name} approved successfully!");
    }

    // ── Reject ─────────────────────────────────────────────────
    public function reject(Request $request, CampusStudent $student)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $student->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason ?? 'Application not approved.',
        ]);

        ActivityLogger::log('campus.rejected', 'CampusStudent', $student->id, $student->name);
        $this->sendStatusMail($student, 'rejected');

        return back()->with('success', "{$student->name} rejected.");
    }

    // ── Delete ─────────────────────────────────────────────────
    public function deleteStudent(CampusStudent $student)
    {
        $name = $student->name;
        $student->delete();
        ActivityLogger::log('campus.deleted', 'CampusStudent', null, $name);
        return back()->with('success', "Campus student \"{$name}\" deleted.");
    }

    // ── Domains ────────────────────────────────────────────────
    public function domains()
    {
        $domains = CampusDomain::latest()->paginate(20);
        return view('admin.campus.domains', compact('domains'));
    }

    public function storeDomain(Request $request)
    {
        $request->validate([
            'domain'       => 'required|string|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:campus_domains,domain',
            'college_name' => 'required|string|max:255',
        ]);

        CampusDomain::create([
            'domain'       => strtolower($request->domain),
            'college_name' => $request->college_name,
            'is_active'    => true,
        ]);

        ActivityLogger::log('campus.domain_added', 'CampusDomain', null, $request->domain);
        return back()->with('success', "Domain @{$request->domain} added for {$request->college_name}.");
    }

    public function toggleDomain(CampusDomain $domain)
    {
        $domain->update(['is_active' => !$domain->is_active]);
        $status = $domain->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Domain @{$domain->domain} {$status}.");
    }

    public function deleteDomain(CampusDomain $domain)
    {
        $d = $domain->domain;
        $domain->delete();
        ActivityLogger::log('campus.domain_deleted', 'CampusDomain', null, $d);
        return back()->with('success', "Domain @{$d} deleted.");
    }

    // ── Stats / Analytics ──────────────────────────────────────
    public function analytics()
    {
        try {
            $topCourses = CampusEnrollment::with('course')
                ->selectRaw('course_id, COUNT(*) as count, AVG(progress) as avg_progress')
                ->groupBy('course_id')
                ->orderByDesc('count')
                ->take(10)->get();
        } catch (\Throwable $e) {
            $topCourses = collect();
        }

        try {
            $collegeBreakdown = CampusStudent::where('status', 'approved')
                ->selectRaw('college_name, COUNT(*) as count')
                ->groupBy('college_name')
                ->orderByDesc('count')
                ->get();
        } catch (\Throwable $e) {
            $collegeBreakdown = collect();
        }

        try {
            $driver = \Illuminate\Support\Facades\DB::getDriverName();
            if ($driver === 'pgsql') {
                $monthlyRegistrations = CampusStudent::selectRaw("TO_CHAR(DATE_TRUNC('month', created_at), 'Mon YYYY') as month, COUNT(*) as count")
                    ->groupByRaw("DATE_TRUNC('month', created_at)")
                    ->orderByRaw("DATE_TRUNC('month', created_at)")
                    ->take(12)->get();
            } elseif ($driver === 'sqlite') {
                $monthlyRegistrations = CampusStudent::selectRaw("strftime('%b %Y', created_at) as month, COUNT(*) as count")
                    ->groupByRaw("strftime('%Y-%m', created_at)")
                    ->take(12)->get();
            } else {
                $monthlyRegistrations = CampusStudent::selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, COUNT(*) as count")
                    ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
                    ->take(12)->get();
            }
        } catch (\Throwable $e) {
            $monthlyRegistrations = collect();
        }

        return view('admin.campus.analytics', compact('topCourses', 'collegeBreakdown', 'monthlyRegistrations'));
    }

    // ── Helper: Status Mail ────────────────────────────────────
    private function sendStatusMail(CampusStudent $student, string $status): void
    {
        try {
            if ($status === 'approved') {
                $html = "
                <div style='font-family:sans-serif;max-width:520px;margin:auto;padding:32px;background:#f0fdf4;border-radius:16px;'>
                    <h2 style='color:#065f46;'>🎉 Application Approved!</h2>
                    <p style='color:#374151;'>Hi <strong>{$student->name}</strong>,</p>
                    <p style='color:#374151;'>Your Campus Learning Program application has been <strong style='color:#059669;'>approved</strong>! You can now log in and access free courses.</p>
                    <a href='" . url('/campus/login') . "' style='display:inline-block;padding:12px 28px;background:#059669;color:#fff;border-radius:10px;text-decoration:none;font-weight:bold;margin-top:16px;'>Login to Campus Portal →</a>
                    <p style='color:#6b7280;font-size:12px;margin-top:24px;'>— Learning Curve Campus Program</p>
                </div>";
                GoogleMailHelper::sendRaw($student->college_email, '✅ Campus Application Approved — Learning Curve', $html);
            } else {
                $html = "
                <div style='font-family:sans-serif;max-width:520px;margin:auto;padding:32px;background:#fff1f2;border-radius:16px;'>
                    <h2 style='color:#9f1239;'>Application Update</h2>
                    <p style='color:#374151;'>Hi <strong>{$student->name}</strong>,</p>
                    <p style='color:#374151;'>Unfortunately, your Campus Learning Program application was <strong style='color:#e11d48;'>not approved</strong>.</p>
                    <p style='color:#374151;'><strong>Reason:</strong> {$student->rejection_reason}</p>
                    <p style='color:#6b7280;font-size:12px;margin-top:24px;'>If you believe this is an error, please contact admin. — Learning Curve</p>
                </div>";
                GoogleMailHelper::sendRaw($student->college_email, 'Campus Application Update — Learning Curve', $html);
            }
        } catch (\Throwable $e) {
            Log::warning("Campus status mail failed: " . $e->getMessage());
        }
    }
}
