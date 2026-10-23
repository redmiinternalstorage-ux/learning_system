<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);          // e.g. user.blocked, course.created
            $table->string('target_type', 60)->nullable();  // User, Course, Task …
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_label')->nullable();     // human readable label
            $table->text('description')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }
    public function down(): void { Schema::dropIfExists('activity_logs'); }
};
