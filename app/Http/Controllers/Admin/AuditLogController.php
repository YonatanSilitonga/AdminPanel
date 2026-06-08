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

        // Resolve entity name and url
        $entityName = null;
        $entityUrl = null;

        if ($log->entity_id && $log->entity_type) {
            try {
                switch ($log->entity_type) {
                    case 'destination':
                        $item = \App\Models\MongoDB\MongoDestination::find($log->entity_id);
                        if ($item) {
                            $entityName = $item->name;
                            $entityUrl = route('admin.destinations.edit', $log->entity_id);
                        }
                        break;
                    case 'event':
                        $item = \App\Models\MongoDB\MongoEvent::find($log->entity_id);
                        if ($item) {
                            $entityName = $item->name;
                            $entityUrl = route('admin.events.edit', $log->entity_id);
                        }
                        break;
                    case 'facility':
                        $item = \App\Models\MongoDB\MongoFasilitasUmum::find($log->entity_id);
                        if ($item) {
                            $entityName = $item->name;
                            $entityUrl = route('admin.fasilitas_umum.edit', $log->entity_id);
                        }
                        break;
                    case 'budaya':
                        $item = \App\Models\MongoDB\MongoBudaya::find($log->entity_id);
                        if ($item) {
                            $entityName = $item->name;
                            $entityUrl = route('admin.budaya.edit', $log->entity_id);
                        }
                        break;
                    case 'berita_promosi':
                        $item = \App\Models\MongoDB\MongoBeritaPromosi::find($log->entity_id);
                        if ($item) {
                            $entityName = $item->title ?? $item->name;
                            $entityUrl = route('admin.berita_promosi.edit', $log->entity_id);
                        }
                        break;
                    case 'review':
                        $item = \App\Models\MongoDB\MongoReview::find($log->entity_id);
                        if ($item) {
                            $entityName = 'Ulasan dari ' . ($item->reviewer_name ?? 'Anonim') . ($item->destination ? ' untuk ' . $item->destination->name : '');
                            $entityUrl = route('admin.reviews.index') . '?search=' . $log->entity_id;
                        }
                        break;
                    case 'report':
                        $item = \App\Models\MongoDB\MongoReport::find($log->entity_id);
                        if ($item) {
                            $entityName = 'Laporan: ' . ($item->title ?? $item->category ?? 'Keluhan Wisatawan');
                            $entityUrl = route('admin.reports.show', $log->entity_id);
                        }
                        break;
                    case 'settings':
                        $entityName = 'Pengaturan ' . ucfirst($log->entity_id);
                        if ($log->entity_id === 'general') {
                            $entityUrl = route('admin.settings.general');
                        } elseif ($log->entity_id === 'api-keys') {
                            $entityUrl = route('admin.settings.api-keys');
                        } elseif ($log->entity_id === 'ai-config') {
                            $entityUrl = route('admin.settings.ai-config');
                        }
                        break;
                    case 'user':
                        $item = \App\Models\User::find($log->entity_id);
                        if ($item) {
                            $entityName = 'Pengguna: ' . ($item->name ?? $item->email);
                            $entityUrl = route('admin.users.index') . '?search=' . urlencode($item->email ?? $item->name);
                        }
                        break;
                }
            } catch (\Exception $e) {
                // Ignore route or db resolution errors
            }
        }

        // Return JSON with extra metadata if it's an AJAX request
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            $logData = $log->toArray();
            $logData['resolved_entity_name'] = $entityName;
            $logData['resolved_entity_url'] = $entityUrl;
            return response()->json($logData);
        }

        // Otherwise return the detail view (fallback)
        return view('admin.settings.audit-logs.show', compact('log', 'entityName', 'entityUrl'));
    }
}
