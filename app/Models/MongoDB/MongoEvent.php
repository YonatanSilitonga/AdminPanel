<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents an event stored in MongoDB to be shared with the Go backend.
 * Collection: events
 */
class MongoEvent extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $table = 'events';
    protected $collection = 'events';

    protected $primaryKey = '_id';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'location',
        'latitude',
        'longitude',
        'organizer',
        'tags',
        'description',
        'long_description',
        'start_date',
        'end_date',
        'banner_url',
        'is_active',
        'admin_id',
        'schedule',
    ];

    public $timestamps = true;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the destination associated with this event (REMOVED)
     */
}
