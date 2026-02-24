<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 10;

    public function __construct(
        private string $mailClass,
        private array $mailParams = []
    ) {
    }

    public function handle(): void
    {
        if (!class_exists($this->mailClass)) {
            throw new \Exception("Mail class {$this->mailClass} not found");
        }

        $mail = new $this->mailClass(...$this->mailParams);
        
        if (!$mail instanceof \Illuminate\Mail\Mailable) {
            throw new \Exception("Class {$this->mailClass} must extend Mailable");
        }

        Mail::send($mail);
    }

    public function failed(\Exception $exception): void
    {
        \Illuminate\Support\Facades\Log::error(
            'SendEmailNotification job failed',
            [
                'mail_class' => $this->mailClass,
                'exception' => $exception->getMessage(),
            ]
        );
    }
}
