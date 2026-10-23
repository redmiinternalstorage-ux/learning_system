<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'bank_account')) {
                $table->string('bank_account', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_ifsc')) {
                $table->string('bank_ifsc', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'account_holder')) {
                $table->string('account_holder', 100)->nullable();
            }
        });

        Schema::table('salary_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_payments', 'payment_status')) {
                $table->string('payment_status', 20)->default('pending');
            }
            if (!Schema::hasColumn('salary_payments', 'bank_account')) {
                $table->string('bank_account', 20)->nullable();
            }
            if (!Schema::hasColumn('salary_payments', 'bank_name')) {
                $table->string('bank_name', 100)->nullable();
            }
            if (!Schema::hasColumn('salary_payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
            if (!Schema::hasColumn('salary_payments', 'hourly_rate')) {
                $table->decimal('hourly_rate', 8, 2)->default(1000);
            }
        });
    }

    public function down(): void {
        Schema::table('users', function ($t) {
            foreach (['bank_account','bank_name','bank_ifsc','account_holder'] as $col) {
                if (Schema::hasColumn('users', $col)) $t->dropColumn($col);
            }
        });
        Schema::table('salary_payments', function ($t) {
            foreach (['payment_status','bank_account','bank_name','paid_at','hourly_rate'] as $col) {
                if (Schema::hasColumn('salary_payments', $col)) $t->dropColumn($col);
            }
        });
    }
};
