<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $student,
        public readonly Course $course,
        public readonly string $completedDate,
        public readonly string $certificateUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎓 Congratulations! Your Certificate is Ready — ' . $this->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.course-certificate',
        );
    }
}
