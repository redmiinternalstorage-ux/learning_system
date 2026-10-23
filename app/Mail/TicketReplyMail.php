<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $ticketSubject,
        public string $replyMessage,
        public int    $ticketId,
        public string $ticketStatus,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '📩 Reply to your ticket: ' . $this->ticketSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ticket-reply');
    }
}
