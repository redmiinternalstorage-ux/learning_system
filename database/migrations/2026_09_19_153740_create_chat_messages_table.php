<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            // session_id groups messages per chat conversation
            $table->string('session_id', 64);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->boolean('is_admin')->default(false);   // true = admin sent it
            $table->boolean('is_read')->default(false);
            // waiting, active, closed
            $table->string('chat_status', 20)->default('waiting');
            $table->timestamps();
            $table->index(['session_id', 'created_at']);
            $table->index(['user_id', 'chat_status']);
        });
    }
    public function down(): void { Schema::dropIfExists('chat_messages'); }
};
