<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BaseAdminController extends Controller
{
    /**
     * Current authenticated admin
     * 
     * @var \App\Models\Admin|null
     */
    protected $admin;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth('admin')->check()) {
                /** @var \App\Models\Admin $admin */
                $admin = auth('admin')->user();
                $this->admin = $admin;
            }
            return $next($request);
        });
    }

    /**
     * Log activity
     *
     * @param string $action
     * @param string $entityType
     * @param mixed $entityId
     * @param array|null $oldValues
     * @param array|null $newValues
     */
    protected function logActivity(string $action, string $entityType, $entityId, ?array $oldValues = null, ?array $newValues = null): void
    {
        if ($this->admin) {
            $logData = [
                'admin_id' => $this->admin->id,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ];

            \App\Jobs\ProcessAdminActivityLog::dispatch($logData)->afterResponse();
        }
    }

    /**
     * Check if user has permission
     */
    protected function checkPermission(string $permission): bool
    {
        if (!$this->admin || !$this->admin->hasPermission($permission)) {
            abort(403, 'Unauthorized');
        }

        return true;
    }

    /**
     * Get paginated items
     */
    protected function paginate($query, $perPage = 15)
    {
        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Upload and store file.
     * Uses Cloudinary when CLOUDINARY_CLOUD_NAME is set, otherwise falls back to local public disk.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $path   Folder/prefix used for both Cloudinary and local storage
     * @param  array   $options
     * @return string|null  Cloudinary secure URL  –or–  local relative path
     */
    protected function uploadFile($file, $path = 'uploads', $options = [])
    {
        if (!$file || !$file->isValid()) {
            \Illuminate\Support\Facades\Log::warning('Invalid file upload attempt', [
                'has_file' => (bool)$file,
                'is_valid' => $file ? $file->isValid() : false,
                'error' => $file ? $file->getError() : 'no file',
            ]);
            return null;
        }

        // Validate size
        $maxSize = $options['max_size'] ?? 10; // MB
        if ($file->getSize() > $maxSize * 1024 * 1024) {
            throw new \Exception("File size exceeds {$maxSize}MB limit");
        }

        // Validate mime
        $allowedMimes = $options['mimes'] ?? ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('File type not allowed: ' . $file->getMimeType());
        }

        // ── Cloudinary ──────────────────────────────────────────────────────
        // Use config() instead of env() for reliability
        $cloudName = config('cloudinary.cloud_url') ?: env('CLOUDINARY_CLOUD_NAME');
        
        if ($cloudName) {
            try {
                // Use the simpler upload() method which is more robust in this package
                $result = \cloudinary()->upload($file->getRealPath(), [
                    'folder'        => 'smarttourism/' . trim($path, '/'),
                    'resource_type' => 'image',
                    'quality'       => 'auto',
                    'fetch_format'  => 'auto',
                ]);

                return $result->getSecureUrl();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Cloudinary upload failed: ' . $e->getMessage(), [
                    'file' => $file->getClientOriginalName(),
                    'path' => $path
                ]);
                // Fall back to local if Cloudinary fails
            }
        }

        // ── Local fallback ───────────────────────────────────────────────────
        try {
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs($path, $filename, 'public');
            
            if (!$storedPath) {
                 \Illuminate\Support\Facades\Log::error('Local storage failed for file', ['path' => $path]);
                 return null;
            }
            
            return $storedPath;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Local storage exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete file from storage.
     * Handles both Cloudinary URLs (https://res.cloudinary.com/…) and local paths.
     */
    protected function deleteFile($filePath)
    {
        if (!$filePath) {
            return;
        }

        // ── Cloudinary ──────────────────────────────────────────────────────
        if (str_starts_with($filePath, 'https://res.cloudinary.com/')) {
            try {
                // Extract public_id from URL
                // URL pattern: https://res.cloudinary.com/{cloud}/image/upload/{version}/{public_id}.{ext}
                $parsed   = parse_url($filePath, PHP_URL_PATH);
                $segments = explode('/upload/', $parsed);
                if (isset($segments[1])) {
                    $withVersion = $segments[1];
                    // Strip optional version prefix (v1234567890/)
                    $publicIdWithExt = preg_replace('/^v\d+\//', '', $withVersion);
                    // Remove extension
                    $publicId = preg_replace('/\.[^.]+$/', '', $publicIdWithExt);
                    \cloudinary()->uploadApi()->destroy($publicId);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Cloudinary delete failed: ' . $e->getMessage());
            }
            return;
        }

        // ── Local fallback ───────────────────────────────────────────────────
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    protected function processImage($file, $path, $sizes = [])
    {
        if (!$file) {
            return null;
        }

        // Delegate to uploadFile() so Cloudinary is used when configured
        return $this->uploadFile($file, $path);
    }

    /**
     * Build response with status
     */
    protected function responseSuccess($message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Build error response
     */
    protected function responseError($message, $errors = null, $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Get dashboard summary statistics
     */
    protected function getDashboardStats()
    {
        return [
            'total_destinations' => \App\Models\MongoDB\MongoDestination::count(),
            'total_events'       => \App\Models\MongoDB\MongoEvent::count(),
            'total_users'        => DB::table('users')->where('is_active', true)->count(),
            'pending_reviews'    => \App\Models\MongoDB\MongoReview::where('status', 'pending')->count(),
            'pending_reports'    => \App\Models\MongoDB\MongoReport::where('status', 'pending')->count(),
        ];
    }

    /**
     * Get recent activity
     */
    protected function getRecentActivity($limit = 10)
    {
        return AdminActivityLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
