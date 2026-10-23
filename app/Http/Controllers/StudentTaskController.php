<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentTaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Single query: get enrolled courses with tasks eager-loaded
        $enrolledCourses = $user->courses()->with('tasks')->get();
        $enrolledCourseIds = $enrolledCourses->pluck('id')->toArray();

        $tasks = Task::whereIn('course_id', $enrolledCourseIds)
            ->with('course')
            ->latest()
            ->get();

        // Single query: all submissions keyed by task_id
        $submissions = $user->submissions()
            ->get()
            ->keyBy('task_id');

        // Build completed course list in memory (no extra queries)
        $completedCourseIds = [];
        foreach ($enrolledCourses as $enrolledCourse) {
            $courseTaskIds = $enrolledCourse->tasks->pluck('id');
            if ($courseTaskIds->isNotEmpty()) {
                $gradedCount = $submissions
                    ->whereIn('task_id', $courseTaskIds->toArray())
                    ->where('status', 'graded')
                    ->count();
                if ($gradedCount >= $courseTaskIds->count()) {
                    $completedCourseIds[] = $enrolledCourse->id;
                }
            }
        }

        // Pre-load completed courses from already-fetched collection (no extra query)
        $completedCourses = $enrolledCourses->whereIn('id', $completedCourseIds)->values();

        return view('tasks.index', compact('tasks', 'submissions', 'completedCourseIds', 'completedCourses'));
    }

    public function show(Task $task)
    {
        $user = Auth::user();
        
        // Ensure student is enrolled in the task's course
        if (!$user->courses()->where('course_id', $task->course_id)->exists()) {
            return redirect()->route('dashboard')->with('error', 'You must be enrolled in this course to view its tasks.');
        }

        $submission = TaskSubmission::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->first();

        // Check if student has completed ALL tasks in this course (for certificate banner)
        $course  = $task->course;
        $taskIds = $course->tasks()->pluck('id');
        $courseCompleted = false;
        if ($taskIds->isNotEmpty()) {
            $gradedCount = TaskSubmission::where('user_id', $user->id)
                ->whereIn('task_id', $taskIds)
                ->where('status', 'graded')
                ->count();
            $courseCompleted = $gradedCount >= $taskIds->count();
        }

        return view('tasks.show', compact('task', 'submission', 'courseCompleted'));
    }

    public function submit(Request $request, Task $task)
    {
        $user = Auth::user();
        
        // Ensure student is enrolled in the task's course
        if (!$user->courses()->where('course_id', $task->course_id)->exists()) {
            return redirect()->route('dashboard')->with('error', 'You must be enrolled in this course to submit tasks.');
        }

        $request->validate([
            'submission_text' => 'required_without:file|nullable|string',
            'file'            => 'nullable|file|max:10240', // Max 10MB
        ]);

        $submission = TaskSubmission::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->first();

        $filePath = $submission ? $submission->file_path : null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions', 'public');
        }

        TaskSubmission::updateOrCreate(
            [
                'task_id' => $task->id,
                'user_id' => $user->id,
            ],
            [
                'submission_text' => $request->submission_text,
                'file_path'       => $filePath,
                'status'          => 'pending',
                'grade'           => null,
                'feedback'        => null,
                'submitted_at'    => now(),
            ]
        );

        // Update real-time user progress
        $user->updateCourseProgress($task->course);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Your assignment has been submitted successfully! 🎉');
    }
}
