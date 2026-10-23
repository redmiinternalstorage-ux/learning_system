<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'auto_change_pass')) {
                $table->boolean('auto_change_pass')->default(false)->after('is_blocked');
            }
            if (!Schema::hasColumn('users', 'auto_change_pass_date')) {
                $table->date('auto_change_pass_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'auto_change_pass_value')) {
                $table->string('auto_change_pass_value')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['auto_change_pass','auto_change_pass_date','auto_change_pass_value'] as $col) {
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
            }
        });
    }
};
