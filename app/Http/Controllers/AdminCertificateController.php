<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\MailLogger;
use App\Mail\CourseCertificateMail;
use App\Models\Course;
use App\Models\MailLog;
use App\Models\TaskSubmission;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminCertificateController extends Controller
{
    /**
     * Display a listing of all issued certificates / course completions.
     */
    public function index(Request $request)
    {
        // Query completed enrollments where progress >= 100
        $query = DB::table('course_user')
            ->join('users',   'course_user.user_id',   '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('course_user.progress', '>=', 100)
            ->select(
                'course_user.user_id',
                'course_user.course_id',
                'course_user.progress',
                'course_user.updated_at as completion_date',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role',
                'courses.title as course_title',
                'courses.category as course_category',
                'courses.instructor_name as instructor_name'
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

        $certificates = $query->orderByDesc('course_user.updated_at')
                              ->paginate(15)
                              ->withQueryString();

        // Attach mail delivery logs status for each certificate row
        foreach ($certificates as $cert) {
            $mailLog = MailLog::where('recipient_email', $cert->user_email)
                ->where('mail_type', 'course_certificate')
                ->latest()
                ->first();

            $cert->mail_status = $mailLog ? ($mailLog->success ? 'sent' : 'failed') : 'not_sent';
            $cert->mail_sent_at = $mailLog ? $mailLog->created_at->format('d M Y, h:i A') : null;
            $cert->mail_error = $mailLog ? $mailLog->error_message : null;
        }

        // Summary stats
        $totalIssued      = DB::table('course_user')->where('progress', '>=', 100)->count();
        $uniqueStudents   = DB::table('course_user')->where('progress', '>=', 100)->distinct('user_id')->count('user_id');
        $emailsDelivered  = MailLog::where('mail_type', 'course_certificate')->where('success', true)->count();
        $emailsFailed     = MailLog::where('mail_type', 'course_certificate')->where('success', false)->count();

        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.certificates', compact(
            'certificates', 'courses', 'totalIssued', 'uniqueStudents', 'emailsDelivered', 'emailsFailed'
        ));
    }

    /**
     * Preview student certificate PDF as admin.
     */
    public function view(User $user, Course $course)
    {
        $completedDate = $this->getCompletionDate($user, $course);
        $student = $user;

        $pdf = Pdf::loadView('certificates.certificate', compact('student', 'course', 'completedDate'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('certificate-' . str($user->name)->slug() . '-' . str($course->title)->slug() . '.pdf');
    }

    /**
     * Download student certificate PDF as admin.
     */
    public function download(User $user, Course $course)
    {
        $completedDate = $this->getCompletionDate($user, $course);
        $student = $user;

        $pdf = Pdf::loadView('certificates.certificate', compact('student', 'course', 'completedDate'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('certificate-' . str($user->name)->slug() . '-' . str($course->title)->slug() . '.pdf');
    }

    /**
     * Send or Resend certificate email to student.
     */
    public function resendEmail(User $user, Course $course)
    {
        $completedDate = $this->getCompletionDate($user, $course);
        $certificateUrl = route('certificates.download', $course);

        $log = MailLogger::log(
            'course_certificate',
            $user->email,
            $user->name,
            $user->id,
            fn() => Mail::to($user->email)->send(
                new CourseCertificateMail($user, $course, $completedDate, $certificateUrl)
            ),
            'admin:resend'
        );

        if ($log->success) {
            ActivityLogger::log('certificate.email_sent', 'Course', $course->id, $course->title, "Sent certificate email to {$user->name} ({$user->email})");
            return back()->with('success', "Certificate email sent successfully to {$user->name} ({$user->email}).");
        } else {
            return back()->with('error', "Failed to send certificate email: " . ($log->error_message ?? 'Unknown error'));
        }
    }

    /**
     * Helper to get completed date string.
     */
    private function getCompletionDate(User $user, Course $course): string
    {
        $taskIds = $course->tasks()->pluck('id');
        if ($taskIds->isNotEmpty()) {
            $lastGraded = TaskSubmission::where('user_id', $user->id)
                ->whereIn('task_id', $taskIds)
                ->where('status', 'graded')
                ->latest('updated_at')
                ->first();

            if ($lastGraded) {
                return Carbon::parse($lastGraded->updated_at)->format('d F Y');
            }
        }

        $pivot = DB::table('course_user')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return $pivot && $pivot->updated_at
            ? Carbon::parse($pivot->updated_at)->format('d F Y')
            : now()->format('d F Y');
    }
}
