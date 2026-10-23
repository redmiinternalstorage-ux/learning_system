<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\TaskSubmission;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Demo Certificates List / Data
     */
    private function getDemoCertificates(): array
    {
        return [
            'web-dev' => [
                'key' => 'web-dev',
                'title' => 'Web Development Masterclass',
                'category' => 'Web Development',
                'level' => 'Beginner to Advanced',
                'hours' => 45,
                'instructor' => 'Alex Morgan',
                'icon' => 'fa-code',
                'gradient' => 'from-emerald-600 to-teal-700',
            ],
            'python-ai' => [
                'key' => 'python-ai',
                'title' => 'Python Data Science & AI',
                'category' => 'Artificial Intelligence',
                'level' => 'Intermediate',
                'hours' => 60,
                'instructor' => 'Dr. Sarah Connor',
                'icon' => 'fa-brain',
                'gradient' => 'from-blue-600 to-indigo-700',
            ],
            'cyber-sec' => [
                'key' => 'cyber-sec',
                'title' => 'Cyber Security & Ethical Hacking',
                'category' => 'Security',
                'level' => 'Advanced',
                'hours' => 50,
                'instructor' => 'Marcus Vance',
                'icon' => 'fa-shield-halved',
                'gradient' => 'from-purple-600 to-indigo-800',
            ],
            'ui-ux' => [
                'key' => 'ui-ux',
                'title' => 'UI/UX Design & Product Architecture',
                'category' => 'Design & UX',
                'level' => 'All Levels',
                'hours' => 35,
                'instructor' => 'Elena Rostova',
                'icon' => 'fa-pen-ruler',
                'gradient' => 'from-rose-600 to-pink-700',
            ],
            'cloud-devops' => [
                'key' => 'cloud-devops',
                'title' => 'Cloud Computing & DevOps Engineer',
                'category' => 'Cloud Infrastructure',
                'level' => 'Advanced',
                'hours' => 55,
                'instructor' => 'David Miller',
                'icon' => 'fa-cloud-arrow-up',
                'gradient' => 'from-teal-600 to-emerald-700',
            ],
        ];
    }

    /**
     * Check if the authenticated student has completed all tasks in a course.
     */
    private function checkCompletion(Course $course): bool
    {
        $user = Auth::user();

        if (!$user->courses()->where('course_id', $course->id)->exists()) {
            return false;
        }

        $taskIds = $course->tasks()->pluck('id');

        if ($taskIds->isEmpty()) {
            return false;
        }

        $gradedCount = TaskSubmission::where('user_id', $user->id)
            ->whereIn('task_id', $taskIds)
            ->where('status', 'graded')
            ->count();

        return $gradedCount >= $taskIds->count();
    }

    /**
     * View certificate in browser.
     */
    public function view(Course $course)
    {
        $user = Auth::user();

        if (!$this->checkCompletion($course)) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Complete and pass all assignments to earn your certificate.');
        }

        $taskIds = $course->tasks()->pluck('id');
        $lastGraded = TaskSubmission::where('user_id', $user->id)
            ->whereIn('task_id', $taskIds)
            ->where('status', 'graded')
            ->latest('updated_at')
            ->first();

        $completedDate = $lastGraded
            ? Carbon::parse($lastGraded->updated_at)->format('d F Y')
            : now()->format('d F Y');

        $student = $user;

        $pdf = Pdf::loadView('certificates.certificate', compact('student', 'course', 'completedDate'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('certificate-' . str($course->title)->slug() . '.pdf');
    }

    /**
     * Force-download certificate as PDF.
     */
    public function download(Course $course)
    {
        $user = Auth::user();

        if (!$this->checkCompletion($course)) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Complete and pass all assignments to earn your certificate.');
        }

        $taskIds = $course->tasks()->pluck('id');
        $lastGraded = TaskSubmission::where('user_id', $user->id)
            ->whereIn('task_id', $taskIds)
            ->where('status', 'graded')
            ->latest('updated_at')
            ->first();

        $completedDate = $lastGraded
            ? Carbon::parse($lastGraded->updated_at)->format('d F Y')
            : now()->format('d F Y');

        $student = $user;

        $pdf = Pdf::loadView('certificates.certificate', compact('student', 'course', 'completedDate'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('certificate-' . str($course->title)->slug() . '.pdf');
    }

    /**
     * Demo Certificates Index Page
     */
    public function demoIndex()
    {
        $demoCertificates = $this->getDemoCertificates();
        return view('certificates.demo_index', compact('demoCertificates'));
    }

    /**
     * Inline preview for Demo Certificate PDF
     */
    public function demoView(string $type)
    {
        $all = $this->getDemoCertificates();
        abort_if(!isset($all[$type]), 404);

        $cert = $all[$type];
        $pdf = Pdf::loadView('certificates.demo_certificate', compact('cert'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('demo-certificate-' . $type . '.pdf');
    }

    /**
     * Download Demo Certificate PDF
     */
    public function demoDownload(string $type)
    {
        $all = $this->getDemoCertificates();
        abort_if(!isset($all[$type]), 404);

        $cert = $all[$type];
        $pdf = Pdf::loadView('certificates.demo_certificate', compact('cert'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('demo-certificate-' . $type . '.pdf');
    }
}
