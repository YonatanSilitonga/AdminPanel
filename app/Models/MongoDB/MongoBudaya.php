<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a Culture & Heritage item stored in MongoDB.
 * Collection: budaya
 */
class MongoBudaya extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $table = 'budaya';

    protected $primaryKey = '_id';

    protected $fillable = [
        'name',
        'category',
        'category_mobile',
        'location',
        'latitude',
        'longitude',
        'description',
        'image_url',
        'is_active',
        'admin_id',
    ];

    public $timestamps = true;

    /**
     * Get the admin who created this budaya.
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
