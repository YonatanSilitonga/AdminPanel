<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MongoBeritaPromosi extends Model
{
    use SoftDeletes;
    protected $connection = 'mongodb';
    protected $table = 'berita_promosi';

    protected $fillable = [
        'judul',
        'tipe',
        'thumbnail',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }
}
