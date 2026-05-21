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

        // Filter by entity type
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->input('entity_type'));
        }

        // Filter by admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->input('admin_id'));
        }

        // Search in IP address
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'regex', ".*{$search}.*")
                  ->orWhere('entity_id', 'regex', ".*{$search}.*");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = (int) $request->input('per_page', 25);
        $logs = $query->paginate($perPage)->withQueryString();

        // distinct('field')->pluck('field') returns nulls in mongodb/laravel-mongodb.
        // Use raw aggregation instead to get actual distinct values.
        $actions = collect(
            AdminActivityLog::raw(fn($col) => $col->distinct('action', []))
        )->filter(fn($v) => !empty($v))->sort()->values();

        $entityTypes = collect(
            AdminActivityLog::raw(fn($col) => $col->distinct('entity_type', []))
        )->filter(fn($v) => !empty($v))->sort()->values();

        $admins = Admin::orderBy('name', 'asc')->get();

        return view('admin.settings.audit-logs.index', compact('logs', 'actions', 'entityTypes', 'admins'));
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
