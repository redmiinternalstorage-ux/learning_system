<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'salary_currency')) {
                $table->string('salary_currency', 10)->default('INR');
            }
            if (!Schema::hasColumn('users', 'salary_cycle')) {
                $table->string('salary_cycle', 20)->default('monthly');
            }
            if (!Schema::hasColumn('users', 'joining_date')) {
                $table->date('joining_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'staff_notes')) {
                $table->text('staff_notes')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            foreach (['position','salary','salary_currency','salary_cycle','joining_date','staff_notes'] as $col) {
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
            }
        });
    }
};
