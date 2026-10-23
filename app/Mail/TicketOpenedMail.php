<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketOpenedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $ticketSubject,
        public string $ticketMessage,
        public int    $ticketId,
        public string $category,
        public string $priority,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '✅ Support Ticket #' . $this->ticketId . ' Received — Learning Curve');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ticket-opened');
    }
}
