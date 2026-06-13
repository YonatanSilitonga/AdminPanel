<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

class MongoReview extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'ratings';

    protected $primaryKey = '_id';

    protected $fillable = [
        'destination_id',
        'user_id',
        'rating',
        'review',
        'status',
        'approved_by',
        'reason',
        'sentiment_label',
        'sentiment_confidence',
        'sentiment_scores',
        'sentiment_reason',
        'sentiment_model_version',
        'sentiment_analyzed_at',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'rating' => 'integer',
        'destination_id' => 'string',
        'user_id' => 'string',
        'sentiment_confidence' => 'float',
        'sentiment_analyzed_at' => 'datetime',
    ];

    /**
     * Get the destination associated with this review
     */
    public function destination()
    {
        return $this->belongsTo(MongoDestination::class, 'destination_id', '_id');
    }

    /**
     * Get the user associated with this review
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', '_id');
    }

    /**
     * Get reviewer name attribute
     */
    public function getReviewerNameAttribute(): string
    {
        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->name ?? ($this->user->firstName ? $this->user->firstName . ' ' . $this->user->lastName : 'Anonim');
        }
        $user = $this->user;
        return $user ? ($user->name ?? ($user->firstName ? $user->firstName . ' ' . $user->lastName : 'Anonim')) : ($this->user_id ?? 'Anonim');
    }

    /**
     * Override toArray to always include sentiment fields even if null
     */
    public function toArray()
    {
        $array = parent::toArray();

        // Ensure sentiment fields are always present in JSON response
        $array['sentiment_label'] = $this->sentiment_label ?? null;
        $array['sentiment_confidence'] = $this->sentiment_confidence ?? null;
        $array['sentiment_reason'] = $this->sentiment_reason ?? null;
        $array['sentiment_scores'] = $this->sentiment_scores ?? null;
        $array['sentiment_analyzed_at'] = $this->sentiment_analyzed_at ?? null;
        $array['sentiment_model_version'] = $this->sentiment_model_version ?? null;
        $array['reviewer_name'] = $this->reviewer_name;

        // Include destination relationship
        if ($this->relationLoaded('destination') && $this->destination) {
            $array['destination'] = [
                '_id' => $this->destination->_id ?? null,
                'name' => $this->destination->name ?? null,
            ];
        }

        return $array;
    }
}
