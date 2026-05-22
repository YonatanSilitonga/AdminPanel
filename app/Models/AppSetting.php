<?php

declare(strict_types=1);

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AppSetting extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'app_settings';
    protected $primaryKey = '_id';
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
        $cacheKey = 'app_settings_' . $key;
        $setting = \Illuminate\Support\Facades\Cache::rememberForever($cacheKey, function () use ($key) {
            return self::where('key', $key)->first();
        });

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

        \Illuminate\Support\Facades\Cache::forget('app_settings_' . $key);

        return $setting;
    }

    /**
     * Check if a setting exists
     */
    public static function has(string $key): bool
    {
        $cacheKey = 'app_settings_' . $key;
        $setting = \Illuminate\Support\Facades\Cache::rememberForever($cacheKey, function () use ($key) {
            return self::where('key', $key)->first();
        });
        return $setting !== null;
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
            'json' => self::castJsonValue($value),
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            default => (string) $value,
        };
    }

    /**
     * Cast JSON safely whether value is string, array, object, or null.
     */
    protected static function castJsonValue(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
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
