<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    // Public home page
    public function home()
    {
        $featuredCourses = Cache::remember('home_featured_courses', 300, fn() => Course::published()->latest()->take(6)->get());
        $totalCourses    = Cache::remember('home_total_courses_count', 300, fn() => Course::published()->count());
        $categories = ['Web Development', 'Data Science', 'Design', 'AI & ML', 'Mobile Development', 'Marketing'];
        return view('home', compact('featuredCourses', 'totalCourses', 'categories'));
    }

    // Public courses listing
    public function publicCourses(Request $request)
    {
        $query = Course::published();
        if ($request->filled('search')) {
            $s = $request->search;
            // Wrap in a where group to preserve the published() scope conditions
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%'.$s.'%')
                  ->orWhere('instructor_name', 'like', '%'.$s.'%');
            });
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        $courses    = $query->latest()->paginate(12)->withQueryString();
        $categories = Cache::remember('published_categories', 300, fn() => Course::published()->distinct()->pluck('category'));
        return view('courses.public', compact('courses', 'categories'));
    }

    // Single course detail
    public function show(Course $course)
    {
        $enrolled = Auth::check() && Auth::user()->courses()->where('course_id', $course->id)->exists();
        $tasks    = $course->tasks()->latest()->get();
        return view('courses.show', compact('course', 'enrolled', 'tasks'));
    }

    // My enrolled courses (auth)
    public function myCourses()
    {
        $user = Auth::user();
        
        $courses = $user->courses()->latest()->get();

        // Build completed course ID set for certificate buttons
        $completedCourseIds = [];
        foreach ($courses as $course) {
            if (($course->pivot->progress ?? 0) >= 100) {
                $completedCourseIds[] = $course->id;
            }
        }

        return view('courses.mine', compact('courses', 'completedCourseIds'));
    }

    public function enroll(Course $course)
    {
        $user = Auth::user();
        if (!$user->courses()->where('course_id', $course->id)->exists()) {
            $user->courses()->attach($course->id);
            return back()->with('success', 'Enrolled in "' . $course->title . '" successfully!');
        }
        return back()->with('info', 'You are already enrolled in this course.');
    }

    public function unenroll(Course $course)
    {
        Auth::user()->courses()->detach($course->id);
        return back()->with('success', 'Unenrolled from "' . $course->title . '".');
    }

    // Developers Showcase Page
    public function developers()
    {
        return view('developers');
    }
}
