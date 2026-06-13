<?php

namespace App\Models;

// use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class DestinationGallery extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'destination_galleries';

    protected $fillable = [
        'destination_id',
        'image_url', // will be file_url
        'caption',
        'order',
        'media_type', // 'image' or 'video'
        'start_time', // integer in seconds
    ];

    protected $casts = [
        'order' => 'integer',
        'start_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the destination
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
