<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Disable transaction wrapping — PostgreSQL aborts entire transaction on any error
    public bool $withinTransaction = false;

    private function safeAddIndex(string $table, array|string $cols, string $name): void
    {
        try {
            // Check table exists via raw pg_class query (no Schema::hasTable in transaction)
            $tableExists = DB::selectOne(
                "SELECT 1 FROM pg_class c JOIN pg_namespace n ON n.oid = c.relnamespace
                 WHERE n.nspname = current_schema() AND c.relname = ? AND c.relkind IN ('r','p')",
                [$table]
            );
            if (!$tableExists) return;

            // Check index exists
            $indexExists = DB::selectOne(
                "SELECT 1 FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ? AND indexname = ?",
                [$table, $name]
            );
            if ($indexExists) return;

            Schema::table($table, fn(Blueprint $t) => $t->index($cols, $name));
        } catch (\Throwable $e) {
            // Skip silently — index may already exist or table may be missing
        }
    }

    public function up(): void
    {
        $this->safeAddIndex('subjects',          'semester_id',              'idx_subjects_semester');
        $this->safeAddIndex('units',             'subject_id',               'idx_units_subject');
        $this->safeAddIndex('topics',            'unit_id',                  'idx_topics_unit');
        $this->safeAddIndex('materials',         'topic_id',                 'idx_materials_topic');
        $this->safeAddIndex('questions',         'topic_id',                 'idx_questions_topic');
        $this->safeAddIndex('question_attempts', 'user_id',                  'idx_qa_user');
        $this->safeAddIndex('question_attempts', 'question_id',              'idx_qa_question');
        $this->safeAddIndex('notes',             'user_id',                  'idx_notes_user');
        $this->safeAddIndex('bookmarks',         'user_id',                  'idx_bookmarks_user');
        $this->safeAddIndex('data_exports',      'user_id',                  'idx_de_user');
        $this->safeAddIndex('data_exports',      ['status', 'created_at'],   'idx_de_status_created');
        $this->safeAddIndex('support_tickets',   ['user_read', 'admin_replied'], 'idx_st_read_replied');
        $this->safeAddIndex('announcements',     ['is_active', 'created_at'],'idx_ann_active_created');
    }

    public function down(): void {}
};
