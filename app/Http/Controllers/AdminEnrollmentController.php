<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('course_user')
            ->join('users',   'course_user.user_id',   '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->select(
                'course_user.user_id',
                'course_user.course_id',
                'course_user.progress',
                'course_user.created_at as enrolled_at',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role',
                'courses.title as course_title',
                'courses.category as course_category',
            );

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('users.name',      'like', "%{$s}%")
                  ->orWhere('users.email',   'like', "%{$s}%")
                  ->orWhere('courses.title', 'like', "%{$s}%");
            });
        }
        if ($request->filled('course_id')) {
            $query->where('course_user.course_id', $request->course_id);
        }
        if ($request->filled('progress')) {
            match($request->progress) {
                'not_started' => $query->where('course_user.progress', 0),
                'in_progress' => $query->whereBetween('course_user.progress', [1, 99]),
                'completed'   => $query->where('course_user.progress', 100),
                default       => null,
            };
        }

        $enrollments = $query->orderByDesc('course_user.created_at')
                             ->paginate(20)
                             ->withQueryString();

        $users    = User::orderBy('name')->get(['id', 'name', 'email']);
        $courses  = Course::orderBy('title')->get(['id', 'title']);

        $totalEnrollments = DB::table('course_user')->count();
        $completed        = DB::table('course_user')->where('progress', 100)->count();
        $inProgress       = DB::table('course_user')->whereBetween('progress', [1, 99])->count();
        $notStarted       = DB::table('course_user')->where('progress', 0)->count();

        return view('admin.enrollments', compact(
            'enrollments', 'courses', 'users',
            'totalEnrollments', 'completed', 'inProgress', 'notStarted'
        ));
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'progress'  => 'nullable|integer|min:0|max:100',
        ]);

        $exists = DB::table('course_user')
            ->where('user_id', $request->user_id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            DB::table('course_user')
                ->where('user_id', $request->user_id)
                ->where('course_id', $request->course_id)
                ->update([
                    'progress'   => $request->progress ?? 0,
                    'updated_at' => now(),
                ]);
            $msg = 'Student course progress updated successfully.';
        } else {
            DB::table('course_user')->insert([
                'user_id'    => $request->user_id,
                'course_id'  => $request->course_id,
                'progress'   => $request->progress ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $msg = 'Student enrolled successfully.';
        }

        $user   = User::find($request->user_id);
        $course = Course::find($request->course_id);
        if ($user && $course) {
            \App\Helpers\ActivityLogger::log('enrollment.created', 'Course', $course->id, $course->title, "Enrolled {$user->name} ({$user->email})");
        }

        return back()->with('success', $msg);
    }

    public function unenroll(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        DB::table('course_user')
            ->where('user_id',   $request->user_id)
            ->where('course_id', $request->course_id)
            ->delete();

        return back()->with('success', 'Student unenrolled successfully.');
    }
}
