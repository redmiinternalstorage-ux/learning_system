<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserDataReportMail extends Mailable
{
    use Queueable, SerializesModels;

    // Protected to prevent SerializesModels from serializing large PDF binary strings
    protected ?string $pdfBytes;

    public function __construct(
        public readonly User  $student,
        public readonly array $reportData,
        ?string $pdfBytes = null
    ) {
        $this->pdfBytes = $pdfBytes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Your Personal Data & Learning Sheet — Learning Curve',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-data-report',
        );
    }

    public function attachments(): array
    {
        if (!$this->pdfBytes) {
            return [];
        }

        $filename = 'Personal-Data-Sheet-' . str($this->student->name)->slug() . '-' . now()->format('Y-m-d') . '.pdf';

        return [
            Attachment::fromData(
                fn () => $this->pdfBytes,
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
