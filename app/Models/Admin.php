<?php

declare(strict_types=1);

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use SoftDeletes, Notifiable, HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'admins';
    protected $primaryKey = '_id';

    protected $guarded = [];
    protected $hidden = ['password'];
    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the admin's role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the admin's activity logs
     */
    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class);
    }

    /**
     * Check if admin has a specific role
     */
    public function hasRole(string $role): bool
    {
        $cacheKey = 'admin_role_' . $this->_id;
        $roleName = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(5), function() {
            return $this->role?->name;
        });
        return $roleName === $role;
    }

    /**
     * Check if admin has any of the given roles
     */
    public function hasAnyRole(string ...$roles): bool
    {
        $cacheKey = 'admin_role_' . $this->_id;
        $roleName = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(5), function() {
            return $this->role?->name;
        });
        return $roleName && in_array($roleName, $roles, true);
    }

    /**
     * Check if admin has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $cacheKey = 'admin_permissions_' . $this->_id;
        $userPermissions = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(5), function() {
            if (!$this->role) return [];
            return $this->role->permissions()->pluck('name')->toArray();
        });
        return in_array($permission, $userPermissions, true);
    }

    /**
     * Check if admin has all of the given permissions
     */
    public function hasAllPermissions(string ...$permissions): bool
    {
        $cacheKey = 'admin_permissions_' . $this->_id;
        $userPermissions = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(5), function() {
            if (!$this->role) return [];
            return $this->role->permissions()->pluck('name')->toArray();
        });
        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if admin is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if admin is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Authenticate admin
     */
    public static function authenticate(string $email, string $password): ?self
    {
        $admin = self::where('email', $email)->first();

        if ($admin && $admin->isActive() && Hash::check($password, $admin->password)) {
            return $admin;
        }

        return null;
    }

    /**
     * Get all permissions for the admin
     */
    public function getPermissions(): array
    {
        $cacheKey = 'admin_permissions_' . $this->_id;
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(5), function() {
            if (!$this->role) return [];
            return $this->role->permissions()->pluck('name')->toArray();
        });
    }
}
