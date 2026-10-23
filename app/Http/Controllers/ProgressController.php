<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * Display student progress analytics and learning journey.
     */
    public function index()
    {
        $user = Auth::user();

        // Enrolled courses with pivot progress — single query
        $courses = $user->courses()->get();

        $totalEnrolled    = $courses->count();
        $completedCourses = $courses->filter(fn($c) => ($c->pivot->progress ?? 0) >= 100)->count();
        $inProgressCourses= $courses->filter(fn($c) => ($c->pivot->progress ?? 0) > 0 && ($c->pivot->progress ?? 0) < 100)->count();
        $totalHours       = $courses->sum('duration_hours');
        $earnedHours      = $courses->sum(fn($c) => round(($c->duration_hours * ($c->pivot->progress ?? 0)) / 100));

        // DB-level aggregates — no memory loading of all submissions
        $userId = $user->id;
        $totalSubmissions  = TaskSubmission::where('user_id', $userId)->count();
        $gradedSubmissions = TaskSubmission::where('user_id', $userId)->where('status', 'graded')->count();
        $pendingSubmissions= TaskSubmission::where('user_id', $userId)->where('status', 'pending')->count();
        $totalPointsEarned = (int) TaskSubmission::where('user_id', $userId)->where('status', 'graded')->sum('points_awarded');

        // Milestones & Badges
        $maxProgress = $courses->max(fn($c) => $c->pivot->progress ?? 0) ?? 0;

        $badges = [
            ['title'=>'First Step',        'desc'=>'Enrolled in your first course',          'unlocked'=>$totalEnrolled > 0,         'icon'=>'fa-graduation-cap'],
            ['title'=>'Task Master',        'desc'=>'Completed and passed 3+ assignments',    'unlocked'=>$gradedSubmissions >= 3,     'icon'=>'fa-file-check'],
            ['title'=>'Fast Learner',       'desc'=>'Reached 50%+ progress in any course',   'unlocked'=>$maxProgress >= 50,          'icon'=>'fa-bolt'],
            ['title'=>'Certified Graduate', 'desc'=>'Earned an official course certificate',  'unlocked'=>$completedCourses > 0,       'icon'=>'fa-award'],
        ];

        return view('progress.index', compact(
            'user', 'courses', 'totalEnrolled', 'completedCourses',
            'inProgressCourses', 'totalHours', 'earnedHours',
            'totalSubmissions', 'gradedSubmissions', 'pendingSubmissions',
            'totalPointsEarned', 'badges'
        ));
    }
}
