<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffAccountNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $staffName,
        public readonly string $actionType, // 'updated', 'blocked', 'unblocked', 'deleted'
        public readonly string $noticeTitle,
        public readonly string $noticeMessage,
        public readonly ?array $details = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📌 Staff Account Notice: ' . $this->noticeTitle . ' | Learning Curve',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-notice',
        );
    }
}
