<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Review Has Been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-approved',
            with: [
                'review' => $this->review,
                'destination' => $this->review->destination,
                'user' => $this->review->user,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
