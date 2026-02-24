<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AdminActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupOldLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 60;

    public function __construct(
        private int $retentionDays = 90
    ) {
    }

    public function handle(): void
    {
        try {
            $cutoffDate = Carbon::now()->subDays($this->retentionDays);
            
            $deleted = AdminActivityLog::where('created_at', '<', $cutoffDate)->delete();
            
            Log::info(
                "Cleanup old logs completed",
                [
                    'retention_days' => $this->retentionDays,
                    'deleted_records' => $deleted,
                ]
            );
        } catch (\Exception $e) {
            Log::error("Error cleaning up old logs: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Exception $exception): void
    {
        Log::error(
            'CleanupOldLogs job failed',
            [
                'exception' => $exception->getMessage(),
            ]
        );
    }
}
