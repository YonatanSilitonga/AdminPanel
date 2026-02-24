<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Destination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'long_description',
        'category',
        'latitude',
        'longitude',
        'thumbnail_url',
        'cover_url',
        'rating',
        'admin_id',
        'is_active',
        'is_featured',
        'is_trending',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the admin that created this destination
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the gallery images
     */
    public function galleryImages()
    {
        return $this->hasMany(DestinationGallery::class)->orderBy('order', 'asc');
    }

    /**
     * Get the facilities
     */
    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'destination_facility');
    }

    /**
     * Get reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope active destinations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope featured destinations
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope trending destinations
     */
    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }
}
