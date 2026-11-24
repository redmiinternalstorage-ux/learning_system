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
    const WEBHOOK_URL = 'https://script.google.com/macros/s/AKfycbz5FgjQ_Q2qGcS2eppev4vCiFlkdH9sQ6F5BFDN8Oo3njXUAYfDDJxpXMfj8loKgbgF/exec';

    /**
     * Send a Laravel Mailable via Google Apps Script.
     * Returns true on success, false on failure.
     */
    public static function send(string $to, Mailable $mailable): bool
    {
        try {
            // Render the mailable to get subject + HTML
            $mailable->mailer('array'); // prevent actual sending
            $rendered = $mailable->render();

            // Get subject from envelope
            $subject  = $mailable->envelope()->subject ?? config('app.name');

            $response = Http::timeout(15)
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
