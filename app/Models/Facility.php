<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Facility extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'name',
        'icon_url',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get destinations with this facility
     */
    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'destination_facility');
    }
}
