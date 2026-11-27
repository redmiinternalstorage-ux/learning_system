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
            $driver = DB::getDriverName(); // 'pgsql', 'mysql', or 'sqlite'

            // ── Helper to get month-grouped data ─────────────────────────────
            $monthlyQuery = function($query, string $dateCol) use ($driver) {
                if ($driver === 'pgsql') {
                    return $query->selectRaw(
                        "TO_CHAR({$dateCol}, 'Mon YYYY') as month, TO_CHAR({$dateCol}, 'YYYY-MM') as sort_key, COUNT(*) as count"
                    );
                } elseif ($driver === 'sqlite') {
                    return $query->selectRaw(
                        "strftime('%b %Y', {$dateCol}) as month, strftime('%Y-%m', {$dateCol}) as sort_key, COUNT(*) as count"
                    );
                } else {
                    return $query->selectRaw(
                        "DATE_FORMAT({$dateCol}, '%b %Y') as month, DATE_FORMAT({$dateCol}, '%Y-%m') as sort_key, COUNT(*) as count"
                    );
                }
            };

            $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

            // ── User growth (last 7 months) ───────────────────────────────────
            try {
                $userGrowth = $monthlyQuery(User::where('created_at', '>=', $sixMonthsAgo), 'created_at')
                    ->groupBy('month', 'sort_key')
                    ->orderBy('sort_key')
                    ->get();
            } catch (\Throwable $e) {
                $userGrowth = collect();
            }

            // ── Enrollment growth (last 7 months) ─────────────────────────────
            try {
                $enrollmentGrowth = $monthlyQuery(DB::table('course_user')->where('created_at', '>=', $sixMonthsAgo), 'created_at')
                    ->groupBy('month', 'sort_key')
                    ->orderBy('sort_key')
                    ->get();
            } catch (\Throwable $e) {
                $enrollmentGrowth = collect();
            }

            // ── Mail activity (last 7 months) ─────────────────────────────────
            try {
                $mailGrowth = $monthlyQuery(MailLog::where('created_at', '>=', $sixMonthsAgo), 'created_at')
                    ->groupBy('month', 'sort_key')
                    ->orderBy('sort_key')
                    ->get();
            } catch (\Throwable $e) {
                $mailGrowth = collect();
            }

            // ── User roles breakdown ───────────────────────────────────────────
            try {
                $roleBreakdown = User::selectRaw('role, COUNT(*) as count')
                    ->groupBy('role')
                    ->pluck('count', 'role');
            } catch (\Throwable $e) {
                $roleBreakdown = collect();
            }

            // ── Top 5 courses by enrollment ────────────────────────────────────
            try {
                $topCourses = Course::withCount('users')
                    ->orderByDesc('users_count')
                    ->take(5)
                    ->get();
            } catch (\Throwable $e) {
                $topCourses = collect();
            }

            // ── Submission stats ───────────────────────────────────────────────
            try {
                $submissionStats = [
                    'total'   => TaskSubmission::count(),
                    'graded'  => TaskSubmission::where('status', 'graded')->count(),
                    'pending' => TaskSubmission::where('status', 'pending')->count(),
                ];
            } catch (\Throwable $e) {
                $submissionStats = ['total' => 0, 'graded' => 0, 'pending' => 0];
            }

            // ── Top graded students ────────────────────────────────────────────
            try {
                $topStudents = User::where('role', 'student')
                    ->withCount('submissions')
                    ->withSum('submissions', 'grade')
                    ->orderByDesc('submissions_sum_grade')
                    ->take(5)
                    ->get();
            } catch (\Throwable $e) {
                $topStudents = collect();
            }

            // ── Mail type breakdown ────────────────────────────────────────────
            try {
                $mailTypes = MailLog::selectRaw('mail_type, COUNT(*) as count')
                    ->groupBy('mail_type')
                    ->orderByDesc('count')
                    ->get();
            } catch (\Throwable $e) {
                $mailTypes = collect();
            }

            // ── New registrations today / this week / this month ───────────────
            try {
                $registrations = [
                    'today' => User::whereDate('created_at', today())->count(),
                    'week'  => User::where('created_at', '>=', now()->startOfWeek())->count(),
                    'month' => User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                ];
            } catch (\Throwable $e) {
                $registrations = ['today' => 0, 'week' => 0, 'month' => 0];
            }

            // ── Course category breakdown ──────────────────────────────────────
            try {
                $categoryBreakdown = Course::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->orderByDesc('count')
                    ->get();
            } catch (\Throwable $e) {
                $categoryBreakdown = collect();
            }

            return compact(
                'userGrowth', 'enrollmentGrowth', 'mailGrowth',
                'roleBreakdown', 'topCourses', 'submissionStats',
                'topStudents', 'mailTypes', 'registrations', 'categoryBreakdown'
            );
        });

        return view('admin.analytics', $data);
    }
}
