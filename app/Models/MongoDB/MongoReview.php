<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

class MongoReview extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ratings';

    protected $primaryKey = '_id';

    protected $fillable = [
        'destination_id',
        'user_id',
        'rating',
        'review',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'rating' => 'integer',
        'destination_id' => 'string',
        'user_id' => 'string',
    ];

    /**
     * Get the destination associated with this review
     */
    public function destination()
    {
        return $this->belongsTo(MongoDestination::class, 'destination_id', '_id');
    }
}
