<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemErrorNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $errorMessage,
        public string $errorTrace = '',
        public string $requestUrl = ''
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peringatan: Kendala Sistem Terdeteksi di Wisata Toba',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.system-error',
            with: [
                'errorMessage' => $this->errorMessage,
                'errorTrace' => $this->errorTrace,
                'requestUrl' => $this->requestUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
