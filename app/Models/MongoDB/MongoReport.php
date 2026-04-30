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
