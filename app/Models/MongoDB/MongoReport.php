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
        'status',
        'assigned_to',
        'action_taken',
        'action_reason',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => 'string',
        'destination_id' => 'string',
        'user_id' => 'string',
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
     */
    public function getImageUrlAttribute(?string $value): ?string
    {
        if (!$value) return null;
        // The Go backend serves images from /uploads
        $goBackendUrl = config('services.go_backend.url', 'http://localhost:8080');
        return rtrim($goBackendUrl, '/') . '/uploads/reports/' . basename($value);
    }
}
