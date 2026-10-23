<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('staff_work_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_work_logs', 'status')) {
                $table->string('status', 20)->default('present')->after('duration_seconds');
            }
            if (!Schema::hasColumn('staff_work_logs', 'marked_at')) {
                $table->timestamp('marked_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void {
        Schema::table('staff_work_logs', function (Blueprint $table) {
            if (Schema::hasColumn('staff_work_logs', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('staff_work_logs', 'marked_at')) {
                $table->dropColumn('marked_at');
            }
        });
    }
};
