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
            AdminActivityLog::log(
                $action,
                $entityType,
                $entityId,
                $oldValues,
                $newValues,
                $this->admin->id
            );
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
     * Upload and store file
     *
     * @param $file
     * @param string $path
     * @param array $options
     * @return string|null
     */
    protected function uploadFile($file, $path = 'uploads', $options = [])
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Validate file
        $maxSize = $options['max_size'] ?? 5; // MB
        if ($file->getSize() > $maxSize * 1024 * 1024) {
            throw new \Exception("File size exceeds {$maxSize}MB limit");
        }

        $allowedMimes = $options['mimes'] ?? ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('File type not allowed');
        }

        // Generate filename
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

        // Store file
        $filePath = $file->storeAs($path, $filename, 'public');

        return $filePath;
    }

    /**
     * Delete file from storage
     */
    protected function deleteFile($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    protected function processImage($file, $path, $sizes = [])
    {
        if (!$file) {
            return null;
        }

        // Return the path relative to storage/app/public
        // This will be something like 'destinations/filename.jpg'
        return $file->store($path, 'public');
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
