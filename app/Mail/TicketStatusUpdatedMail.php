<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $ticketSubject,
        public int    $ticketId,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📌 Ticket #' . $this->ticketId . ' status updated to ' . ucfirst($this->newStatus)
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ticket-status-updated');
    }
}
