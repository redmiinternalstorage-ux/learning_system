<?php

namespace App\Helpers;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

/**
 * Sends mail via Google Apps Script (GmailApp) — no SMTP needed.
 * Works perfectly on Render free tier since it uses HTTPS (port 443).
 */
class GoogleMailHelper
{
    const WEBHOOK_URL = 'https://script.google.com/macros/s/AKfycbw6NJCSuJKD5E9b__hUpE4tnrKCO2EclR66WSe54JGS0DVwrKs1mOnZndgh-bUWqe2B/exec';

    /**
     * Send a Laravel Mailable via Google Apps Script.
     * Returns true on success, false on failure.
     */
    public static function send(string $to, Mailable $mailable): bool
    {
        try {
            // Set mailer to 'array' to prevent actual sending during render
            $mailable->mailer('array');

            // Render HTML — wrap in try/catch in case view fails
            try {
                $rendered = $mailable->render();
            } catch (\Throwable $re) {
                // Fallback: build simple HTML if blade render fails
                $rendered = '<p>Mail from Learning Curve</p><p>' . $to . '</p>';
                Log::warning("GoogleMail render failed for {$to}: " . $re->getMessage());
            }

            // Get subject
            try {
                $subject = $mailable->envelope()->subject ?? config('app.name', 'Learning Curve');
            } catch (\Throwable $se) {
                $subject = config('app.name', 'Learning Curve');
            }

            $response = Http::timeout(20)
                ->withoutRedirecting()
                ->get(self::WEBHOOK_URL, [
                    'job'       => 'mail',
                    'to'        => $to,
                    'subject'   => $subject,
                    'html'      => $rendered,
                    'from_name' => config('mail.from.name', 'Learning Curve'),
                ]);

            $result = $response->json();

            if (!empty($result['success'])) {
                return true;
            }

            Log::warning("GoogleMail failed to {$to}: " . ($result['error'] ?? 'unknown'));
            return false;

        } catch (\Throwable $e) {
            Log::error("GoogleMail exception to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send raw HTML mail directly (no Mailable needed).
     */
    public static function sendRaw(string $to, string $subject, string $html): bool
    {
        try {
            $response = Http::timeout(15)
                ->withoutRedirecting()
                ->get(self::WEBHOOK_URL, [
                    'job'       => 'mail',
                    'to'        => $to,
                    'subject'   => $subject,
                    'html'      => $html,
                    'from_name' => config('mail.from.name', 'Learning Curve'),
                ]);

            $result = $response->json();
            return !empty($result['success']);

        } catch (\Throwable $e) {
            Log::error("GoogleMail sendRaw exception to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
