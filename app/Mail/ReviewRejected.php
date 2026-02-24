<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Review $review,
        public string $reason = ''
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Review Has Been Rejected',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-rejected',
            with: [
                'review' => $this->review,
                'destination' => $this->review->destination,
                'user' => $this->review->user,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
