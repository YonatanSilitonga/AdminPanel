<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\MongoDB\MongoReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReportNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MongoReport $report
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi: Laporan Baru Wisata Toba',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-report',
            with: [
                'report' => $this->report,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
