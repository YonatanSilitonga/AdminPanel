<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

class MongoBeritaPromosi extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'berita_promosi';
    protected $collection = 'berita_promosi';
    protected $primaryKey = '_id';

    protected $fillable = [
        'judul',
        'tipe',
        'thumbnail',
        'images',
        'videos',
        'konten',
        'tanggal_tayang',
        'tampilkan_di_carousel',
        'is_active',
        'admin_id',
    ];

    protected $casts = [
        'tanggal_tayang' => 'datetime',
        'tampilkan_di_carousel' => 'boolean',
        'is_active' => 'boolean',
        'images' => 'array',
        'videos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }
}
