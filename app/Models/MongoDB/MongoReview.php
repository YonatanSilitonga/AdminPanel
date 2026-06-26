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
        'sentiment_is_uncertain',
        'sentiment_uncertainty_reasons',
    ];

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            if (\App\Models\AppSetting::get('notify_new_review', false)) {
                try {
                    $adminEmail = config('mail.from.address', 'admin@toba.id');
                    \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\NewReviewNotification($review));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send new review email notification: ' . $e->getMessage());
                }
            }
        });
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'rating' => 'integer',
        'destination_id' => 'string',
        'user_id' => 'string',
        'sentiment_confidence' => 'float',
        'sentiment_analyzed_at' => 'datetime',
        'sentiment_is_uncertain' => 'boolean',
        'sentiment_uncertainty_reasons' => 'array',
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
        $array['sentiment_is_uncertain'] = $this->sentiment_is_uncertain ?? false;
        $array['sentiment_uncertainty_reasons'] = $this->sentiment_uncertainty_reasons ?? [];
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
