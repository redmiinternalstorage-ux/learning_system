<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MasterDataSheetMail extends Mailable
{
    use Queueable, SerializesModels;

    // Use protected to prevent SerializesModels from trying to serialize large binary strings
    protected string $pdfBytes;

    public function __construct(
        public readonly mixed $admin,
        public readonly array $masterData,
        string $pdfBytes
    ) {
        $this->pdfBytes = $pdfBytes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Master Finance & Complete System Data Sheet — Learning Curve Admin',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.master-sheet-notification',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->pdfBytes,
                'master-finance-and-system-sheet-' . now()->format('Y-m-d') . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
