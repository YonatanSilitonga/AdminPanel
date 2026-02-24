<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationGallery extends Model
{
    protected $fillable = [
        'destination_id',
        'image_url',
        'caption',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the destination
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
