<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $primaryKey = '_id';

    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if (\App\Models\AppSetting::get('notify_new_user', false)) {
                try {
                    $adminEmail = config('mail.from.address', 'admin@toba.id');
                    \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\NewUserNotification($user));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send new user email notification: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Default attributes for the model.
     */
    protected $attributes = [
        'role' => 'user',
        'is_active' => true,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'role', // Single role: member/standard
        'suspend_category',
        'suspend_reason',
        'suspended_at',
        'suspended_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }
}
