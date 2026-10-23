<?php

namespace App\Http\Controllers;

use App\Helpers\MailLogger;
use App\Mail\AdminDirectMail;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminMailController extends Controller
{
    // ── Direct mail compose form ──────────────────────────────────────────
    public function compose()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'role']);
        return view('admin.mail-compose', compact('users'));
    }

    // ── Send direct mail (AJAX — returns JSON) ───────────────────────────
    public function send(Request $request)
    {
        $request->validate([
            'recipients'   => 'required|array|min:1',
            'recipients.*' => 'exists:users,id',
            'subject'      => 'required|string|max:150',
            'message'      => 'required|string|max:5000',
            'cta_label'    => 'nullable|string|max:60',
            'cta_url'      => 'nullable|url|max:300',
        ]);

        $users  = User::whereIn('id', $request->recipients)->get();
        $total  = $users->count();
        $sent   = 0;
        $failed = 0;
        $results = [];

        foreach ($users as $user) {
            $log = MailLogger::log(
                'admin_direct',
                $user->email,
                $user->name,
                $user->id,
                fn() => Mail::to($user->email)->send(new AdminDirectMail(
                    recipientName: $user->name,
                    mailSubject:   $request->subject,
                    messageBody:   $request->message,
                    ctaLabel:      $request->cta_label ?: null,
                    ctaUrl:        $request->cta_url   ?: null,
                )),
                'admin_direct'
            );

            if ($log->success) {
                $sent++;
                $results[] = ['name' => $user->name, 'email' => $user->email, 'status' => 'ok'];
            } else {
                $failed++;
                $results[] = ['name' => $user->name, 'email' => $user->email, 'status' => 'fail', 'error' => $log->error_message];
            }
        }

        return response()->json([
            'total'   => $total,
            'sent'    => $sent,
            'failed'  => $failed,
            'results' => $results,
        ]);
    }

    // ── Mail logs index ───────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = MailLog::with('user')->latest();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('mail_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('success', true);
            } elseif ($request->status === 'failed') {
                $query->where('success', false);
            }
        }

        // Search by email or name
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('recipient_email', 'like', "%{$s}%")
                  ->orWhere('recipient_name', 'like', "%{$s}%");
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Stats per type — MySQL-safe aggregate
        $typeCounts = MailLog::selectRaw('mail_type, COUNT(*) as total, SUM(success = 1) as sent_ok, SUM(success = 0) as failed')
            ->groupBy('mail_type')
            ->get()
            ->keyBy('mail_type');

        // Single aggregate instead of 5 separate COUNT queries
        $agg = MailLog::selectRaw(
            "COUNT(*) as total,
             SUM(CASE WHEN success=1 THEN 1 ELSE 0 END) as success_count,
             SUM(CASE WHEN success=0 THEN 1 ELSE 0 END) as failed_count,
             SUM(CASE WHEN mail_type IN ('otp','otp_resend') AND otp_verified=1 THEN 1 ELSE 0 END) as otp_ok,
             SUM(CASE WHEN mail_type IN ('otp','otp_resend') AND (otp_verified=0 OR otp_verified IS NULL) THEN 1 ELSE 0 END) as otp_pending"
        )->first();

        $overallStats = [
            'total'          => (int) ($agg->total        ?? 0),
            'success'        => (int) ($agg->success_count ?? 0),
            'failed'         => (int) ($agg->failed_count  ?? 0),
            'otp_verified'   => (int) ($agg->otp_ok        ?? 0),
            'otp_unverified' => (int) ($agg->otp_pending   ?? 0),
        ];

        $types = array_keys(MailLog::$typesMeta);

        return view('admin.mail-logs', compact('logs', 'typeCounts', 'overallStats', 'types'));
    }
}
