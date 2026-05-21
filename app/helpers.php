<?php

if (!function_exists('image_url')) {
    /**
     * Return a publicly accessible image URL.
     * - Cloudinary / external URLs are returned as-is.
     * - Local storage paths are wrapped with asset('storage/...').
     *
     * @param  string|null  $path
     * @return string
     */
    function image_url(?string $path): string
    {
        if (!$path) {
            return '';
        }

        // Handle full URLs
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            // Fix for Go Backend local IPs (anything with port :8080)
            if (str_contains($path, ':8080') && !str_contains($path, 'localhost')) {
                $goBackendUrl = rtrim(config('services.go_backend.url', 'http://localhost:8080'), '/');
                $urlParts = parse_url($path);
                $relativePath = $urlParts['path'] ?? '';
                return $goBackendUrl . $relativePath;
            }
            return $path;
        }

        // Fix for relative Go paths (e.g. "uploads\reports\...")
        if (str_starts_with($path, 'uploads') || str_contains($path, 'uploads/')) {
             $goBackendUrl = rtrim(config('services.go_backend.url', 'http://localhost:8080'), '/');
             $normalizedPath = str_replace('\\', '/', $path);
             return $goBackendUrl . '/' . ltrim($normalizedPath, '/');
        }

        // Handle relative paths (Laravel Storage)
        return asset('storage/' . $path);
    }
}

if (!function_exists('app_setting')) {
    /**
     * Get a setting value from AppSetting model.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function app_setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\AppSetting::get($key, $default);
    }
}
