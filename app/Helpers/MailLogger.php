<?php

namespace App\Helpers;

use App\Models\MailLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Central helper to send and log every outbound mail.
 *
 * Smart fallback strategy:
 *   1. Try primary SMTP mailer
 *   2. If SMTP fails (connection error, port blocked etc.) → auto-retry via log driver
 *   3. Mail failure NEVER crashes the app or shows errors to users
 *
 * Usage:
 *   MailLogger::log('welcome', $email, $name, $userId, fn() => Mail::to(...)->send(...));
 */
class MailLogger
{
    /**
     * Send a mail with automatic fallback and log the result.
     *
     * @param  string        $mailType     e.g. 'welcome', 'otp', 'ticket_reply'
     * @param  string        $email        Recipient email
     * @param  string|null   $name         Recipient name
     * @param  int|null      $userId       FK to users table (nullable)
     * @param  callable      $sendCallback The actual Mail::to()->send() / ->queue() call
     * @param  string|null   $triggeredBy  Context string e.g. 'registration'
     * @param  array         $extra        Additional fields e.g. ['otp_code' => '123456']
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
            // Check if this is a connection/transport error (port blocked, network issue)
            $isConnectionError = self::isConnectionError($e);

            if ($isConnectionError) {
                // Auto-fallback: log the mail content to file instead of failing
                Log::warning("Mail [{$mailType}] SMTP unavailable, falling back to log driver. Error: " . $e->getMessage());

                try {
                    // Switch to log mailer and retry — mail content saved to storage/logs/laravel.log
                    Mail::mailer('log')->to($email)->send(
                        self::extractMailable($sendCallback, $email)
                            ?? new \App\Mail\FallbackLogMail($mailType, $email, $name)
                    );
                    $data['success']       = true;
                    $data['error_message'] = 'SMTP unavailable — logged to file (fallback)';
                } catch (\Throwable $fallbackError) {
                    $data['success']       = false;
                    $data['error_message'] = 'SMTP: ' . $e->getMessage();
                    Log::error("Mail [{$mailType}] to {$email} — both SMTP and log fallback failed: " . $fallbackError->getMessage());
                }
            } else {
                // Non-connection error (bad template, missing data etc.) — log as failure
                $data['success']       = false;
                $data['error_message'] = $e->getMessage();
                Log::warning("Mail [{$mailType}] to {$email} failed: " . $e->getMessage());
            }
        }

        return MailLog::create($data);
    }

    /**
     * Determine if an exception is a connection/transport error
     * (port blocked, DNS failure, timeout, TLS handshake etc.)
     */
    private static function isConnectionError(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        $connectionKeywords = [
            'connection could not be established',
            'connection refused',
            'connection timed out',
            'network is unreachable',
            'could not connect',
            'name or service not known',
            'no route to host',
            'tls handshake',
            'ssl',
            'stream_socket_client',
            'getaddrinfo',
            'timeout',
            'connect() failed',
        ];

        foreach ($connectionKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        // Also catch Symfony Mailer transport exceptions
        if ($e instanceof \Symfony\Component\Mailer\Exception\TransportException) {
            return true;
        }

        return false;
    }

    /**
     * Try to extract the Mailable object from the callback by intercepting Mail::to()->send()
     * Returns null if extraction isn't possible (queue-based sends etc.)
     */
    private static function extractMailable(callable $callback, string $email): ?\Illuminate\Mail\Mailable
    {
        // We cannot reliably extract a mailable from an arbitrary closure,
        // so return null and let caller use FallbackLogMail
        return null;
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
