<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject', 200);
            $table->text('message');
            $table->string('category', 60)->default('general');
            // open, in_progress, resolved, closed
            $table->string('status', 20)->default('open');
            // low, medium, high, urgent
            $table->string('priority', 20)->default('medium');
            $table->boolean('admin_replied')->default(false);
            $table->boolean('user_read')->default(true);   // student read flag
            $table->boolean('admin_read')->default(false); // admin read flag
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('support_tickets'); }
};
