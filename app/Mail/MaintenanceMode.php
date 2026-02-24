<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceMode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public bool $enabled = true,
        public string $message = ''
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->enabled 
            ? 'Admin Panel - Maintenance Mode Enabled'
            : 'Admin Panel - Maintenance Mode Disabled';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $viewName = $this->enabled 
            ? 'emails.maintenance-enabled'
            : 'emails.maintenance-disabled';

        return new Content(
            view: $viewName,
            with: [
                'message' => $this->message,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
