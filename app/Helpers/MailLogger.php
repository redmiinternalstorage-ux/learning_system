<?php

namespace App\Helpers;

use App\Models\MailLog;
use Illuminate\Support\Facades\Log;

/**
 * Central helper to send and log every outbound mail.
 *
 * Strategy:
 *   - Try to send via configured mailer (resend / smtp)
 *   - If it fails, log as FAILED with real error message
 *   - Never silently swallow failures and report false success
 *
 * Usage:
 *   MailLogger::log('welcome', $email, $name, $userId, fn() => Mail::to(...)->send(...));
 */
class MailLogger
{
    public static function log(
        string   $mailType,
        string   $email,
        ?string  $name,
        ?int     $userId,
        callable $sendCallback,
        ?string  $triggeredBy = null,
        array    $extra = []
    ): MailLog {
        $data = array_merge([
            'mail_type'       => $mailType,
            'recipient_email' => $email,
            'recipient_name'  => $name,
            'user_id'         => $userId,
            'triggered_by'    => $triggeredBy,
            'success'         => true,
            'error_message'   => null,
        ], $extra);

        try {
            $sendCallback();
            // Only reaches here if send actually succeeded
            $data['success']       = true;
            $data['error_message'] = null;
        } catch (\Throwable $e) {
            // Real failure — log it and mark as failed
            $data['success']       = false;
            $data['error_message'] = $e->getMessage();
            Log::error("Mail [{$mailType}] to {$email} FAILED: " . $e->getMessage());
        }

        return MailLog::create($data);
    }

    /**
     * Mark an existing OTP log entry as verified.
     */
    public static function markOtpVerified(string $email): void
    {
        MailLog::where('recipient_email', $email)
            ->whereIn('mail_type', ['otp', 'otp_resend'])
            ->where('otp_verified', false)
            ->latest()
            ->limit(1)
            ->update([
                'otp_verified'    => true,
                'otp_verified_at' => now(),
            ]);
    }
}
