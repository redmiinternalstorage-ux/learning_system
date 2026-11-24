<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public bool $withinTransaction = false;

    private function addIndex(string $table, array|string $cols, string $name): void
    {
        try {
            if (!Schema::hasTable($table)) return;
            $driver = DB::getDriverName();
            if ($driver === 'pgsql') {
                $exists = DB::select(
                    "SELECT 1 FROM pg_indexes WHERE schemaname=current_schema() AND tablename=? AND indexname=?",
                    [$table, $name]
                );
            } elseif (in_array($driver, ['mysql', 'mariadb'])) {
                $exists = DB::select(
                    "SELECT 1 FROM information_schema.statistics WHERE table_schema=DATABASE() AND table_name=? AND index_name=? LIMIT 1",
                    [$table, $name]
                );
            } else {
                $exists = DB::select("SELECT 1 FROM sqlite_master WHERE type='index' AND name=?", [$name]);
            }
            if (!empty($exists)) return;
            Schema::table($table, fn(Blueprint $t) => $t->index($cols, $name));
        } catch (\Throwable $e) {}
    }

    public function up(): void
    {
        $this->addIndex('subjects',          'semester_id',             'idx_subjects_semester');
        $this->addIndex('units',             'subject_id',              'idx_units_subject');
        $this->addIndex('topics',            'unit_id',                 'idx_topics_unit');
        $this->addIndex('materials',         'topic_id',                'idx_materials_topic');
        $this->addIndex('questions',         'topic_id',                'idx_questions_topic');
        $this->addIndex('question_attempts', 'user_id',                 'idx_qa_user');
        $this->addIndex('question_attempts', 'question_id',             'idx_qa_question');
        $this->addIndex('notes',             'user_id',                 'idx_notes_user');
        $this->addIndex('bookmarks',         'user_id',                 'idx_bookmarks_user');
        $this->addIndex('data_exports',      'user_id',                 'idx_de_user');
        $this->addIndex('data_exports',      ['status', 'created_at'],  'idx_de_status_created');
        $this->addIndex('support_tickets',   ['user_read', 'admin_replied'], 'idx_st_read_replied');
        $this->addIndex('announcements',     ['is_active', 'created_at'], 'idx_ann_active_created');
    }

    public function down(): void {}
};
