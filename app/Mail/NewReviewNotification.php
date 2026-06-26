<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\MongoDB\MongoReview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReviewNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MongoReview $review
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi: Ulasan Baru Wisata Toba',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-review',
            with: [
                'review' => $this->review,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
