<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportResolved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Report $report)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Report Has Been Resolved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-resolved',
            with: [
                'report' => $this->report,
                'user' => $this->report->user,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
