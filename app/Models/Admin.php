<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Admin extends Model implements Authenticatable
{
    use SoftDeletes, AuthenticatableTrait;

    protected $table = 'admins';
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
        return $this->role->name === $role;
    }

    /**
     * Check if admin has any of the given roles
     */
    public function hasAnyRole(string ...$roles): bool
    {
        return in_array($this->role->name, $roles);
    }

    /**
     * Check if admin has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role->permissions()->where('name', $permission)->exists();
    }

    /**
     * Check if admin has all of the given permissions
     */
    public function hasAllPermissions(string ...$permissions): bool
    {
        $userPermissions = $this->role->permissions()->pluck('name')->toArray();
        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
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
        return $this->role->name === 'super_admin';
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
        return $this->role->permissions()->pluck('name')->toArray();
    }
}
