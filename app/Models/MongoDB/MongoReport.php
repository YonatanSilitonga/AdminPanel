<?php

namespace App\Models\MongoDB;

use MongoDB\Client;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Represents a visitor report stored in MongoDB by the Go backend.
 * Collection: reports
 *
 * Statuses: pending | reviewed | resolved
 */
class MongoReport extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'reports';

    protected $primaryKey = '_id';

    protected $fillable = [
        'destination_id',
        'user_id',
        'image_path',
        'image_url',
        'image_paths',
        'image_urls',
        'description',
        'reason',
        'status',
        'assigned_to',
        'action_taken',
        'action_reason',
    ];

    public $timestamps = false;  // Go backend manages timestamps — do NOT let Laravel overwrite them

    public static function boot()
    {
        parent::boot();

        static::created(function ($report) {
            if (\App\Models\AppSetting::get('notify_new_report', false)) {
                try {
                    $adminEmail = config('mail.from.address', 'admin@toba.id');
                    \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\NewReportNotification($report));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send new report email notification: ' . $e->getMessage());
                }
            }
        });
    }

    protected $casts = [
        'status'         => 'string',
        'destination_id' => 'string',

        'user_id' => 'string',
    ];

    protected $appends = ['all_image_urls', 'image_url', 'reporter_name'];

    /**
     * Get the destination associated with this report
     */
    public function destination()
    {
        return $this->belongsTo(MongoDestination::class, 'destination_id', '_id');
    }

    /**
     * Get the user associated with this report
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', '_id');
    }

    /**
     * Get reporter name attribute
     */
    public function getReporterNameAttribute(): string
    {
        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->name ?? ($this->user->firstName ? $this->user->firstName . ' ' . $this->user->lastName : 'Anonim');
        }
        $user = $this->user;
        return $user ? ($user->name ?? ($user->firstName ? $user->firstName . ' ' . $user->lastName : 'Anonim')) : ($this->user_id ?? 'Anonim');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter pending reports.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter by destination.
     */
    public function scopeForDestination($query, string $destinationId)
    {
        return $query->where('destination_id', $destinationId);
    }

    /**
     * Get the full image URL.
     *
     * Priority:
     * 1. Use image_path (relative path) + current Go backend URL  — most reliable
     * 2. Fall back to stored image_url if no path available
     */
    public function getImageUrlAttribute(?string $value): ?string
    {
        return $this->formatImageUrl($this->image_path ?? $this->attributes['image_url'] ?? $value);
    }

    /**
     * Get all image URLs as an array.
     */
    public function getAllImageUrlsAttribute(): array
    {
        $rawUrls = $this->image_urls ?? [];
        $processedUrls = [];

        foreach ($rawUrls as $url) {
            if ($url) {
                $processedUrls[] = $this->formatImageUrl($url);
            }
        }
        
        // Add the single image_url or image_path if it's not already in the list
        $singleUrl = $this->image_url;
        if (!$singleUrl && $this->image_path) {
            $singleUrl = $this->formatImageUrl($this->image_path);
        }

        if ($singleUrl && !in_array($singleUrl, $processedUrls)) {
            $processedUrls[] = $singleUrl;
        }
        
        return array_unique($processedUrls);
    }

    /**
     * Helper to format image URL consistently.
     */
    private function formatImageUrl(?string $value): ?string
    {
        return image_url($value);
    }

    /**
     * Get raw reports directly using the MongoDB PHP Driver.
     * This bypasses Eloquent and uses the native client as requested.
     */
    public static function getRawReports()
    {
        // Requires the MongoDB PHP Driver
        // https://www.mongodb.com/docs/drivers/php/
        $client = new Client('mongodb+srv://deeny3366_db_user:p1CLeU4YJU48ECRn@cluster0.9gwjswd.mongodb.net/?appName=Cluster0');
        $collection = $client->selectCollection('smarttourism', 'reports');
        
        return $collection->find([]);
    }
}
