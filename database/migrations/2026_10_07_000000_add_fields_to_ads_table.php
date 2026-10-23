<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (!Schema::hasColumn('ads', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('ads', 'start_date')) {
                $table->date('start_date')->nullable()->after('powered_by');
            }
            if (!Schema::hasColumn('ads', 'duration_days')) {
                $table->integer('duration_days')->default(7)->after('start_date');
            }
            if (!Schema::hasColumn('ads', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('duration_days');
            }
            if (!Schema::hasColumn('ads', 'advance_paid')) {
                $table->decimal('advance_paid', 10, 2)->default(0)->after('total_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['description', 'start_date', 'duration_days', 'total_amount', 'advance_paid']);
        });
    }
};
