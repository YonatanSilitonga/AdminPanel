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

if (!function_exists('media_is_video')) {
    /**
     * Detect whether a media path or URL points to a video file.
     */
    function media_is_video(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        $normalizedPath = strtolower((string) (parse_url($path, PHP_URL_PATH) ?: $path));

        return (bool) preg_match('/\.(mp4|mov|avi|webm|ogg)(?:$|\?)/i', $normalizedPath);
    }
}

if (!function_exists('video_url_with_time_range')) {
    /**
     * Generate a video URL dengan start_time dan end_time fragment untuk HTML5 video playback.
     * Format: url#t=start,end atau url#t=start (jika hanya start)
     * 
     * @param  string|null  $videoPath
     * @param  int|null     $startTime (dalam detik)
     * @param  int|null     $endTime (dalam detik)
     * @return string
     */
    function video_url_with_time_range(?string $videoPath, ?int $startTime = 0, ?int $endTime = null): string
    {
        $url = image_url($videoPath);
        
        if ($startTime && $startTime > 0) {
            if ($endTime && $endTime > $startTime) {
                $url .= '#t=' . (int)$startTime . ',' . (int)$endTime;
            } else {
                $url .= '#t=' . (int)$startTime;
            }
        } elseif ($endTime && $endTime > 0) {
            $url .= '#t=0,' . (int)$endTime;
        }
        
        return $url;
    }
}

if (!function_exists('video_url_with_start_time')) {
    /**
     * Generate a video URL with start_time fragment for HTML5 video playback.
     * Uses the #t=seconds format for HTML5 videos
     * 
     * @param  string|null  $videoPath
     * @param  int|null     $startTime (in seconds)
     * @return string
     */
    function video_url_with_start_time(?string $videoPath, ?int $startTime = 0): string
    {
        $url = image_url($videoPath);
        
        if ($startTime && $startTime > 0) {
            $url .= '#t=' . (int)$startTime;
        }
        
        return $url;
    }
}

if (!function_exists('format_time')) {
    /**
     * Convert seconds to MM:SS format
     * 
     * @param  int|float  $seconds
     * @return string
     */
    function format_time($seconds): string
    {
        if (!$seconds || $seconds < 0) {
            return '00:00';
        }
        
        $mins = floor($seconds / 60);
        $secs = floor($seconds % 60);
        
        return sprintf('%02d:%02d', $mins, $secs);
    }
}

if (!function_exists('get_media_info')) {
    /**
     * Extract media information from image/video entry
     * 
     * @param  array|string  $imageData
     * @return array
     */
    function get_media_info($imageData): array
    {
        // Handle both string and array formats
        if (is_string($imageData)) {
            return [
                'url' => image_url($imageData),
                'type' => media_is_video($imageData) ? 'video' : 'image',
                'start_time' => 0,
                'end_time' => null,
            ];
        }
        
        if (is_array($imageData)) {
            return [
                'url' => image_url($imageData['url'] ?? ''),
                'type' => $imageData['type'] ?? 'image',
                'start_time' => $imageData['start_time'] ?? 0,
                'end_time' => $imageData['end_time'] ?? null,
            ];
        }
        
        return [
            'url' => '',
            'type' => 'image',
            'start_time' => 0,
            'end_time' => null,
        ];
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
