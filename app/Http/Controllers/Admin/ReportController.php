<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends BaseAdminController
{
    /**
     * Display list of reports from MongoDB
     */
    public function index(Request $request)
    {
        Log::info('Report index accessed', ['request' => $request->all()]);
        try {
            $query = MongoReport::query();

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by reason
            if ($request->filled('reason')) {
                $query->where('reason', $request->reason);
            }

            // Filter by assigned admin
            if ($request->filled('assigned')) {
                if ($request->assigned === 'me' && $this->admin) {
                    $query->where('assigned_to', (string)$this->admin->id);
                } elseif ($request->assigned === 'unassigned') {
                    $query->whereNull('assigned_to');
                }
            }

            // Search in description, user ID, reason, or destination name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('user_id', 'like', "%{$search}%")
                      ->orWhere('reason', 'like', "%{$search}%")
                      ->orWhereHas('destination', function ($destQuery) use ($search) {
                          $destQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Pagination & Sorting
            $perPage = (int) $request->input('per_page', 15);
            $allowedSorts = ['reason', 'status', 'created_at', 'user_id'];
            $sortBy = in_array($request->input('sort_by'), $allowedSorts) ? $request->input('sort_by') : 'created_at';
            $sortOrder = $request->input('sort_order') === 'asc' ? 'asc' : 'desc';

            $reports = $query->orderBy($sortBy, $sortOrder)
                ->paginate($perPage)
                ->withQueryString();

            $statuses = ['pending', 'reviewed', 'resolved'];
            $reasons = ['spam', 'inappropriate', 'fake', 'harassment', 'facility_damage', 'other'];

            return view('admin.reports.index', [
                'reports' => $reports,
                'statuses' => $statuses,
                'reasons' => $reasons,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reports from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
        }
    }

    public function show(string $id, Request $request)
    {
        try {
            $report = MongoReport::findOrFail($id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($report->append('all_image_urls'));
            }

            return view('admin.reports.show', ['report' => $report]);
        } catch (\Exception $e) {
            Log::error('Error loading report from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error loading report');
        }
    }

    /**
     * Assign report to admin in MongoDB
     */
    public function assign(string $id)
    {
        try {
            $report = MongoReport::findOrFail($id);
            $oldAssignedTo = $report->assigned_to;
            
            $report->assigned_to = (string)$this->admin->id;
            $report->status = 'reviewed';
            $report->save();

            $this->logActivity(
                'assign_report_mongo',
                'report',
                $id,
                ['assigned_to' => $oldAssignedTo],
                ['assigned_to' => (string)$this->admin->id]
            );

            return back()->with('success', 'Report assigned to you (MongoDB updated)');
        } catch (\Exception $e) {
            Log::error('Error assigning report in Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error assigning report');
        }
    }

    public function updateStatus(string $id, Request $request)
    {
        try {
            $request->validate(['status' => 'required|in:pending,reviewed,resolved']);

            $report = MongoReport::findOrFail($id);
            $oldStatus = $report->status;
            $report->status = $request->status;
            $report->assigned_to = $report->assigned_to ?? (string)$this->admin->id;
            $report->save();

            $this->logActivity('update_report_status_mongo', 'report', $id, ['status' => $oldStatus], ['status' => $request->status]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Status updated.']);
            }

            return back()->with('success', 'Report status updated in MongoDB');
        } catch (\Exception $e) {
            Log::error('Error updating report status in Mongo: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', 'Error updating report status');
        }
    }

    /**
     * Take action on report in MongoDB
     */
    public function takeAction(string $id, Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|in:delete_content,warn_user,ignore',
                'action_reason' => 'required|string|max:500',
            ]);

            $report = MongoReport::findOrFail($id);
            $oldAction = $report->action_taken;
            
            $report->action_taken = $request->action;
            $report->action_reason = $request->action_reason;
            $report->status = 'resolved';
            $report->assigned_to = (string)$this->admin->id;
            $report->save();

            $this->logActivity(
                'take_report_action_mongo',
                'report',
                $id,
                ['action_taken' => $oldAction],
                ['action_taken' => $request->action, 'action_reason' => $request->action_reason]
            );

            return back()->with('success', 'Action taken on report (MongoDB updated)');
        } catch (\Exception $e) {
            Log::error('Error taking action on report in Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error taking action on report');
        }
    }

    /**
     * Delete report from MongoDB
     */
    public function destroy(string $id)
    {
        try {
            $report = MongoReport::findOrFail($id);
            $report->delete();

            $this->logActivity('delete_report_mongo', 'report', $id);

            return redirect()
                ->route('admin.reports.index')
                ->with('success', 'Report deleted from MongoDB successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting report from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error deleting report');
        }
    }

    /**
     * Export reports to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = MongoReport::query();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('reason')) {
                $query->where('reason', $request->reason);
            }

            // Search in description, user ID, reason, or destination name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('user_id', 'like', "%{$search}%")
                      ->orWhere('reason', 'like', "%{$search}%")
                      ->orWhereHas('destination', function ($destQuery) use ($search) {
                          $destQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $reports = $query->orderBy('created_at', 'desc')->get();

            $filename = 'reports_data_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($reports) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                fputcsv($file, ['ID', 'Pelapor', 'Target/Destinasi', 'Alasan', 'Deskripsi', 'Status', 'Tanggal'], ';');

                foreach ($reports as $report) {
                    fputcsv($file, [
                        $report->_id,
                        $report->user_id ?? 'Anonim',
                        $report->destination?->name ?? 'Umum',
                        $report->reason ?? '-',
                        $report->description ?? '-',
                        $report->status ?? 'pending',
                        $report->created_at?->format('d-m-Y H:i') ?? '-',
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Report export error: ' . $e->getMessage());
            return redirect()->route('admin.reports.index')->with('error', 'Gagal mengekspor data laporan.');
        }
    }
}
