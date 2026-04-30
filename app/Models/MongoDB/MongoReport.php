<?php

namespace App\Models\MongoDB;

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
    protected $collection = 'reports';

    protected $primaryKey = '_id';

    protected $fillable = [
        'destination_id',
        'user_id',
        'image_path',
        'image_url',
        'description',
        'reason',
        'status',
        'assigned_to',
        'action_taken',
        'action_reason',
    ];

    public $timestamps = false;  // Go backend manages timestamps — do NOT let Laravel overwrite them

    protected $casts = [
        'status'         => 'string',
        'destination_id' => 'string',
        'user_id'        => 'string',
        'reason'         => 'string',
    ];

    /**
     * Get the destination associated with this report
     */
    public function destination()
    {
        return $this->belongsTo(MongoDestination::class, 'destination_id', '_id');
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
        $goBackendUrl = rtrim(config('services.go_backend.url', 'http://localhost:8080'), '/');

        // Prefer image_path (relative) — avoids stale IPs stored in image_url
        $imagePath = $this->attributes['image_path'] ?? null;
        if ($imagePath) {
            // Normalise Windows backslashes to forward slashes
            $relativePath = ltrim(str_replace('\\', '/', $imagePath), '/');
            return $goBackendUrl . '/' . $relativePath;
        }

        // Fallback: use stored image_url
        if (!$value) return null;
        if (str_starts_with($value, 'http')) {
            // Replace any stored IP/host with current Go backend URL
            $parsed   = parse_url($value);
            $filePart = $parsed['path'] ?? '';
            return $goBackendUrl . $filePart;
        }

        return $goBackendUrl . '/' . ltrim(str_replace('\\', '/', $value), '/');
    }
}
