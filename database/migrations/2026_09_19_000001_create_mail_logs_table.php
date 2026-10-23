<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            // Mail type: welcome, otp, otp_resend, password_changed, account_blocked,
            //            account_unblocked, account_deleted, auto_password_changed
            $table->string('mail_type', 50);
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            // Optional: link to the user if they still exist
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Did the mail send successfully?
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            // For OTP mails: was the OTP successfully verified?
            $table->string('otp_code', 6)->nullable();
            $table->boolean('otp_verified')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            // Context: what triggered the send (e.g. 'registration', 'admin_action', 'forgot_password')
            $table->string('triggered_by', 50)->nullable();
            $table->timestamps();

            $table->index('mail_type');
            $table->index('recipient_email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
