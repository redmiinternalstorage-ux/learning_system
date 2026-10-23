<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Safely add index — driver-agnostic, skips if already exists.
     */
    private function addIndex(string $table, array|string $cols, string $name): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $driver = DB::getDriverName();

        try {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                // Check information_schema — works on MySQL/MariaDB
                $exists = DB::select(
                    "SELECT 1 FROM information_schema.statistics
                     WHERE table_schema = DATABASE()
                       AND table_name = ?
                       AND index_name = ?
                     LIMIT 1",
                    [$table, $name]
                );
            } else {
                // SQLite fallback
                $exists = DB::select(
                    "SELECT 1 FROM sqlite_master WHERE type='index' AND name=?",
                    [$name]
                );
            }

            if (!empty($exists)) {
                return;
            }

            Schema::table($table, function (Blueprint $t) use ($cols, $name) {
                $t->index($cols, $name);
            });
        } catch (\Throwable $e) {
            // Index already exists or column missing — skip silently
        }
    }

    /**
     * Safely drop index — silently skips if not found.
     */
    private function dropIdx(string $table, string $name): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        try {
            Schema::table($table, fn(Blueprint $t) => $t->dropIndex($name));
        } catch (\Throwable $e) {
            // Index didn't exist — nothing to do
        }
    }

    public function up(): void
    {
        // ── Users ──────────────────────────────────────────────────────────
        $this->addIndex('users', ['role', 'is_blocked'], 'idx_users_role_blocked');
        $this->addIndex('users', ['created_at'],          'idx_users_created_at');

        // ── Courses ────────────────────────────────────────────────────────
        $this->addIndex('courses', ['is_published', 'category'],   'idx_courses_published_cat');
        $this->addIndex('courses', ['is_published', 'created_at'], 'idx_courses_published_created');
        $this->addIndex('courses', ['level'],                       'idx_courses_level');

        // ── Course-User pivot ──────────────────────────────────────────────
        $this->addIndex('course_user', ['user_id',   'progress'], 'idx_cu_user_progress');
        $this->addIndex('course_user', ['course_id', 'progress'], 'idx_cu_course_progress');
        $this->addIndex('course_user', ['created_at'],            'idx_cu_created_at');

        // ── Tasks ──────────────────────────────────────────────────────────
        $this->addIndex('tasks', ['course_id'], 'idx_tasks_course_id');

        // ── Task Submissions ───────────────────────────────────────────────
        $this->addIndex('task_submissions', ['user_id', 'status'], 'idx_ts_user_status');
        $this->addIndex('task_submissions', ['task_id', 'status'], 'idx_ts_task_status');
        $this->addIndex('task_submissions', ['created_at'],        'idx_ts_created_at');

        // ── Mail Logs ──────────────────────────────────────────────────────
        $this->addIndex('mail_logs', ['user_id', 'mail_type'],  'idx_ml_user_type');
        $this->addIndex('mail_logs', ['success', 'created_at'], 'idx_ml_success_created');
        $this->addIndex('mail_logs', ['recipient_email'],       'idx_ml_recipient');

        // ── Activity Logs ──────────────────────────────────────────────────
        $this->addIndex('activity_logs', ['user_id', 'action'], 'idx_al_user_action');
        $this->addIndex('activity_logs', ['created_at'],        'idx_al_created_at');

        // ── Chat Messages ──────────────────────────────────────────────────
        $this->addIndex('chat_messages', ['session_id', 'created_at'], 'idx_cm_session_created');
        $this->addIndex('chat_messages', ['user_id', 'chat_status'],   'idx_cm_user_status');
        $this->addIndex('chat_messages', ['is_admin', 'is_read'],      'idx_cm_admin_read');

        // ── Support Tickets ────────────────────────────────────────────────
        $this->addIndex('support_tickets', ['user_id', 'status'],  'idx_st_user_status');
        $this->addIndex('support_tickets', ['status', 'priority'], 'idx_st_status_priority');

        // ── Ticket Replies ─────────────────────────────────────────────────
        $this->addIndex('ticket_replies', ['ticket_id'], 'idx_tr_ticket_id');

        // ── Staff Work Logs ────────────────────────────────────────────────
        $this->addIndex('staff_work_logs', ['user_id', 'work_date'], 'idx_swl_user_date');

        // ── Finance Transactions ───────────────────────────────────────────
        $this->addIndex('finance_transactions', ['status', 'flow_type'],           'idx_ft_status_flow');
        $this->addIndex('finance_transactions', ['transaction_type', 'created_at'], 'idx_ft_type_created');

        // ── Ads ────────────────────────────────────────────────────────────
        $this->dropIdx('ads', 'idx_ads_active_expires'); // remove old wrong-name index if present
        $this->addIndex('ads', ['is_active', 'deadline'], 'idx_ads_active_deadline');
    }

    public function down(): void
    {
        $this->dropIdx('users', 'idx_users_role_blocked');
        $this->dropIdx('users', 'idx_users_created_at');

        $this->dropIdx('courses', 'idx_courses_published_cat');
        $this->dropIdx('courses', 'idx_courses_published_created');
        $this->dropIdx('courses', 'idx_courses_level');

        $this->dropIdx('course_user', 'idx_cu_user_progress');
        $this->dropIdx('course_user', 'idx_cu_course_progress');
        $this->dropIdx('course_user', 'idx_cu_created_at');

        $this->dropIdx('tasks',            'idx_tasks_course_id');
        $this->dropIdx('task_submissions', 'idx_ts_user_status');
        $this->dropIdx('task_submissions', 'idx_ts_task_status');
        $this->dropIdx('task_submissions', 'idx_ts_created_at');

        $this->dropIdx('mail_logs', 'idx_ml_user_type');
        $this->dropIdx('mail_logs', 'idx_ml_success_created');
        $this->dropIdx('mail_logs', 'idx_ml_recipient');

        $this->dropIdx('activity_logs', 'idx_al_user_action');
        $this->dropIdx('activity_logs', 'idx_al_created_at');

        $this->dropIdx('chat_messages', 'idx_cm_session_created');
        $this->dropIdx('chat_messages', 'idx_cm_user_status');
        $this->dropIdx('chat_messages', 'idx_cm_admin_read');

        $this->dropIdx('support_tickets', 'idx_st_user_status');
        $this->dropIdx('support_tickets', 'idx_st_status_priority');

        $this->dropIdx('ticket_replies',       'idx_tr_ticket_id');
        $this->dropIdx('staff_work_logs',      'idx_swl_user_date');
        $this->dropIdx('finance_transactions', 'idx_ft_status_flow');
        $this->dropIdx('finance_transactions', 'idx_ft_type_created');

        $this->dropIdx('ads', 'idx_ads_active_deadline');
    }
};
