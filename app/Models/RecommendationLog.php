<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationLog extends Model
{
    protected $table = 'recommendation_logs';
    protected $fillable = [
        'user_id',
        'recommended_destination_id',
        'behavior_data',
        'recommendation_score',
        'is_clicked',
    ];

    protected $casts = [
        'behavior_data' => 'json',
        'recommendation_score' => 'float',
        'is_clicked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $indexes = [
        'user_id',
        'recommended_destination_id',
    ];

    /**
     * Get the user that this recommendation is for
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recommended destination
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'recommended_destination_id');
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, int $userId)
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
     * Scope to filter not clicked recommendations
     */
    public function scopeNotClicked($query)
    {
        return $query->where('is_clicked', false);
    }

    /**
     * Scope to filter by score range
     */
    public function scopeScoreRange($query, float $min, float $max)
    {
        return $query->whereBetween('recommendation_score', [$min, $max]);
    }

    /**
     * Mark recommendation as clicked
     */
    public function markAsClicked(): bool
    {
        return $this->update(['is_clicked' => true]);
    }

    /**
     * Get behavior type from JSON data
     */
    public function getBehaviorTypes(): array
    {
        return array_keys((array) $this->behavior_data);
    }

    /**
     * Get behavior count for a specific type
     */
    public function getBehaviorCount(string $type): int
    {
        return $this->behavior_data[$type] ?? 0;
    }
}
