<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Represents a tourist destination stored in MongoDB by the Go backend.
 * Collection: destinations
 */
class MongoDestination extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'destinations';
    protected $collection = 'destinations';

    protected $primaryKey = '_id';

    protected $fillable = [
        'name',
        'description',
        'location',
        'images',
        'average_rating',
        'total_reviews',
        'category',
        'latitude',
        'longitude',
        'is_active',
        'is_featured',
    ];

    public $timestamps = true; // Use Laravel timestamps, which will be stored in Mongo

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'latitude'   => 'float',
        'longitude'  => 'float',
        'average_rating' => 'float',
        'total_reviews' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Scope to search by name.
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where('name', 'like', '%' . $keyword . '%');
    }
}
