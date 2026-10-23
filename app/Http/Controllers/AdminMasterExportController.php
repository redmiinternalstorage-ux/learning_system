<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\MailLogger;
use App\Mail\MasterDataSheetMail;
use App\Models\Ad;
use App\Models\User;
use App\Models\SalaryPayment;
use App\Models\FinanceTransaction;
use App\Models\SupportTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminMasterExportController extends Controller
{
    /**
     * Compile complete user side & admin/finance side master data.
     */
    private function compileMasterData(): array
    {
        // 1. User Side Data
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

        // 2. Staff & Finance Payroll Data
        $staff = User::whereIn('role', ['staff', 'admin', 'finance', 'instructor'])->get()->map(function($s) {
            $totalPaid = SalaryPayment::where('staff_id', $s->id)->where('payment_status', 'paid')->sum('amount');
            $pendingSalary = SalaryPayment::where('staff_id', $s->id)->where('payment_status', 'pending')->sum('amount');
            return [
                'id'             => $s->id,
                'name'           => $s->name,
                'email'          => $s->email,
                'role'           => ucfirst($s->role),
                'bank_account'   => $s->bank_account ?? $s->bank_name ?? 'N/A',
                'total_paid'     => (float)$totalPaid,
                'pending_salary' => (float)$pendingSalary,
            ];
        })->toArray();

        // 3. Ad Campaigns & Collection Data
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

        // 4. Financial Transactions
        $transactions = FinanceTransaction::latest()->take(20)->get()->map(fn($tx) => [
            'id'          => $tx->id,
            'type'        => ucfirst($tx->transaction_type ?? 'Payment'),
            'recipient'   => $tx->receiver_name ?? $tx->client_name ?? $tx->sender_name ?? 'System Client',
            'amount'      => (float)$tx->amount,
            'status'      => ucfirst($tx->status ?? 'Approved'),
            'approved_by' => 'Main Admin',
            'date'        => $tx->created_at->format('d M Y, h:i A'),
        ])->toArray();

        // Financial Totals
        $totalAdRevenue = Ad::sum('total_amount');
        $totalAdAdvance = Ad::sum('advance_paid');
        $totalPaidSalaries = SalaryPayment::where('payment_status', 'paid')->sum('amount');
        $totalTxnRevenue = FinanceTransaction::where('status', 'completed')->sum('amount');
        $totalRevenue = $totalAdRevenue + $totalTxnRevenue + $totalPaidSalaries;

        return [
            'users'            => $users,
            'staff'            => $staff,
            'ads'              => $ads,
            'transactions'     => $transactions,
            'finance_summary'  => [
                'ad_revenue'     => (float)$totalAdRevenue,
                'ad_advance'     => (float)$totalAdAdvance,
                'total_revenue'  => (float)$totalRevenue,
                'paid_salaries'  => (float)$totalPaidSalaries,
            ],
            'generated_at'     => now()->format('d F Y, h:i A'),
        ];
    }

    /**
     * Download master sheet PDF directly.
     */
    public function downloadMasterData(?Request $request = null)
    {
        $admin = Auth::user();
        $data = $this->compileMasterData();

        if ($admin) {
            ActivityLogger::log('admin.master_export_downloaded', 'User', $admin->id, $admin->name, 
                "Admin {$admin->name} downloaded the complete master finance & system data sheet.");
        }

        $pdf = Pdf::loadView('emails.master-sheet-pdf', compact('data'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('master-finance-and-system-sheet-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Email complete colorful master sheet directly to akpatel2029@gmail.com.
     * Supports background AJAX / JSON dispatch without full page reload.
     */
    public function emailMasterData(?Request $request = null)
    {
        $admin = Auth::user();
        $targetEmail = 'akpatel2029@gmail.com';
        $data = $this->compileMasterData();

        // Generate PDF bytes
        $pdf = Pdf::loadView('emails.master-sheet-pdf', compact('data'));
        $pdf->setPaper('A4', 'portrait');
        $pdfBytes = $pdf->output();

        // Send Email & log in system mail logs
        $mailLog = MailLogger::log(
            'master_sheet_export',
            $targetEmail,
            'Admin (AK Patel)',
            $admin ? $admin->id : null,
            fn() => Mail::to($targetEmail)->send(new MasterDataSheetMail($admin, $data, $pdfBytes)),
            'admin_master_export'
        );

        if (!$mailLog->success) {
            if ($admin) {
                ActivityLogger::log('admin.master_export_failed', 'User', $admin->id, $admin->name, 
                    "Master sheet email dispatch to {$targetEmail} failed: {$mailLog->error_message}");
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok'      => false,
                    'message' => "❌ Email sending failed: " . ($mailLog->error_message ?? 'SMTP Connection Error')
                ], 422);
            }

            return back()->with('error', "❌ Email sending failed: " . ($mailLog->error_message ?? 'SMTP Error'));
        }

        if ($admin) {
            ActivityLogger::log('admin.master_export_emailed', 'User', $admin->id, $admin->name, 
                "Admin {$admin->name} successfully dispatched complete master finance & system data sheet to {$targetEmail}.");
        }

        $msg = "📧 Success! Colorful Master Finance & System Data Sheet has been sent directly to {$targetEmail}!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok'      => true,
                'message' => $msg
            ]);
        }

        return back()->with('success', $msg);
    }
}
