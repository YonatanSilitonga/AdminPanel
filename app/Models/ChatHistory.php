<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatHistory extends Model
{
    protected $connection = 'mysql';
    protected $table = 'chat_histories';
    protected $fillable = [
        'user_id',
        'conversation_id',
        'content',
        'role',
        'is_flagged',
        'flag_reason',
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $indexes = [
        'user_id',
        'conversation_id',
        'created_at',
    ];

    /**
     * Get the user that owns this chat history
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by conversation
     */
    public function scopeConversation($query, string $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope to filter by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to filter flagged messages
     */
    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    /**
     * Mark message as flagged
     */
    public function flag(string $reason): bool
    {
        return $this->update([
            'is_flagged' => true,
            'flag_reason' => $reason,
        ]);
    }

    /**
     * Unflag message
     */
    public function unflag(): bool
    {
        return $this->update([
            'is_flagged' => false,
            'flag_reason' => null,
        ]);
    }
}
