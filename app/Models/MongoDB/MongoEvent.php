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
        'organizer',
        'tags',
        'description',
        'long_description',
        'start_date',
        'end_date',
        'banner_url',
        'admin_id',
        'schedule',
        'opening_hours',
        'ticket_price',
        'best_time',
    ];

    public $timestamps = true;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the admin who created this event.
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }

    /**
     * Get the destination associated with this event (REMOVED)
     */
}
