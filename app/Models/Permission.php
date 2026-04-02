<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Permission extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'permissions';
    protected $primaryKey = '_id';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, null, 'permission_ids', 'role_ids');
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}
