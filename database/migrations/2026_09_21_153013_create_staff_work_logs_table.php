<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('staff_work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // staff user
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0); // filled on clock-out
            $table->text('notes')->nullable();
            $table->date('work_date');  // date of work
            $table->timestamps();
            $table->index(['user_id','work_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('staff_work_logs'); }
};
