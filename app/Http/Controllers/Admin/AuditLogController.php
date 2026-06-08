<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\AdminActivityLog;
use App\Models\Admin;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuditLogController extends BaseAdminController
{
    /**
     * Display all audit logs.
     */
    public function index(Request $request): View
    {
        $query = AdminActivityLog::query();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filter by module (entity type)
        if ($request->filled('module')) {
            $query->where('entity_type', $request->input('module'));
        }

        // Search in IP address, entity ID, or admin name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'regex', ".*{$search}.*")
                  ->orWhere('entity_id', 'regex', ".*{$search}.*");
            });
        }

        // Date range filter with better handling
        if ($request->filled('date_range')) {
            $range = $request->input('date_range');
            
            switch ($range) {
                case 'today':
                    $query->whereDate('created_at', '=', now()->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', '=', now()->subDay()->toDateString());
                    break;
                case 'last_7_days':
                    $query->whereDate('created_at', '>=', now()->subDays(7)->toDateString());
                    break;
                case 'last_30_days':
                    $query->whereDate('created_at', '>=', now()->subDays(30)->toDateString());
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', '=', now()->month)
                          ->whereYear('created_at', '=', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', '=', now()->subMonth()->month)
                          ->whereYear('created_at', '=', now()->subMonth()->year);
                    break;
                case 'custom':
                    if ($request->filled('custom_date_from')) {
                        $query->whereDate('created_at', '>=', $request->input('custom_date_from'));
                    }
                    if ($request->filled('custom_date_to')) {
                        $query->whereDate('created_at', '<=', $request->input('custom_date_to'));
                    }
                    break;
            }
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = (int) $request->input('per_page', 25);
        $logs = $query->with('admin')->paginate($perPage)->withQueryString();

        // distinct('field')->pluck('field') returns nulls in mongodb/laravel-mongodb.
        // Use raw aggregation instead to get actual distinct values.
        $actions = collect(
            AdminActivityLog::raw(fn($col) => $col->distinct('action', []))
        )->filter(fn($v) => !empty($v))->sort()->values();

        $entityTypes = collect(
            AdminActivityLog::raw(fn($col) => $col->distinct('entity_type', []))
        )->filter(fn($v) => !empty($v))->sort()->values();

        return view('admin.settings.audit-logs.index', compact('logs', 'actions', 'entityTypes'));
    }

    /**
     * Show audit log detail via AJAX or web.
     */
    public function show(string $id)
    {
        $log = AdminActivityLog::findOrFail($id);
        $log->load('admin');

        // If it's an AJAX request, return JSON
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json($log);
        }

        // Otherwise return the detail view (fallback)
        return view('admin.settings.audit-logs.show', compact('log'));
    }
}
