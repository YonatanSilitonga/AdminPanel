<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a public facility stored in MongoDB.
 * Collection: fasilitas_umum
 */
class MongoFasilitasUmum extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $table = 'fasilitas_umum';

    protected $primaryKey = '_id';

    protected $fillable = [
        'name',
        'type',
        'address',
        'latitude',
        'longitude',
        'phone_number',
        'description',
        'available_services',
        'tags',
        'operational_hours',
        'is_active',
        'image_url',
        'images',
        'admin_id',
    ];

    /**
     * Get the admin who created this facility.
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }

    public $timestamps = true;

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
