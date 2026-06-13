<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Represents a recommendation log stored in MongoDB
 * Collection: recommendation_logs
 *
 * @property string $_id ObjectID
 * @property string $user_id User's ObjectID (nullable for guests)
 * @property string $destination_id Recommended destination ID
 * @property float $recommendation_score Score/duration of recommendation
 * @property bool $is_clicked Whether recommendation was clicked
 * @property array $behavior_data User behavior metadata
 * @property \DateTime $created_at Timestamp when record created
 * @property \DateTime $updated_at Timestamp when record updated
 */
class MongoRecommendation extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'recommendation_logs';
    protected $collection = 'recommendation_logs';

    protected $primaryKey = '_id';

    protected $fillable = [
        'user_id',
        'destination_id',
        'recommendation_score',
        'is_clicked',
        'behavior_data',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'recommendation_score' => 'float',
        'is_clicked' => 'boolean',
        'behavior_data' => 'array',
    ];

    /**
     * Get the destination this recommendation refers to
     */
    public function destination()
    {
        return $this->belongsTo(MongoDestination::class, 'destination_id', '_id');
    }

    /**
     * Get the user this recommendation refers to
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', '_id');
    }

    /**
     * Scope to filter by user ID
     */
    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter clicked recommendations
     */
    public function scopeClicked($query)
    {
        return $query->where('is_clicked', true);
    }

    /**
     * Get formatted destination name
     */
    public function getDestinationNameAttribute()
    {
        return $this->destination()?->name ?? 'Unknown Destination';
    }
}
