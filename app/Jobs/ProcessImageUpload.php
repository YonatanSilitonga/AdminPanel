<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        private string $filePath,
        private array $sizes = ['thumbnail' => [150, 150], 'medium' => [500, 500], 'large' => [1200, 1200]]
    ) {
    }

    public function handle(): void
    {
        try {
            if (!Storage::disk('public')->exists($this->filePath)) {
                throw new \Exception("File not found: {$this->filePath}");
            }

            // TODO: Implement image resizing with intervention/image
            // Example:
            // $image = \Image::make(Storage::disk('public')->path($this->filePath));
            // foreach ($this->sizes as $name => $size) {
            //     $image->resize($size[0], $size[1], function ($constraint) {
            //         $constraint->aspectRatio();
            //         $constraint->upsize();
            //     })->save(...);
            // }

            Log::info("Image processed: {$this->filePath}");
        } catch (\Exception $e) {
            Log::error("Error processing image: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Exception $exception): void
    {
        Log::error(
            'ProcessImageUpload job failed',
            [
                'file_path' => $this->filePath,
                'exception' => $exception->getMessage(),
            ]
        );
    }
}
