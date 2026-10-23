<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FinanceSheetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $adminName,
        public readonly array  $data,
        public readonly string $pdfBytes,
        public readonly string $triggeredBy = 'Finance Department',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🏦 Finance Data Vault Export — Learning Curve | ' . now()->format('d M Y')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.finance-sheet-mail');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBytes, 'finance-vault-' . now()->format('Y-m-d') . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
