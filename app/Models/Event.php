<?php

declare(strict_types=1);

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MongoDB\MongoDestination;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';

    protected $fillable = [
        'destination_id',
        'name',
        'slug',
        'description',
        'long_description',
        'start_date',
        'end_date',
        'banner_url',
        'is_active',
        'admin_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the destination this event belongs to (from MongoDB)
     */
    public function getDestinationAttribute()
    {
        if (!$this->destination_id) {
            return null;
        }
        return MongoDestination::find($this->destination_id);
    }

    /**
     * Get the admin who created this event
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
