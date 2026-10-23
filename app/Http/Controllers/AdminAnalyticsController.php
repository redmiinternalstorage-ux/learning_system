<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MailLog;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        $data = Cache::remember('admin_analytics_data', 120, function () {
            $isSqlite = DB::getDriverName() === 'sqlite';

            // ── User growth (last 7 months) ──────────────────────────────────
            if ($isSqlite) {
                $userGrowth = User::selectRaw("strftime('%Y-%m', created_at) as sort_key, COUNT(*) as count")
                    ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                    ->groupBy('sort_key')
                    ->orderBy('sort_key')
                    ->get()
                    ->map(function($item) {
                        $item->month = \Carbon\Carbon::createFromFormat('Y-m', $item->sort_key)->format('b Y');
                        return $item;
                    });
            } else {
                $userGrowth = User::selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as count")
                    ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                    ->groupBy('month', 'sort_key')
                    ->orderBy('sort_key')
                    ->get();
            }

            // ── Enrollment growth (last 7 months) ───────────────────────────
            if ($isSqlite) {
                $enrollmentGrowth = DB::table('course_user')
                    ->selectRaw("strftime('%Y-%m', created_at) as sort_key, COUNT(*) as count")
                    ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                    ->groupBy('sort_key')
                    ->orderBy('sort_key')
                    ->get()
                    ->map(function($item) {
                        $item->month = \Carbon\Carbon::createFromFormat('Y-m', $item->sort_key)->format('b Y');
                        return $item;
                    });
            } else {
                $enrollmentGrowth = DB::table('course_user')
                    ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as count")
                    ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                    ->groupBy('month', 'sort_key')
                    ->orderBy('sort_key')
                    ->get();
            }

            // ── Mail activity (last 7 months) ────────────────────────────────
            if ($isSqlite) {
                $mailGrowth = MailLog::selectRaw("strftime('%Y-%m', created_at) as sort_key, COUNT(*) as count")
                    ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                    ->groupBy('sort_key')
                    ->orderBy('sort_key')
                    ->get()
                    ->map(function($item) {
                        $item->month = \Carbon\Carbon::createFromFormat('Y-m', $item->sort_key)->format('b Y');
                        return $item;
                    });
            } else {
                $mailGrowth = MailLog::selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as count")
                    ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                    ->groupBy('month', 'sort_key')
                    ->orderBy('sort_key')
                    ->get();
            }

            // ── User roles breakdown ─────────────────────────────────────────
            $roleBreakdown = User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role');

            // ── Top 5 courses by enrollment ──────────────────────────────────
            $topCourses = Course::withCount('users')
                ->orderByDesc('users_count')
                ->take(5)
                ->get();

            // ── Submission stats ─────────────────────────────────────────────
            $submissionStats = [
                'total'   => TaskSubmission::count(),
                'graded'  => TaskSubmission::where('status', 'graded')->count(),
                'pending' => TaskSubmission::where('status', 'pending')->count(),
            ];

            // ── Top graded students ──────────────────────────────────────────
            $topStudents = User::where('role', 'student')
                ->withCount('submissions')
                ->withSum('submissions', 'grade')
                ->orderByDesc('submissions_sum_grade')
                ->take(5)
                ->get();

            // ── Mail type breakdown ──────────────────────────────────────────
            $mailTypes = MailLog::selectRaw('mail_type, COUNT(*) as count')
                ->groupBy('mail_type')
                ->orderByDesc('count')
                ->get();

            // ── New registrations today / this week / this month ─────────────
            $registrations = [
                'today'  => User::whereDate('created_at', today())->count(),
                'week'   => User::where('created_at', '>=', now()->startOfWeek())->count(),
                'month'  => User::whereMonth('created_at', now()->month)->count(),
            ];

            // ── Course category breakdown ────────────────────────────────────
            $categoryBreakdown = Course::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderByDesc('count')
                ->get();

            return compact(
                'userGrowth', 'enrollmentGrowth', 'mailGrowth',
                'roleBreakdown', 'topCourses', 'submissionStats',
                'topStudents', 'mailTypes', 'registrations', 'categoryBreakdown'
            );
        });

        return view('admin.analytics', $data);
    }
}
