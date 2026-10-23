<?php

namespace App\Http\Controllers;

use App\Mail\CourseCertificateMail;
use App\Models\Course;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Helpers\MailLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminTaskController extends Controller
{
    public function index(Course $course)
    {
        $tasks = $course->tasks()->with('assignedTo')->latest()->get();
        return view('admin.tasks.index', compact('course', 'tasks'));
    }

    public function create(Course $course)
    {
        $staffList = \App\Models\User::whereIn('role', ['admin', 'staff'])->where('is_blocked', false)->get();
        return view('admin.tasks.create', compact('course', 'staffList'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'points'      => 'required|integer|min:1',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $course->tasks()->create($request->only('title', 'description', 'due_date', 'points', 'assigned_to'));

        return redirect()->route('admin.courses.tasks.index', $course)
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $staffList = \App\Models\User::whereIn('role', ['admin', 'staff'])->where('is_blocked', false)->get();
        return view('admin.tasks.edit', compact('task', 'staffList'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'points'      => 'required|integer|min:1',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($request->only('title', 'description', 'due_date', 'points', 'assigned_to'));

        return redirect()->route('admin.courses.tasks.index', $task->course_id)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $courseId = $task->course_id;
        $task->delete();
        return redirect()->route('admin.courses.tasks.index', $courseId)
            ->with('success', 'Task deleted successfully.');
    }

    public function submissions(Task $task)
    {
        $submissions = $task->submissions()->with('user')->latest()->get();
        return view('admin.tasks.submissions', compact('task', 'submissions'));
    }

    public function grade(Request $request, TaskSubmission $submission)
    {
        $request->validate([
            'grade'    => 'required|integer|min:0|max:' . $submission->task->points,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade'          => $request->grade,
            'points_awarded' => $request->grade, // sync points_awarded with grade
            'feedback'       => $request->feedback,
            'status'         => 'graded',
        ]);

        // ── Real-time Progress & Certificate Check ────────────────────────
        $student = $submission->user;
        $course  = $submission->task->course;
        $progress = $student->updateCourseProgress($course);

        // If progress is 100%, trigger automatic certificate delivery
        if ($progress >= 100) {
            $this->checkAndSendCertificate($submission);
        }
        // ────────────────────────────────────────────────────────────────────

        return redirect()->route('admin.tasks.submissions', $submission->task_id)
            ->with('success', 'Submission graded successfully.');
    }

    /**
     * Check whether the student has all course tasks graded.
     * If yes, send them a congratulatory certificate email.
     */
    private function checkAndSendCertificate(TaskSubmission $submission): void
    {
        $student = $submission->user;
        $course  = $submission->task->course;

        $taskIds = $course->tasks()->pluck('id');

        // Need at least 1 task
        if ($taskIds->isEmpty()) {
            return;
        }

        $gradedCount = TaskSubmission::where('user_id', $student->id)
            ->whereIn('task_id', $taskIds)
            ->where('status', 'graded')
            ->count();

        // Not all tasks graded yet
        if ($gradedCount < $taskIds->count()) {
            return;
        }

        // All done — build the certificate download URL
        $certificateUrl = route('certificates.download', $course);

        $lastGraded = TaskSubmission::where('user_id', $student->id)
            ->whereIn('task_id', $taskIds)
            ->where('status', 'graded')
            ->latest('updated_at')
            ->first();

        $completedDate = $lastGraded
            ? Carbon::parse($lastGraded->updated_at)->format('d F Y')
            : now()->format('d F Y');

        MailLogger::log(
            'course_certificate',
            $student->email,
            $student->name,
            $student->id,
            fn() => Mail::to($student->email)->send(
                new CourseCertificateMail($student, $course, $completedDate, $certificateUrl)
            ),
            'system:grade',
        );
    }
}
