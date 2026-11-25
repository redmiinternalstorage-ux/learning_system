<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public bool $withinTransaction = false;

    public function up(): void
    {
        // Allowed college email domains
        Schema::create('campus_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('college_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Campus student profiles
        Schema::create('campus_students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('college_email')->unique();
            $table->string('password');
            $table->string('college_name')->nullable();
            $table->string('student_id')->nullable();
            $table->string('department')->nullable();
            $table->string('semester')->nullable();
            $table->string('phone')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        // Campus course enrollments
        Schema::create('campus_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('progress')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['campus_student_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_enrollments');
        Schema::dropIfExists('campus_students');
        Schema::dropIfExists('campus_domains');
    }
};
