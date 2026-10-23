<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDirectMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string  $recipientName,
        public string  $mailSubject,
        public string  $messageBody,
        public ?string $ctaLabel = null,
        public ?string $ctaUrl   = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->mailSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-direct');
    }
}
