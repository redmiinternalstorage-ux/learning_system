<?php

namespace App\Helpers;

use App\Models\MailLog;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

/**
 * Central mail sender + logger.
 *
 * PRIMARY:  Google Apps Script (GmailApp) via HTTPS — works on Render free tier
 * FALLBACK: Laravel Mail (SMTP/Resend) if Apps Script fails
 */
class MailLogger
{
    public static function log(
        string   $mailType,
        string   $email,
        ?string  $name,
        ?int     $userId,
        callable $mailableOrCallback,
        ?string  $triggeredBy = null,
        array    $extra = []
    ): ?MailLog {
        $data = array_merge([
            'mail_type'       => $mailType,
            'recipient_email' => $email,
            'recipient_name'  => $name,
            'user_id'         => $userId,
            'triggered_by'    => $triggeredBy,
            'success'         => false,
            'error_message'   => null,
        ], $extra);

        try {
            $result = $mailableOrCallback();

            if ($result instanceof Mailable) {
                $sent = GoogleMailHelper::send($email, $result);

                if ($sent) {
                    $data['success']       = true;
                    $data['error_message'] = null;
                } else {
                    $data = self::tryLaravelFallback($data, $email, $result);
                }
            } else {
                $data['success']       = true;
                $data['error_message'] = null;
            }

        } catch (\Throwable $e) {
            $data['success']       = false;
            $data['error_message'] = $e->getMessage();
            Log::error("Mail [{$mailType}] to {$email} FAILED: " . $e->getMessage());
        }

        // Save to DB — wrapped in try-catch so a DB failure doesn't crash the page
        try {
            return MailLog::create($data);
        } catch (\Throwable $e) {
            Log::warning("MailLogger DB save failed [{$mailType}]: " . $e->getMessage());
            return null;
        }
    }

    private static function tryLaravelFallback(array $data, string $email, Mailable $mailable): array
    {
        try {
            \Illuminate\Support\Facades\Mail::to($email)->send($mailable);
            $data['success']       = true;
            $data['error_message'] = 'sent via laravel fallback';
        } catch (\Throwable $fe) {
            $data['success']       = false;
            $data['error_message'] = 'GoogleScript: failed | Laravel: ' . $fe->getMessage();
            Log::error("Mail fallback also failed to {$email}: " . $fe->getMessage());
        }
        return $data;
    }

    public static function markOtpVerified(string $email): void
    {
        try {
            MailLog::where('recipient_email', $email)
                ->whereIn('mail_type', ['otp', 'otp_resend'])
                ->where('otp_verified', false)
                ->latest()
                ->limit(1)
                ->update([
                    'otp_verified'    => true,
                    'otp_verified_at' => now(),
                ]);
        } catch (\Throwable $e) {
            Log::warning("MailLogger markOtpVerified failed: " . $e->getMessage());
        }
    }
}
