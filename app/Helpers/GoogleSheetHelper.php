<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetHelper
{
    const WEBHOOK_URL = 'https://script.google.com/macros/s/AKfycbzvAktosuEVs_Tgr-N290i5BakQpZUid4wGwEfEYj0Iz_cJefw-0fYkhBowc2_hT_OJ/exec';

    // ── Single row upsert ──────────────────────────────────────
    public static function sync(string $sheet, array $data, string $action = 'sync'): void
    {
        try {
            $data['sheet']  = $sheet;
            $data['action'] = $action;
            Http::timeout(10)->withoutRedirecting()->get(self::WEBHOOK_URL, $data);
        } catch (\Throwable $e) {
            Log::warning("Sheet sync [{$sheet}:{$action}] failed: " . $e->getMessage());
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
            // Send in chunks of 50 to avoid URL length limits
            $chunks = array_chunk($rows, 50);
            foreach ($chunks as $i => $chunk) {
                Http::timeout(30)->withoutRedirecting()->get(self::WEBHOOK_URL, [
                    'sheet'  => $sheet,
                    'action' => $i === 0 ? 'clear_and_load' : 'clear_and_load',
                    'rows'   => json_encode($chunk),
                ]);
                // small delay between chunks
                if (count($chunks) > 1) usleep(300000);
            }
        } catch (\Throwable $e) {
            Log::warning("Sheet bulk [{$sheet}] failed: " . $e->getMessage());
        }
    }

    // ── MASTER SYNC — push entire DB to all sheets ─────────────
    public static function syncFullDB(): array
    {
        $results = [];

        // 1. USERS
        $users = DB::table('users')->orderBy('id')->get();
        self::bulkLoad('users', $users->map(fn($u) => [
            'id'            => $u->id,
            'name'          => $u->name,
            'email'         => $u->email,
            'role'          => $u->role,
            'status'        => $u->is_blocked ? 'blocked' : 'active',
            'registered_at' => $u->created_at,
        ])->toArray());
        $results['users'] = $users->count();

        // 2. COURSES
        $courses = DB::table('courses')->orderBy('id')->get();
        self::bulkLoad('courses', $courses->map(fn($c) => [
            'id'         => $c->id,
            'title'      => $c->title,
            'category'   => $c->category,
            'level'      => $c->level,
            'instructor' => $c->instructor_name,
            'published'  => $c->is_published ? 'Yes' : 'No',
            'price'      => $c->price,
            'duration'   => $c->duration_hours,
            'created_at' => $c->created_at,
        ])->toArray());
        $results['courses'] = $courses->count();

        // 3. ENROLLMENTS (course_user pivot)
        $enrollments = DB::table('course_user')
            ->join('users',   'course_user.user_id',   '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->select('course_user.*', 'users.name as user_name', 'courses.title as course_title')
            ->orderBy('course_user.created_at')
            ->get();
        self::bulkLoad('enrollments', $enrollments->map(fn($e) => [
            'id'           => $e->user_id . '-' . $e->course_id,
            'user_id'      => $e->user_id,
            'user_name'    => $e->user_name,
            'course_id'    => $e->course_id,
            'course_title' => $e->course_title,
            'progress'     => $e->progress,
            'enrolled_at'  => $e->created_at,
        ])->toArray());
        $results['enrollments'] = $enrollments->count();

        // 4. TASKS
        $tasks = DB::table('tasks')
            ->leftJoin('courses', 'tasks.course_id', '=', 'courses.id')
            ->select('tasks.*', 'courses.title as course_title')
            ->orderBy('tasks.id')->get();
        self::bulkLoad('tasks', $tasks->map(fn($t) => [
            'id'         => $t->id,
            'title'      => $t->title,
            'course'     => $t->course_title,
            'points'     => $t->points ?? 0,
            'due_date'   => $t->due_date ?? '',
            'created_at' => $t->created_at,
        ])->toArray());
        $results['tasks'] = $tasks->count();

        // 5. TASK SUBMISSIONS
        $subs = DB::table('task_submissions')
            ->leftJoin('users', 'task_submissions.user_id', '=', 'users.id')
            ->leftJoin('tasks', 'task_submissions.task_id', '=', 'tasks.id')
            ->leftJoin('courses', 'tasks.course_id', '=', 'courses.id')
            ->select('task_submissions.*',
                     'users.name as user_name',
                     'tasks.title as task_title',
                     'courses.title as course_title')
            ->orderBy('task_submissions.id')->get();
        self::bulkLoad('task_submissions', $subs->map(fn($s) => [
            'id'           => $s->id,
            'user'         => $s->user_name,
            'task'         => $s->task_title,
            'course'       => $s->course_title,
            'status'       => $s->status,
            'grade'        => $s->grade ?? '',
            'points'       => $s->points_awarded ?? '',
            'submitted_at' => $s->created_at,
        ])->toArray());
        $results['task_submissions'] = $subs->count();

        // 6. MAIL LOGS
        $mails = DB::table('mail_logs')->orderBy('id')->get();
        self::bulkLoad('mail_logs', $mails->map(fn($m) => [
            'id'        => $m->id,
            'type'      => $m->mail_type,
            'recipient' => $m->recipient_email,
            'user_id'   => $m->user_id ?? '',
            'success'   => $m->success ? 'Yes' : 'No',
            'context'   => $m->context ?? '',
            'sent_at'   => $m->created_at,
        ])->toArray());
        $results['mail_logs'] = $mails->count();

        // 7. ACTIVITY LOGS
        $acts = DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name as user_name')
            ->orderBy('activity_logs.id')->get();
        self::bulkLoad('activity_logs', $acts->map(fn($a) => [
            'id'         => $a->id,
            'user'       => $a->user_name ?? 'System',
            'action'     => $a->action,
            'model'      => $a->model_type ?? '',
            'model_id'   => $a->model_id ?? '',
            'detail'     => $a->detail ?? '',
            'ip'         => $a->ip_address ?? '',
            'created_at' => $a->created_at,
        ])->toArray());
        $results['activity_logs'] = $acts->count();

        // 8. SUPPORT TICKETS
        $tickets = DB::table('support_tickets')
            ->leftJoin('users', 'support_tickets.user_id', '=', 'users.id')
            ->select('support_tickets.*', 'users.name as user_name')
            ->orderBy('support_tickets.id')->get();
        self::bulkLoad('support_tickets', $tickets->map(fn($t) => [
            'id'         => $t->id,
            'user'       => $t->user_name,
            'subject'    => $t->subject,
            'status'     => $t->status,
            'priority'   => $t->priority,
            'created_at' => $t->created_at,
        ])->toArray());
        $results['support_tickets'] = $tickets->count();

        // 9. TICKET REPLIES
        $replies = DB::table('ticket_replies')
            ->leftJoin('users', 'ticket_replies.user_id', '=', 'users.id')
            ->select('ticket_replies.*', 'users.name as user_name')
            ->orderBy('ticket_replies.id')->get();
        self::bulkLoad('ticket_replies', $replies->map(fn($r) => [
            'id'         => $r->id,
            'ticket_id'  => $r->ticket_id,
            'user'       => $r->user_name ?? 'System',
            'message'    => mb_substr($r->message ?? '', 0, 200),
            'created_at' => $r->created_at,
        ])->toArray());
        $results['ticket_replies'] = $replies->count();

        // 10. CHAT MESSAGES
        $chats = DB::table('chat_messages')
            ->leftJoin('users', 'chat_messages.user_id', '=', 'users.id')
            ->select('chat_messages.*', 'users.name as user_name')
            ->orderBy('chat_messages.id')->get();
        self::bulkLoad('chat_messages', $chats->map(fn($c) => [
            'id'         => $c->id,
            'user'       => $c->user_name,
            'session'    => $c->session_id ?? '',
            'message'    => mb_substr($c->message ?? '', 0, 200),
            'is_admin'   => ($c->is_admin ?? false) ? 'Yes' : 'No',
            'is_read'    => ($c->is_read ?? false) ? 'Yes' : 'No',
            'status'     => $c->chat_status ?? '',
            'created_at' => $c->created_at,
        ])->toArray());
        $results['chat_messages'] = $chats->count();

        // 11. ANNOUNCEMENTS
        $anns = DB::table('announcements')->orderBy('id')->get();
        self::bulkLoad('announcements', $anns->map(fn($a) => [
            'id'         => $a->id,
            'title'      => $a->title,
            'body'       => mb_substr($a->body ?? '', 0, 300),
            'active'     => ($a->is_active ?? false) ? 'Yes' : 'No',
            'created_at' => $a->created_at,
        ])->toArray());
        $results['announcements'] = $anns->count();

        // 12. ADS
        $ads = DB::table('ads')->orderBy('id')->get();
        self::bulkLoad('ads', $ads->map(fn($a) => [
            'id'         => $a->id,
            'title'      => $a->title,
            'link'       => $a->link ?? '',
            'active'     => ($a->is_active ?? false) ? 'Yes' : 'No',
            'deadline'   => $a->deadline ?? '',
            'created_at' => $a->created_at,
        ])->toArray());
        $results['ads'] = $ads->count();

        // 13. FINANCE ACCOUNTS
        try {
            $faccs = DB::table('finance_accounts')->orderBy('id')->get();
            self::bulkLoad('finance_accounts', $faccs->map(fn($f) => [
                'id'         => $f->id,
                'name'       => $f->name,
                'type'       => $f->type ?? '',
                'balance'    => $f->balance ?? 0,
                'currency'   => $f->currency ?? 'INR',
                'created_at' => $f->created_at,
            ])->toArray());
            $results['finance_accounts'] = $faccs->count();
        } catch (\Throwable $e) { $results['finance_accounts'] = 'skipped'; }

        // 14. FINANCE TRANSACTIONS
        try {
            $ftxns = DB::table('finance_transactions')->orderBy('id')->get();
            self::bulkLoad('finance_transactions', $ftxns->map(fn($t) => [
                'id'         => $t->id,
                'account'    => $t->account_id ?? '',
                'type'       => $t->transaction_type ?? '',
                'flow'       => $t->flow_type ?? '',
                'amount'     => $t->amount ?? 0,
                'currency'   => $t->currency ?? 'INR',
                'status'     => $t->status ?? '',
                'note'       => mb_substr($t->notes ?? '', 0, 200),
                'created_at' => $t->created_at,
            ])->toArray());
            $results['finance_transactions'] = $ftxns->count();
        } catch (\Throwable $e) { $results['finance_transactions'] = 'skipped'; }

        // 15. STAFF WORK LOGS
        try {
            $wlogs = DB::table('staff_work_logs')
                ->leftJoin('users', 'staff_work_logs.user_id', '=', 'users.id')
                ->select('staff_work_logs.*', 'users.name as staff_name')
                ->orderBy('staff_work_logs.id')->get();
            self::bulkLoad('staff_work_logs', $wlogs->map(fn($w) => [
                'id'        => $w->id,
                'staff'     => $w->staff_name,
                'date'      => $w->work_date ?? '',
                'clock_in'  => $w->clock_in ?? '',
                'clock_out' => $w->clock_out ?? '',
                'hours'     => $w->hours_worked ?? '',
                'status'    => $w->status ?? '',
            ])->toArray());
            $results['staff_work_logs'] = $wlogs->count();
        } catch (\Throwable $e) { $results['staff_work_logs'] = 'skipped'; }

        // 16. SALARY PAYMENTS
        try {
            $salaries = DB::table('salary_payments')
                ->leftJoin('users', 'salary_payments.staff_id', '=', 'users.id')
                ->select('salary_payments.*', 'users.name as staff_name')
                ->orderBy('salary_payments.id')->get();
            self::bulkLoad('salary_payments', $salaries->map(fn($s) => [
                'id'       => $s->id,
                'staff'    => $s->staff_name,
                'amount'   => $s->amount ?? 0,
                'currency' => $s->currency ?? 'INR',
                'month'    => $s->payment_month ?? '',
                'paid_at'  => $s->paid_at ?? '',
                'status'   => $s->status ?? '',
            ])->toArray());
            $results['salary_payments'] = $salaries->count();
        } catch (\Throwable $e) { $results['salary_payments'] = 'skipped'; }

        return $results;
    }
}
