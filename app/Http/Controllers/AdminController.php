<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\MailLogger;
use App\Models\ActivityLog;
use App\Models\MailLog;
use App\Models\User;
use App\Models\Course;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Mail\AccountBlockedMail;
use App\Mail\AccountDeletedMail;
use App\Mail\AccountUnblockedMail;
use App\Mail\PasswordChangedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = Cache::remember('admin_dashboard_stats', 30, function () {
            return [
                'total_users'       => User::count(),
                'students'          => User::where('role', 'student')->count(),
                'instructors'       => User::where('role', 'instructor')->count(),
                'staff'             => User::where('role', 'staff')->count(),
                'new_this_month'    => User::whereMonth('created_at', now()->month)->count(),
                'total_courses'     => Course::count(),
                'published_courses' => Course::where('is_published', true)->count(),
                'total_tasks'       => Task::count(),
                'total_submissions' => TaskSubmission::count(),
                'pending_grading'   => TaskSubmission::where('status', 'pending')->count(),
                'blocked_users'     => User::where('is_blocked', true)->count(),
                'total_enrollments' => DB::table('course_user')->count(),
                'completed_courses' => DB::table('course_user')->where('progress', 100)->count(),
                'mails_sent'        => MailLog::where('success', true)->count(),
                'activity_today'    => ActivityLog::whereDate('created_at', today())->count(),
            ];
        });
        $recentUsers = User::latest()->take(8)->get();
        $recentSubmissions = TaskSubmission::with(['user', 'task.course'])
            ->latest()
            ->take(5)
            ->get();
        $recentActivity = ActivityLog::with('user')->latest()->take(6)->get();
        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentSubmissions', 'recentActivity'));
    }

    public function users(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            if ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($request->status === 'active') {
                $query->where('is_blocked', false);
            }
        }
        $users = $query->latest()->paginate(15)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function bulkUsers(Request $request)
    {
        $request->validate([
            'action'   => 'required|in:block,unblock,delete,role_student,role_instructor,role_staff',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;
        $users   = User::whereIn('id', $userIds)->get();
        $count   = 0;

        foreach ($users as $u) {
            // Guard: cannot modify main admin
            if ($u->email === 'admin@gmail.com') {
                continue;
            }

            match ($request->action) {
                'block' => (function() use ($u, &$count) {
                    if (!$u->is_blocked && $u->role !== 'admin') {
                        $u->update(['is_blocked' => true]);
                        ActivityLogger::log('user.blocked', 'User', $u->id, $u->name, 'Bulk action block');
                        $count++;
                    }
                })(),
                'unblock' => (function() use ($u, &$count) {
                    if ($u->is_blocked) {
                        $u->update(['is_blocked' => false]);
                        ActivityLogger::log('user.unblocked', 'User', $u->id, $u->name, 'Bulk action unblock');
                        $count++;
                    }
                })(),
                'delete' => (function() use ($u, &$count) {
                    if ($u->role !== 'admin') {
                        $uName = $u->name;
                        $u->delete();
                        ActivityLogger::log('user.deleted', 'User', null, $uName, 'Bulk action delete');
                        $count++;
                    }
                })(),
                'role_student' => (function() use ($u, &$count) {
                    if ($u->role !== 'admin' && $u->role !== 'student') {
                        $u->update(['role' => 'student']);
                        ActivityLogger::log('user.updated', 'User', $u->id, $u->name, 'Role updated to student via bulk action');
                        $count++;
                    }
                })(),
                'role_instructor' => (function() use ($u, &$count) {
                    if ($u->role !== 'admin' && $u->role !== 'instructor') {
                        $u->update(['role' => 'instructor']);
                        ActivityLogger::log('user.updated', 'User', $u->id, $u->name, 'Role updated to instructor via bulk action');
                        $count++;
                    }
                })(),
                'role_staff' => (function() use ($u, &$count) {
                    if ($u->role !== 'admin' && $u->role !== 'staff') {
                        $u->update(['role' => 'staff']);
                        ActivityLogger::log('user.updated', 'User', $u->id, $u->name, 'Role updated to staff via bulk action');
                        $count++;
                    }
                })(),
            };
        }

        return back()->with('success', "Bulk action completed on {$count} accounts.");
    }

    public function getUserDetail(User $user)
    {
        $enrollments = DB::table('course_user')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('course_user.user_id', $user->id)
            ->select('courses.title', 'courses.category', 'course_user.progress', 'course_user.created_at')
            ->get();

        $submissions = TaskSubmission::with('task')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($s) => [
                'task'      => $s->task->title ?? 'Task',
                'status'    => $s->status,
                'grade'     => $s->grade,
                'submitted' => $s->created_at->format('d M Y, h:i A'),
            ]);

        $logsCount = ActivityLog::where('user_id', $user->id)->count();
        $mailCount = MailLog::where('user_id', $user->id)->count();

        return response()->json([
            'user'         => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'is_blocked' => $user->is_blocked,
                'joined'     => $user->created_at->format('d M Y'),
            ],
            'stats'        => [
                'enrollments' => $enrollments->count(),
                'submissions' => $submissions->count(),
                'activity'    => $logsCount,
                'mails'       => $mailCount,
            ],
            'enrollments' => $enrollments,
            'submissions' => $submissions,
        ]);
    }

    public function editUser(User $user)
    {
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role'  => 'required|in:student,instructor,admin,staff',
        ]);

        $user->update($request->only('name', 'email', 'role'));
        ActivityLogger::log('user.updated', 'User', $user->id, $user->name, "Role: {$user->role}, Email: {$user->email}");

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $user->update(['password' => Hash::make($request->password)]);

            // Notify user that their password was changed by admin
            MailLogger::log(
                'password_changed',
                $user->email,
                $user->name,
                $user->id,
                fn() => Mail::to($user->email)->send(new PasswordChangedMail($user->name, now()->format('d M Y, h:i A'))),
                'admin_action'
            );
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be deleted.');
        }

        $userName  = $user->name;
        $userEmail = $user->email;

        $user->delete();
        ActivityLogger::log('user.deleted', 'User', null, $userName, "Email: {$userEmail}");
        // Send account deletion notification email
        MailLogger::log(
            'account_deleted',
            $userEmail,
            $userName,
            null,
            fn() => Mail::to($userEmail)->send(new AccountDeletedMail($userName, $userEmail)),
            'admin_action'
        );

        return back()->with('success', "User \"{$userName}\" deleted and notified via email.");
    }

    // Course management
    public function courses(Request $request)
    {
        $query = Course::withCount(['tasks', 'users']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('instructor_name', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%");
            });
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $courses    = $query->latest()->paginate(10)->withQueryString();
        $categories = Course::select('category')->distinct()->pluck('category')->filter();

        return view('admin.courses.index', compact('courses', 'categories'));
    }

    public function bulkCourses(Request $request)
    {
        $request->validate([
            'action'     => 'required|in:publish,unpublish,delete',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $courses = Course::whereIn('id', $request->course_ids)->get();
        $count   = 0;

        foreach ($courses as $c) {
            match ($request->action) {
                'publish' => (function() use ($c, &$count) {
                    if (!$c->is_published) {
                        $c->update(['is_published' => true]);
                        ActivityLogger::log('course.published', 'Course', $c->id, $c->title, 'Bulk publish');
                        $count++;
                    }
                })(),
                'unpublish' => (function() use ($c, &$count) {
                    if ($c->is_published) {
                        $c->update(['is_published' => false]);
                        ActivityLogger::log('course.unpublished', 'Course', $c->id, $c->title, 'Bulk unpublish');
                        $count++;
                    }
                })(),
                'delete' => (function() use ($c, &$count) {
                    $title = $c->title;
                    $c->delete();
                    ActivityLogger::log('course.deleted', 'Course', null, $title, 'Bulk delete');
                    $count++;
                })(),
            };
        }

        return back()->with('success', "Bulk action completed on {$count} courses.");
    }

    public function createCourse()
    {
        return view('admin.courses.create');
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'     => 'nullable|string',
            'instructor_name' => 'required|string|max:255',
            'category'        => 'required|string',
            'level'           => 'required|string',
            'duration_hours'  => 'required|integer|min:0',
            'lessons_count'   => 'nullable|integer|min:0',
            'price'           => 'nullable|numeric|min:0',
            'video_type'      => 'nullable|in:link,upload',
            'video_link'      => 'nullable|url|required_if:video_type,link',
            'video_file'      => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:512000|required_if:video_type,upload',
        ]);

        $data = $request->only([
            'title','short_description','description','instructor_name',
            'category','level','duration_hours','lessons_count','price','video_type','video_link',
        ]);
        $data['is_published'] = $request->boolean('is_published');

        // Handle video upload
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('course-videos', 'public');
            $data['video_path'] = $path;
            $data['video_link'] = null;
        } elseif ($request->video_type === 'link') {
            $data['video_path'] = null;
        }

        Course::create($data);
        ActivityLogger::log('course.created', 'Course', null, $request->title);
        return redirect()->route('admin.courses')->with('success', 'Course created successfully.');
    }

    public function editCourse(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'     => 'nullable|string',
            'instructor_name' => 'required|string|max:255',
            'category'        => 'required|string',
            'level'           => 'required|string',
            'duration_hours'  => 'required|integer|min:0',
            'lessons_count'   => 'nullable|integer|min:0',
            'price'           => 'nullable|numeric|min:0',
            'video_type'      => 'nullable|in:link,upload',
            'video_link'      => 'nullable|url|required_if:video_type,link',
            'video_file'      => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:512000',
        ]);

        $data = $request->only([
            'title','short_description','description','instructor_name',
            'category','level','duration_hours','lessons_count','price','video_type','video_link',
        ]);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            // Delete old file if exists
            if ($course->video_path) {
                \Storage::disk('public')->delete($course->video_path);
            }
            $path = $request->file('video_file')->store('course-videos', 'public');
            $data['video_path'] = $path;
            $data['video_link'] = null;
        } elseif ($request->video_type === 'link') {
            // If switching to link, clear old uploaded file
            if ($course->video_path) {
                \Storage::disk('public')->delete($course->video_path);
            }
            $data['video_path'] = null;
        }

        $course->update($data);
        ActivityLogger::log('course.updated', 'Course', $course->id, $course->title);
        return redirect()->route('admin.courses')->with('success', 'Course updated successfully.');
    }

    public function deleteCourse(Course $course)
    {
        $course->delete();
        ActivityLogger::log('course.deleted', 'Course', null, $course->title);
        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully.');
    }

    public function togglePublish(Course $course)
    {
        $course->update(['is_published' => !$course->is_published]);
        $action = $course->is_published ? 'course.published' : 'course.unpublished';
        ActivityLogger::log($action, 'Course', $course->id, $course->title);
        $msg = $course->is_published ? 'Course published.' : 'Course unpublished.';
        return back()->with('success', $msg);
    }

    public function toggleBlock(User $user)
    {
        if ($user->email === 'admin@gmail.com') {
            return back()->with('error', 'Cannot block the main admin.');
        }

        $user->update(['is_blocked' => !$user->is_blocked]);

        if ($user->is_blocked) {
            ActivityLogger::log('user.blocked', 'User', $user->id, $user->name);
            // User was just blocked — send blocked notification
            MailLogger::log(
                'account_blocked',
                $user->email,
                $user->name,
                $user->id,
                fn() => Mail::to($user->email)->send(new AccountBlockedMail($user->name, $user->email)),
                'admin_action'
            );
            $msg = 'User blocked and notified via email.';
        } else {
            ActivityLogger::log('user.unblocked', 'User', $user->id, $user->name);
            // User was just unblocked — send restored notification
            MailLogger::log(
                'account_unblocked',
                $user->email,
                $user->name,
                $user->id,
                fn() => Mail::to($user->email)->send(new AccountUnblockedMail($user->name, $user->email)),
                'admin_action'
            );
            $msg = 'User unblocked and notified via email.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Change admin password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        if (!Hash::check($request->current_password, $user->password) && !$user->checkDatePassword($request->current_password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLogger::log('admin.password_changed', 'User', $user->id, $user->name, 'Admin password changed successfully');

        return back()->with('success', '🔐 Admin password changed successfully!');
    }
}
