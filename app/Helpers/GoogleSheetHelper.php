<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Syncs entire DB to Google Sheets via Apps Script webhook.
 *
 * Sheet structure:
 *   USER SIDE  — prefixed "U - "
 *   ADMIN SIDE — prefixed "A - "
 */
class GoogleSheetHelper
{
    const WEBHOOK_URL = 'https://script.google.com/macros/s/AKfycbw6NJCSuJKD5E9b__hUpE4tnrKCO2EclR66WSe54JGS0DVwrKs1mOnZndgh-bUWqe2B/exec';

    // ── Single row upsert ──────────────────────────────────────
    public static function sync(string $sheet, array $data, string $action = 'sync'): void
    {
        try {
            $data['sheet']  = $sheet;
            $data['action'] = $action;
            Http::timeout(10)->withoutRedirecting()->get(self::WEBHOOK_URL, $data);
        } catch (\Throwable $e) {
            Log::warning("Sheet sync [{$sheet}] failed: " . $e->getMessage());
        }
    }

    // ── Delete a row ───────────────────────────────────────────
    public static function delete(string $sheet, int $id): void
    {
        try {
            Http::timeout(10)->withoutRedirecting()->get(self::WEBHOOK_URL, [
                'sheet'  => $sheet,
                'action' => 'delete',
                'id'     => $id,
            ]);
        } catch (\Throwable $e) {
            Log::warning("Sheet delete [{$sheet}:{$id}] failed: " . $e->getMessage());
        }
    }

    // ── Bulk load one sheet (clear + reload) ───────────────────
    public static function bulkLoad(string $sheet, array $rows): void
    {
        try {
            $chunks = array_chunk($rows, 50);
            foreach ($chunks as $chunk) {
                Http::timeout(30)->withoutRedirecting()->get(self::WEBHOOK_URL, [
                    'sheet'  => $sheet,
                    'action' => 'clear_and_load',
                    'rows'   => json_encode($chunk),
                ]);
                if (count($chunks) > 1) usleep(300000);
            }
        } catch (\Throwable $e) {
            Log::warning("Sheet bulk [{$sheet}] failed: " . $e->getMessage());
        }
    }

    // ── User sync shortcuts ────────────────────────────────────
    public static function syncUser($user, string $action = 'sync'): void
    {
        self::sync('U - Users', [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'role'          => $user->role,
            'status'        => $user->is_blocked ? 'blocked' : 'active',
            'registered_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
        ], $action);
    }

    public static function deleteUser(int $userId, string $email): void
    {
        self::delete('U - Users', $userId);
    }

    // ── MASTER SYNC — push entire DB to all sheets ─────────────
    public static function syncFullDB(): array
    {
        $results = [];

        // ══ USER SIDE ════════════════════════════════════════

        // U - Users
        $users = DB::table('users')->orderBy('id')->get();
        self::bulkLoad('U - Users', $users->map(fn($u) => [
            'id'            => $u->id,
            'name'          => $u->name,
            'email'         => $u->email,
            'role'          => $u->role,
            'status'        => $u->is_blocked ? 'blocked' : 'active',
            'registered_at' => $u->created_at,
        ])->toArray());
        $results['U-Users'] = $users->count();

        // U - Enrollments
        $enrollments = DB::table('course_user')
            ->join('users',   'course_user.user_id',   '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->select('course_user.*', 'users.name as user_name', 'courses.title as course_title')
            ->orderBy('course_user.created_at')->get();
        self::bulkLoad('U - Enrollments', $enrollments->map(fn($e) => [
            'id'           => $e->user_id . '-' . $e->course_id,
            'user_id'      => $e->user_id,
            'user_name'    => $e->user_name,
            'course_id'    => $e->course_id,
            'course_title' => $e->course_title,
            'progress'     => $e->progress . '%',
            'enrolled_at'  => $e->created_at,
        ])->toArray());
        $results['U-Enrollments'] = $enrollments->count();

        // U - Task Submissions
        $subs = DB::table('task_submissions')
            ->leftJoin('users',   'task_submissions.user_id', '=', 'users.id')
            ->leftJoin('tasks',   'task_submissions.task_id', '=', 'tasks.id')
            ->leftJoin('courses', 'tasks.course_id',          '=', 'courses.id')
            ->select('task_submissions.*', 'users.name as user_name', 'tasks.title as task_title', 'courses.title as course_title')
            ->orderBy('task_submissions.id')->get();
        self::bulkLoad('U - Task Submissions', $subs->map(fn($s) => [
            'id'           => $s->id,
            'user'         => $s->user_name,
            'task'         => $s->task_title,
            'course'       => $s->course_title,
            'status'       => $s->status,
            'grade'        => $s->grade ?? '-',
            'points'       => $s->points_awarded ?? '-',
            'submitted_at' => $s->created_at,
        ])->toArray());
        $results['U-TaskSubmissions'] = $subs->count();

        // U - Support Tickets
        $tickets = DB::table('support_tickets')
            ->leftJoin('users', 'support_tickets.user_id', '=', 'users.id')
            ->select('support_tickets.*', 'users.name as user_name')
            ->orderBy('support_tickets.id')->get();
        self::bulkLoad('U - Support Tickets', $tickets->map(fn($t) => [
            'id'         => $t->id,
            'user'       => $t->user_name,
            'subject'    => $t->subject,
            'category'   => $t->category ?? '-',
            'priority'   => $t->priority,
            'status'     => $t->status,
            'created_at' => $t->created_at,
        ])->toArray());
        $results['U-SupportTickets'] = $tickets->count();

        // U - Ticket Replies
        $replies = DB::table('ticket_replies')
            ->leftJoin('users', 'ticket_replies.user_id', '=', 'users.id')
            ->select('ticket_replies.*', 'users.name as user_name')
            ->orderBy('ticket_replies.id')->get();
        self::bulkLoad('U - Ticket Replies', $replies->map(fn($r) => [
            'id'         => $r->id,
            'ticket_id'  => $r->ticket_id,
            'user'       => $r->user_name ?? 'System',
            'is_admin'   => ($r->is_admin ?? false) ? 'Yes' : 'No',
            'message'    => mb_substr($r->message ?? '', 0, 200),
            'created_at' => $r->created_at,
        ])->toArray());
        $results['U-TicketReplies'] = $replies->count();

        // U - Chat Messages
        $chats = DB::table('chat_messages')
            ->leftJoin('users', 'chat_messages.user_id', '=', 'users.id')
            ->select('chat_messages.*', 'users.name as user_name')
            ->orderBy('chat_messages.id')->get();
        self::bulkLoad('U - Chat Messages', $chats->map(fn($c) => [
            'id'         => $c->id,
            'user'       => $c->user_name,
            'session'    => $c->session_id ?? '-',
            'message'    => mb_substr($c->message ?? '', 0, 200),
            'is_admin'   => ($c->is_admin ?? false) ? 'Yes' : 'No',
            'is_read'    => ($c->is_read ?? false) ? 'Yes' : 'No',
            'status'     => $c->chat_status ?? '-',
            'created_at' => $c->created_at,
        ])->toArray());
        $results['U-ChatMessages'] = $chats->count();

        // U - Certificates (completed enrollments)
        $certs = DB::table('course_user')
            ->join('users',   'course_user.user_id',   '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('course_user.progress', '>=', 100)
            ->select('course_user.*', 'users.name as user_name', 'courses.title as course_title')
            ->orderBy('course_user.updated_at')->get();
        self::bulkLoad('U - Certificates', $certs->map(fn($c) => [
            'id'           => $c->user_id . '-' . $c->course_id,
            'user_id'      => $c->user_id,
            'user_name'    => $c->user_name,
            'course_id'    => $c->course_id,
            'course_title' => $c->course_title,
            'progress'     => '100%',
            'completed_at' => $c->updated_at,
        ])->toArray());
        $results['U-Certificates'] = $certs->count();

        // U - Data Exports
        try {
            $exports = DB::table('data_exports')
                ->leftJoin('users', 'data_exports.user_id', '=', 'users.id')
                ->select('data_exports.*', 'users.name as user_name')
                ->orderBy('data_exports.id')->get();
            self::bulkLoad('U - Data Exports', $exports->map(fn($e) => [
                'id'         => $e->id,
                'user'       => $e->user_name,
                'type'       => $e->export_type ?? '-',
                'action'     => '-',
                'status'     => $e->status,
                'file_size'  => $e->file_size ?? '-',
                'created_at' => $e->created_at,
            ])->toArray());
            $results['U-DataExports'] = $exports->count();
        } catch (\Throwable $e) { $results['U-DataExports'] = 'skipped'; }

        // U - Notes
        try {
            $notes = DB::table('notes')
                ->leftJoin('users', 'notes.user_id', '=', 'users.id')
                ->select('notes.*', 'users.name as user_name')
                ->orderBy('notes.id')->get();
            self::bulkLoad('U - Notes', $notes->map(fn($n) => [
                'id'           => $n->id,
                'user'         => $n->user_name,
                'topic'        => $n->topic_id ?? '-',
                'note_preview' => mb_substr($n->content ?? '', 0, 100),
                'created_at'   => $n->created_at,
            ])->toArray());
            $results['U-Notes'] = $notes->count();
        } catch (\Throwable $e) { $results['U-Notes'] = 'skipped'; }

        // U - Bookmarks
        try {
            $bookmarks = DB::table('bookmarks')
                ->leftJoin('users', 'bookmarks.user_id', '=', 'users.id')
                ->select('bookmarks.*', 'users.name as user_name')
                ->orderBy('bookmarks.id')->get();
            self::bulkLoad('U - Bookmarks', $bookmarks->map(fn($b) => [
                'id'         => $b->id,
                'user'       => $b->user_name,
                'topic'      => $b->topic_id ?? '-',
                'created_at' => $b->created_at,
            ])->toArray());
            $results['U-Bookmarks'] = $bookmarks->count();
        } catch (\Throwable $e) { $results['U-Bookmarks'] = 'skipped'; }

        // ══ ADMIN SIDE ═══════════════════════════════════════

        // A - Courses
        $courses = DB::table('courses')->orderBy('id')->get();
        self::bulkLoad('A - Courses', $courses->map(fn($c) => [
            'id'         => $c->id,
            'title'      => $c->title,
            'category'   => $c->category,
            'level'      => $c->level,
            'instructor' => $c->instructor_name,
            'published'  => $c->is_published ? 'Yes' : 'No',
            'price'      => $c->price ?? 0,
            'duration'   => $c->duration_hours,
            'created_at' => $c->created_at,
        ])->toArray());
        $results['A-Courses'] = $courses->count();

        // A - Tasks
        $tasks = DB::table('tasks')
            ->leftJoin('courses', 'tasks.course_id', '=', 'courses.id')
            ->leftJoin('users',   'tasks.assigned_to', '=', 'users.id')
            ->select('tasks.*', 'courses.title as course_title', 'users.name as assigned_name')
            ->orderBy('tasks.id')->get();
        self::bulkLoad('A - Tasks', $tasks->map(fn($t) => [
            'id'          => $t->id,
            'title'       => $t->title,
            'course'      => $t->course_title,
            'points'      => $t->points ?? 0,
            'due_date'    => $t->due_date ?? '-',
            'assigned_to' => $t->assigned_name ?? '-',
            'created_at'  => $t->created_at,
        ])->toArray());
        $results['A-Tasks'] = $tasks->count();

        // A - Announcements
        $anns = DB::table('announcements')->orderBy('id')->get();
        self::bulkLoad('A - Announcements', $anns->map(fn($a) => [
            'id'         => $a->id,
            'title'      => $a->title,
            'body'       => mb_substr($a->body ?? '', 0, 200),
            'active'     => ($a->is_active ?? false) ? 'Yes' : 'No',
            'created_at' => $a->created_at,
        ])->toArray());
        $results['A-Announcements'] = $anns->count();

        // A - Ads
        try {
            $ads = DB::table('ads')->orderBy('id')->get();
            self::bulkLoad('A - Ads', $ads->map(fn($a) => [
                'id'           => $a->id,
                'title'        => $a->title,
                'powered_by'   => $a->powered_by ?? '-',
                'type'         => $a->type ?? '-',
                'total_amount' => $a->total_amount ?? 0,
                'advance_paid' => $a->advance_paid ?? 0,
                'due_amount'   => $a->due_amount ?? 0,
                'active'       => ($a->is_active ?? false) ? 'Yes' : 'No',
                'deadline'     => $a->deadline ?? '-',
                'created_at'   => $a->created_at,
            ])->toArray());
            $results['A-Ads'] = $ads->count();
        } catch (\Throwable $e) { $results['A-Ads'] = 'skipped'; }

        // A - Staff
        $staff = DB::table('users')->where('role', 'staff')->orderBy('id')->get();
        self::bulkLoad('A - Staff', $staff->map(fn($s) => [
            'id'          => $s->id,
            'name'        => $s->name,
            'email'       => $s->email,
            'position'    => $s->position ?? '-',
            'salary'      => $s->salary ?? 0,
            'currency'    => $s->salary_currency ?? 'INR',
            'cycle'       => $s->salary_cycle ?? 'monthly',
            'joining_date'=> $s->joining_date ?? '-',
            'status'      => $s->is_blocked ? 'blocked' : 'active',
            'created_at'  => $s->created_at,
        ])->toArray());
        $results['A-Staff'] = $staff->count();

        // A - Staff Work Logs
        try {
            $wlogs = DB::table('staff_work_logs')
                ->leftJoin('users', 'staff_work_logs.user_id', '=', 'users.id')
                ->select('staff_work_logs.*', 'users.name as staff_name')
                ->orderBy('staff_work_logs.id')->get();
            self::bulkLoad('A - Staff Work Logs', $wlogs->map(fn($w) => [
                'id'        => $w->id,
                'staff'     => $w->staff_name,
                'date'      => $w->work_date ?? '-',
                'clock_in'  => $w->clock_in ?? '-',
                'clock_out' => $w->clock_out ?? '-',
                'hours'     => $w->hours_worked ?? '-',
                'status'    => $w->status ?? '-',
            ])->toArray());
            $results['A-StaffWorkLogs'] = $wlogs->count();
        } catch (\Throwable $e) { $results['A-StaffWorkLogs'] = 'skipped'; }

        // A - Salary Payments
        try {
            $salaries = DB::table('salary_payments')
                ->leftJoin('users', 'salary_payments.staff_id', '=', 'users.id')
                ->select('salary_payments.*', 'users.name as staff_name')
                ->orderBy('salary_payments.id')->get();
            self::bulkLoad('A - Salary Payments', $salaries->map(fn($s) => [
                'id'       => $s->id,
                'staff'    => $s->staff_name,
                'amount'   => $s->amount ?? 0,
                'currency' => $s->currency ?? 'INR',
                'period'   => $s->period_label ?? $s->payment_month ?? '-',
                'hours'    => $s->hours_worked ?? '-',
                'status'   => $s->payment_status ?? $s->status ?? '-',
                'paid_at'  => $s->paid_at ?? '-',
            ])->toArray());
            $results['A-SalaryPayments'] = $salaries->count();
        } catch (\Throwable $e) { $results['A-SalaryPayments'] = 'skipped'; }

        // A - Finance Accounts
        try {
            $faccs = DB::table('finance_accounts')->orderBy('id')->get();
            self::bulkLoad('A - Finance Accounts', $faccs->map(fn($f) => [
                'id'         => $f->id,
                'name'       => $f->name,
                'type'       => $f->type ?? '-',
                'balance'    => $f->balance ?? 0,
                'currency'   => 'INR',
                'created_at' => $f->created_at,
            ])->toArray());
            $results['A-FinanceAccounts'] = $faccs->count();
        } catch (\Throwable $e) { $results['A-FinanceAccounts'] = 'skipped'; }

        // A - Finance Transactions
        try {
            $ftxns = DB::table('finance_transactions')->orderBy('id')->get();
            self::bulkLoad('A - Finance Transactions', $ftxns->map(fn($t) => [
                'id'         => $t->id,
                'code'       => $t->transaction_code ?? '-',
                'type'       => $t->transaction_type ?? '-',
                'flow'       => $t->flow_type ?? '-',
                'sender'     => $t->sender_name ?? '-',
                'receiver'   => $t->receiver_name ?? '-',
                'amount'     => $t->amount ?? 0,
                'status'     => $t->status ?? '-',
                'notes'      => mb_substr($t->notes ?? '', 0, 150),
                'created_at' => $t->created_at,
            ])->toArray());
            $results['A-FinanceTransactions'] = $ftxns->count();
        } catch (\Throwable $e) { $results['A-FinanceTransactions'] = 'skipped'; }

        // A - Activity Logs
        $acts = DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name as user_name')
            ->orderBy('activity_logs.id')->get();
        self::bulkLoad('A - Activity Logs', $acts->map(fn($a) => [
            'id'         => $a->id,
            'user'       => $a->user_name ?? 'System',
            'action'     => $a->action,
            'model'      => $a->model_type ?? '-',
            'model_id'   => $a->model_id ?? '-',
            'detail'     => mb_substr($a->description ?? $a->detail ?? '', 0, 200),
            'ip'         => $a->ip_address ?? '-',
            'created_at' => $a->created_at,
        ])->toArray());
        $results['A-ActivityLogs'] = $acts->count();

        // A - Mail Logs (with full send status)
        $mails = DB::table('mail_logs')->orderBy('id')->get();
        self::bulkLoad('A - Mail Logs', $mails->map(fn($m) => [
            'id'             => $m->id,
            'mail_type'      => $m->mail_type,
            'recipient'      => $m->recipient_email,
            'recipient_name' => $m->recipient_name ?? '-',
            'user_id'        => $m->user_id ?? '-',
            'success'        => $m->success ? '✅ Sent' : '❌ Failed',
            'sent_at'        => $m->created_at,
            'error'          => $m->error_message ?? '-',
        ])->toArray());
        $results['A-MailLogs'] = $mails->count();

        // A - Site Settings
        try {
            $settings = DB::table('site_settings')->orderBy('id')->get();
            self::bulkLoad('A - Site Settings', $settings->map(fn($s) => [
                'id'         => $s->id,
                'key'        => $s->key,
                'value'      => mb_substr((string)($s->value ?? ''), 0, 200),
                'updated_at' => $s->updated_at,
            ])->toArray());
            $results['A-SiteSettings'] = $settings->count();
        } catch (\Throwable $e) { $results['A-SiteSettings'] = 'skipped'; }

        return $results;
    }
}
