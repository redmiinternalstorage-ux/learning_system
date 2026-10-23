<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\MailLogger;
use App\Mail\UserDataReportMail;
use App\Models\ActivityLog;
use App\Models\SupportTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserDataExportController extends Controller
{
    /**
     * Gather complete comprehensive report data for the authenticated user.
     */
    private function compileReportData($user): array
    {
        $courses = $user->courses()->get()->map(fn($c) => [
            'id'       => $c->id,
            'title'    => $c->title,
            'category' => $c->category ?? 'General',
            'level'    => $c->level ?? 'All Levels',
            'progress' => $c->pivot->progress ?? 0,
            'hours'    => $c->duration_hours ?? 0,
            'enrolled' => $c->pivot->created_at ? $c->pivot->created_at->format('d M Y') : 'Active',
        ])->toArray();

        $submissions = $user->submissions()->with('task')->latest()->get()->map(fn($s) => [
            'task_title'   => $s->task->title ?? 'Assignment Task',
            'status'       => $s->status,
            'points'       => $s->points_awarded ?? 0,
            'submitted_at' => $s->created_at->format('d M Y, h:i A'),
            'comments'     => $s->feedback ?? 'No feedback',
        ])->toArray();

        $tickets = SupportTicket::where('user_id', $user->id)->latest()->get()->map(fn($t) => [
            'subject'    => $t->subject,
            'category'   => ucfirst($t->category),
            'priority'   => ucfirst($t->priority),
            'status'     => ucfirst($t->status),
            'created_at' => $t->created_at->format('d M Y, h:i A'),
        ])->toArray();

        $activityLogs = ActivityLog::where('user_id', $user->id)->latest()->take(15)->get()->map(fn($a) => [
            'action'      => $a->getMeta()['label'] ?? $a->action,
            'description' => $a->description,
            'date'        => $a->created_at->format('d M Y, h:i A'),
        ])->toArray();

        $datePasswordsCount = $user->datePasswords()->where('is_active', true)->count();

        return [
            'user'            => [
                'name'    => $user->name,
                'email'   => $user->email,
                'role'    => ucfirst($user->role),
                'joined'  => $user->created_at->format('d F Y'),
                'status'  => $user->is_blocked ? 'Blocked' : 'Active',
            ],
            'courses'         => $courses,
            'submissions'     => $submissions,
            'tickets'         => $tickets,
            'activity_logs'   => $activityLogs,
            'date_passwords'  => $datePasswordsCount,
            'generated_at'    => now()->format('d F Y, h:i A'),
        ];
    }

    /**
     * Download personal data report sheet as PDF document file.
     */
    public function downloadData(Request $request)
    {
        $user = Auth::user();
        $reportData = $this->compileReportData($user);

        // Log action for Admin Activity Logs & real-time monitoring
        ActivityLogger::log('user.data_downloaded', 'User', $user->id, $user->name, 
            "Student {$user->name} downloaded their complete personal data report PDF sheet.");

        $pdf = Pdf::loadView('emails.user-data-report', [
            'student'    => $user,
            'reportData' => $reportData,
        ]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('my-data-sheet-' . str($user->name)->slug() . '.pdf');
    }

    /**
     * Send complete personal data report with PDF attachment directly to student's email.
     */
    public function emailData(Request $request)
    {
        $user = Auth::user();
        $reportData = $this->compileReportData($user);

        // Generate PDF bytes for attachment
        $pdf = Pdf::loadView('emails.user-data-report', [
            'student'    => $user,
            'reportData' => $reportData,
        ]);
        $pdf->setPaper('A4', 'portrait');
        $pdfBytes = $pdf->output();

        // Send Email & log in system mail logs
        $mailLog = MailLogger::log(
            'data_report',
            $user->email,
            $user->name,
            $user->id,
            fn() => Mail::to($user->email)->send(new UserDataReportMail($user, $reportData, $pdfBytes)),
            'user_data_export'
        );

        if (!$mailLog->success) {
            ActivityLogger::log('user.data_emailed_failed', 'User', $user->id, $user->name, 
                "Attempted data report email to {$user->email} failed: {$mailLog->error_message}");

            if ($request->wantsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => "❌ Email delivery issue: " . ($mailLog->error_message ?? 'Mail connection failed')
                ], 422);
            }

            return back()->with('error', "❌ Unable to send email: " . ($mailLog->error_message ?? 'Mail connection failed'));
        }

        // Log activity for Admin Activity Logs
        ActivityLogger::log('user.data_emailed', 'User', $user->id, $user->name, 
            "Student {$user->name} requested their complete personal data report PDF sheet via email to {$user->email}.");

        if ($request->wantsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => "✅ Complete data report sheet (PDF) has been attached and emailed to {$user->email}!"
            ]);
        }

        return back()->with('success', "📧 Success! Complete data report sheet (PDF attached) has been sent to {$user->email}.");
    }
}
