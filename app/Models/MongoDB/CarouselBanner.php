<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

class CarouselBanner extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'carousel_banners';

    protected $fillable = [
        'title',
        'subtitle',
        'category_badge',
        'image_url',
        'start_date',
        'end_date',
        'order',
        'is_active',
        'admin_id',
    ];

    /**
     * Get the admin who created this banner.
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
