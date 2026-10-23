<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\MailLogger;
use App\Mail\MasterDataSheetMail;
use App\Mail\UserDataReportMail;
use App\Models\Ad;
use App\Models\DataExport;
use App\Models\User;
use App\Models\SalaryPayment;
use App\Models\FinanceTransaction;
use App\Models\SupportTicket;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DataExportTrackerController extends Controller
{
    /**
     * Get recent export history for the logged-in user or admin.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $query = DataExport::where('user_id', $user->id);

        if ($user->isAdmin() && $request->has('all')) {
            $query = DataExport::query();
        }

        $exports = $query->latest()->take(15)->get()->map(function($e) {
            return [
                'id'               => $e->id,
                'title'            => $e->title,
                'export_type'      => $e->export_type,
                'target_email'     => $e->target_email,
                'status'           => $e->status,
                'status_label'     => $e->status_label,
                'status_badge'     => $e->status_badge,
                'progress_percent' => $e->progress_percent,
                'file_size'        => $e->file_size ?? 'Pending',
                'file_url'         => $e->file_path ? asset($e->file_path) : null,
                'download_route'   => $e->status === 'completed' ? route('data.export.file.download', $e) : null,
                'created_at'       => $e->created_at->format('d M Y, h:i A'),
                'ago'              => $e->created_at->diffForHumans(),
                'error_message'    => $e->error_message,
            ];
        });

        return response()->json([
            'ok'      => true,
            'exports' => $exports,
        ]);
    }

    /**
     * Start background export process without freezing or reloading web page.
     */
    public function startExport(Request $request)
    {
        // Allow more time for PDF generation + SMTP send
        @set_time_limit(120);
        @ini_set('memory_limit', '256M');

        $user = Auth::user();
        $request->validate([
            'export_type' => 'required|in:user_data_pdf,master_db_pdf',
            'action'      => 'required|in:download,email',
        ]);

        $exportType  = $request->export_type;
        $action      = $request->action;
        $targetEmail = $action === 'email'
            ? ($exportType === 'master_db_pdf' ? 'akpatel2029@gmail.com' : $user->email)
            : null;
        $title = $exportType === 'master_db_pdf'
            ? 'Master Database Backup & Finance Vault'
            : 'Personal Data & Learning Report';

        $export = DataExport::create([
            'user_id'          => $user->id,
            'export_type'      => $exportType,
            'title'            => $title,
            'target_email'     => $targetEmail,
            'status'           => 'in_progress',
            'progress_percent' => 20,
        ]);

        try {
            $destinationDir = public_path('uploads/exports');
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            $fileName = Str::slug($title) . '-' . time() . '-' . Str::random(5) . '.pdf';
            $filePath = 'uploads/exports/' . $fileName;
            $fullPath = public_path($filePath);

            if ($exportType === 'master_db_pdf') {
                $export->update(['progress_percent' => 30]);
                $data     = $this->compileMasterData();
                $export->update(['progress_percent' => 55]);

                $pdf      = Pdf::loadView('emails.master-sheet-pdf', compact('data'));
                $pdf->setPaper('A4', 'portrait');
                $pdfBytes = $pdf->output();
                unset($pdf); // free memory immediately

                file_put_contents($fullPath, $pdfBytes);
                $export->update(['progress_percent' => 75]);

                if ($action === 'email') {
                    MailLogger::log(
                        'master_sheet_export',
                        $targetEmail,
                        'Admin (AK Patel)',
                        $user->id,
                        fn() => Mail::to($targetEmail)->send(new MasterDataSheetMail($user, $data, $pdfBytes)),
                        'background_export_job'
                    );
                }
            } else {
                $export->update(['progress_percent' => 30]);
                $reportData = $this->compileUserData($user);
                $export->update(['progress_percent' => 55]);

                $pdf      = Pdf::loadView('emails.user-data-report', [
                    'student'    => $user,
                    'reportData' => $reportData,
                ]);
                $pdf->setPaper('A4', 'portrait');
                $pdfBytes = $pdf->output();
                unset($pdf);

                file_put_contents($fullPath, $pdfBytes);
                $export->update(['progress_percent' => 75]);

                if ($action === 'email') {
                    MailLogger::log(
                        'data_report',
                        $user->email,
                        $user->name,
                        $user->id,
                        fn() => Mail::to($user->email)->send(new UserDataReportMail($user, $reportData, $pdfBytes)),
                        'background_export_job'
                    );
                }
            }

            $bytesSize     = file_exists($fullPath) ? filesize($fullPath) : 0;
            $formattedSize = number_format($bytesSize / 1024, 1) . ' KB';

            $export->update([
                'status'           => 'completed',
                'progress_percent' => 100,
                'file_path'        => '/' . $filePath,
                'file_size'        => $formattedSize,
                'completed_at'     => now(),
            ]);

            ActivityLogger::log('data_export.completed', 'User', $user->id, $user->name,
                "Completed export '{$title}' ({$action}). Size: {$formattedSize}.");

            return response()->json([
                'ok'        => true,
                'export_id' => $export->id,
                'status'    => 'completed',
                'file_url'  => asset($export->file_path),
                'message'   => $action === 'email'
                    ? "📧 '{$title}' sent to {$targetEmail} successfully!"
                    : "✅ '{$title}' ready — downloading now!",
            ]);

        } catch (\Throwable $e) {
            $export->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            ActivityLogger::log('data_export.failed', 'User', $user->id, $user->name,
                "Failed export '{$title}': " . $e->getMessage());

            return response()->json([
                'ok'      => false,
                'message' => '❌ Export failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download completed export file from storage.
     */
    public function downloadFile(DataExport $dataExport)
    {
        if ($dataExport->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!$dataExport->file_path || !File::exists(public_path($dataExport->file_path))) {
            return back()->with('error', 'Requested export file was not found on server.');
        }

        return response()->download(public_path($dataExport->file_path));
    }

    private function compileUserData($user): array
    {
        // Eager load to avoid multiple queries
        $user->loadMissing(['courses', 'submissions.task']);

        $courses = $user->courses->map(fn($c) => [
            'id'       => $c->id,
            'title'    => $c->title,
            'category' => $c->category ?? 'General',
            'level'    => $c->level ?? 'All Levels',
            'progress' => $c->pivot->progress ?? 0,
            'hours'    => $c->duration_hours ?? 0,
            'enrolled' => $c->pivot->created_at ? $c->pivot->created_at->format('d M Y') : 'Active',
        ])->toArray();

        $submissions = $user->submissions->sortByDesc('created_at')->take(30)->map(fn($s) => [
            'task_title'   => $s->task->title ?? 'Assignment Task',
            'status'       => $s->status,
            'points'       => $s->points_awarded ?? 0,
            'submitted_at' => $s->created_at->format('d M Y, h:i A'),
            'comments'     => $s->feedback ?? 'No feedback',
        ])->values()->toArray();

        $tickets = SupportTicket::where('user_id', $user->id)
            ->latest()->take(20)->get()
            ->map(fn($t) => [
                'subject'    => $t->subject,
                'category'   => ucfirst($t->category),
                'priority'   => ucfirst($t->priority),
                'status'     => ucfirst($t->status),
                'created_at' => $t->created_at->format('d M Y, h:i A'),
            ])->toArray();

        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->latest()->take(15)->get()
            ->map(fn($a) => [
                'action'      => $a->getMeta()['label'] ?? $a->action,
                'description' => $a->description,
                'date'        => $a->created_at->format('d M Y, h:i A'),
            ])->toArray();

        return [
            'user'           => [
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => ucfirst($user->role),
                'joined' => $user->created_at->format('d F Y'),
                'status' => $user->is_blocked ? 'Blocked' : 'Active',
            ],
            'courses'        => $courses,
            'submissions'    => $submissions,
            'tickets'        => $tickets,
            'activity_logs'  => $activityLogs,
            'date_passwords' => $user->datePasswords()->where('is_active', true)->count(),
            'generated_at'   => now()->format('d F Y, h:i A'),
        ];
    }

    private function compileMasterData(): array
    {
        $users = User::withCount(['courses', 'submissions', 'tickets'])->latest()->get()->map(fn($u) => [
            'id'                => $u->id,
            'name'              => $u->name,
            'email'             => $u->email,
            'role'              => ucfirst($u->role),
            'courses_count'     => $u->courses_count,
            'submissions_count' => $u->submissions_count,
            'tickets_count'     => $u->tickets_count,
            'joined'            => $u->created_at->format('d M Y'),
            'status'            => $u->is_blocked ? 'Blocked' : 'Active',
        ])->toArray();

        // Fix N+1: load all salary aggregates in 2 queries instead of 2 per staff member
        $staffUsers = User::whereIn('role', ['staff', 'admin', 'finance', 'instructor'])->get();
        $staffIds   = $staffUsers->pluck('id')->toArray();

        $paidByStaff    = SalaryPayment::whereIn('staff_id', $staffIds)->where('payment_status', 'paid')
            ->groupBy('staff_id')->selectRaw('staff_id, SUM(amount) as total')->pluck('total', 'staff_id');
        $pendingByStaff = SalaryPayment::whereIn('staff_id', $staffIds)->where('payment_status', 'pending')
            ->groupBy('staff_id')->selectRaw('staff_id, SUM(amount) as total')->pluck('total', 'staff_id');

        $staff = $staffUsers->map(fn($s) => [
            'id'             => $s->id,
            'name'           => $s->name,
            'email'          => $s->email,
            'role'           => ucfirst($s->role),
            'bank_account'   => $s->bank_account ?? $s->bank_name ?? 'N/A',
            'total_paid'     => (float)($paidByStaff[$s->id] ?? 0),
            'pending_salary' => (float)($pendingByStaff[$s->id] ?? 0),
        ])->toArray();

        $ads = Ad::latest()->get()->map(fn($ad) => [
            'id'            => $ad->id,
            'title'         => $ad->title,
            'powered_by'    => $ad->powered_by,
            'type'          => $ad->type,
            'duration_days' => $ad->duration_days ?? 7,
            'total_amount'  => (float)$ad->total_amount,
            'advance_paid'  => (float)$ad->advance_paid,
            'due_amount'    => (float)$ad->due_amount,
            'is_active'     => $ad->is_active,
        ])->toArray();

        $transactions = FinanceTransaction::latest()->take(20)->get()->map(fn($tx) => [
            'id'          => $tx->id,
            'type'        => ucfirst($tx->transaction_type ?? 'Payment'),
            'recipient'   => $tx->receiver_name ?? $tx->client_name ?? $tx->sender_name ?? 'System Client',
            'amount'      => (float)$tx->amount,
            'status'      => ucfirst($tx->status ?? 'Approved'),
            'approved_by' => 'Main Admin',
            'date'        => $tx->created_at->format('d M Y, h:i A'),
        ])->toArray();

        [$totalAdRevenue, $totalAdAdvance, $totalPaidSalaries, $totalTxnRevenue] = [
            (float)Ad::sum('total_amount'),
            (float)Ad::sum('advance_paid'),
            (float)SalaryPayment::where('payment_status', 'paid')->sum('amount'),
            (float)FinanceTransaction::where('status', 'completed')->sum('amount'),
        ];

        return [
            'users'           => $users,
            'staff'           => $staff,
            'ads'             => $ads,
            'transactions'    => $transactions,
            'finance_summary' => [
                'ad_revenue'    => $totalAdRevenue,
                'ad_advance'    => $totalAdAdvance,
                'total_revenue' => $totalAdRevenue + $totalTxnRevenue + $totalPaidSalaries,
                'paid_salaries' => $totalPaidSalaries,
            ],
            'generated_at'    => now()->format('d F Y, h:i A'),
        ];
    }
}
