<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paid_by')->constrained('users')->cascadeOnDelete(); // finance user
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('INR');
            $table->string('period_label', 60)->nullable(); // e.g. "September 2026"cfxff
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->unsignedInteger('hours_worked')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('mail_sent')->default(false);
            $table->timestamps();
            $table->index(['staff_id','created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('salary_payments'); }
};
