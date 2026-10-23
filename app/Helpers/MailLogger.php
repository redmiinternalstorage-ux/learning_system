<?php

namespace App\Helpers;

use App\Models\MailLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Central helper to log every outbound mail.
 * Usage:
 *   MailLogger::log('welcome', $user->email, $user->name, $user->id, fn() => Mail::to(...)->send(...));
 */
class MailLogger
{
    /**
     * Send a mail and log the result.
     *
     * @param  string        $mailType     One of MailLog::$typesMeta keys
     * @param  string        $email        Recipient email
     * @param  string|null   $name         Recipient name
     * @param  int|null      $userId       FK to users table (nullable)
     * @param  callable      $sendCallback The actual Mail::to()->send() call
     * @param  string|null   $triggeredBy  Context string
     * @param  array         $extra        Additional fields (e.g. otp_code)
     * @return MailLog
     */
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
        } catch (\Throwable $e) {
            $data['success']       = false;
            $data['error_message'] = $e->getMessage();
            Log::warning("Mail [{$mailType}] to {$email} failed: " . $e->getMessage());
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
