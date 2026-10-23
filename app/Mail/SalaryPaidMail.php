<?php

namespace App\Mail;

use App\Models\SalaryPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalaryPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $staffName,
        public readonly string $period,
        public readonly string $amount,
        public readonly string $currency,
        public readonly int    $hoursWorked,
        public readonly string $paidBy,
        public readonly string $notes = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '🎉 Salary Paid — ' . $this->period . ' | Learning Curve');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.salary-paid');
    }
}
