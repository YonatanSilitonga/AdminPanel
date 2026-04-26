<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessAdminActivityLog implements ShouldQueue
{
    use Queueable;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $oldValues = $this->data['old_values'] ?? null;
        $newValues = $this->data['new_values'] ?? null;
        $changes = null;

        if ($oldValues && $newValues) {
            $changes = [];
            foreach ($newValues as $key => $value) {
                if (isset($oldValues[$key]) && $oldValues[$key] != $value) {
                    $changes[$key] = [
                        'old' => $oldValues[$key],
                        'new' => $value,
                    ];
                } elseif (!isset($oldValues[$key])) {
                    $changes[$key] = [
                        'old' => null,
                        'new' => $value,
                    ];
                }
            }
        }

        $logData = $this->data;
        $logData['changes'] = $changes ?: null;
        $logData['status'] = 'success';

        \App\Models\AdminActivityLog::create($logData);
    }
}
