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

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . $path);
    }
}
