<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountUnblockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $userEmail
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Your Learning Curve Account Has Been Restored',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-unblocked',
        );
    }
}
