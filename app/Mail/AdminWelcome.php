<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Admin $admin,
        public string $password = ''
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Smart Tourism Admin Panel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-welcome',
            with: [
                'admin' => $this->admin,
                'password' => $this->password,
                'login_url' => route('admin.login'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
