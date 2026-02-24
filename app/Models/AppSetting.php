<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'app_settings';
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value, string $type = 'string'): self
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = self::stringifyValue($value, $type);
        $setting->type = $type;
        $setting->save();

        return $setting;
    }

    /**
     * Check if a setting exists
     */
    public static function has(string $key): bool
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Delete a setting
     */
    public static function remove(string $key): bool
    {
        return self::where('key', $key)->delete() > 0;
    }

    /**
     * Get all settings as a key-value array
     * Nama diubah agar tidak bentrok dengan Model::all()
     */
    public static function getAllSettings(): array
    {
        // Gunakan query() agar tidak tertukar dengan static function get() di atas
        return self::query()->get()->reduce(function ($carry, $setting) {
            $carry[$setting->key] = self::castValue($setting->value, $setting->type);
            return $carry;
        }, []);
    }

    /**
     * Cast value based on type
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'json' => json_decode($value, true),
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            default => (string) $value,
        };
    }

    /**
     * Stringify value based on type
     */
    protected static function stringifyValue(mixed $value, string $type): string
    {
        return match ($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            'integer' => (string) (int) $value,
            'float' => (string) (float) $value,
            default => (string) $value,
        };
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
