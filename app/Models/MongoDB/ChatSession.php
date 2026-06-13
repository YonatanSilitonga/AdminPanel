<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Represents a chat session stored in MongoDB by the Go backend.
 * Collection: chat_sessions
 *
 * @property string $_id         ObjectID
 * @property string $user_id     User's ObjectID (hex string), nullable for guests
 * @property array  $messages    Array of { role, content, timestamp }
 * @property string $updated_at
 */
class ChatSession extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'chat_sessions';

    protected $primaryKey = '_id';

    public $timestamps = false; // Go backend manages timestamps

    protected $fillable = [
        'is_flagged',
        'flag_reason',
        'flagged_at',
        'flagged_by',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'is_flagged' => 'boolean',
        'flagged_at' => 'datetime',
    ];

    /**
     * Get messages formatted for display.
     */
    public function getMessages(): array
    {
        return $this->messages ?? [];
    }

    /**
     * Returns count of messages.
     */
    public function getMessageCountAttribute(): int
    {
        return count($this->messages ?? []);
    }

    /**
     * Scope to filter sessions by user ID.
     */
    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter guest sessions (no user_id).
     */
    public function scopeGuests($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope to filter only flagged sessions.
     */
    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    /**
     * Check if this session is flagged.
     */
    public function isFlagged(): bool
    {
        return $this->is_flagged === true;
    }

    /**
     * Get the user that owns the chat session.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', '_id');
    }
}
