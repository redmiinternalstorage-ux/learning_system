<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('task_submissions', 'points_awarded')) {
                $table->unsignedInteger('points_awarded')->nullable()->after('grade');
            }
        });

        // Sync existing graded submissions: points_awarded = grade value
        DB::statement(
            'UPDATE task_submissions SET points_awarded = grade WHERE status = ? AND grade IS NOT NULL',
            ['graded']
        );
    }

    public function down(): void
    {
        Schema::table('task_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('task_submissions', 'points_awarded')) {
                $table->dropColumn('points_awarded');
            }
        });
    }
};
