<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_date_passwords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('day_of_month'); // 1 to 31
            $table->string('password'); // Hashed password
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'day_of_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_date_passwords');
    }
};
