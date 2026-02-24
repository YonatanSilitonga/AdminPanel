<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Role Model
 * 
 * Roles:
 * - super_admin (Full access)
 * - admin (Manage destinations & events)
 * - moderator (Review & report moderation)
 */
class Role extends Model
{
    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the permissions for the role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Get the admins for the role
     */
    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    /**
     * Add permission to role
     */
    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching($permission);

        return $this;
    }

    /**
     * Remove permission from role
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->firstOrFail();
        }

        $this->permissions()->detach($permission);

        return $this;
    }

    /**
     * Get role name formatted (super_admin -> Super Admin)
     */
    public function getFormattedNameAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->name));
    }
}
