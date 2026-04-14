<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

class CarouselBanner extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'carrousel_banner';

    protected $fillable = [
        'title',
        'subtitle',
        'category_badge',
        'image_url',
        'link_type',
        'start_date',
        'end_date',
        'order',
        'is_active',
        'admin_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
