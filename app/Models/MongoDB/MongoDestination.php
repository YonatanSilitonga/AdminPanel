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
        'category',
        'latitude',
        'longitude',
        'is_active',
        'facilities',
        'opening_hours',
        'ticket_price',
        'best_time',
        'video_duration',
        'video_autoplay',
        'video_loop',
        'video_wait_until_ready',
        'admin_id',
    ];

    /**
     * Get the admin who created this destination.
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }

    public $timestamps = true; // Use Laravel timestamps, which will be stored in Mongo

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'latitude'   => 'float',
        'longitude'  => 'float',
        'is_active' => 'boolean',
        'video_duration' => 'integer',
        'video_autoplay' => 'boolean',
        'video_loop' => 'boolean',
        'video_wait_until_ready' => 'boolean',
    ];

    /**
     * Get the reviews for this destination
     */
    public function reviews()
    {
        return MongoReview::where('destination_id', (string)$this->_id)->where('status', 'approved');
    }

    /**
     * Get average rating from approved reviews
     */
    public function getAverageRatingAttribute($value)
    {   
        if ($value !== null && $value !== '') {
            return (float) $value;
        }

        $cacheKey = 'destination_avg_rating_' . $this->_id;
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function() {
            $reviews = $this->reviews()->get();
            if ($reviews->isEmpty()) {
                return 0.0;
            }
            return (float) ($reviews->avg('rating') ?? 0.0);
        });
    }

    /**
     * Get total review count from approved reviews
     */
    public function getTotalReviewsAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return (int) $value;
        }

        $cacheKey = 'destination_total_reviews_' . $this->_id;
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function() {
            return (int) $this->reviews()->count();
        });
    }

    /**
     * Scope to search by name.
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where('name', 'like', '%' . $keyword . '%');
    }
}
