<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Fallback mail used when SMTP is unavailable.
 * Gets sent via the 'log' driver — content saved to storage/logs/laravel.log
 * App never shows error to user when this is used.
 */
class FallbackLogMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string  $mailType,
        public string  $recipientEmail,
        public ?string $recipientName = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[SMTP Fallback] Mail logged: ' . $this->mailType,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fallback-log',
        );
    }
}
