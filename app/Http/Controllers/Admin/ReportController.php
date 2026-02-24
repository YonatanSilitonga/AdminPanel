<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends BaseAdminController
{
    /**
     * Display list of reports
     */
    public function index(Request $request)
    {
        try {
            $query = Report::with('user');

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by reason
            if ($request->filled('reason')) {
                $query->where('reason', $request->reason);
            }

            // Filter by assigned
            if ($request->filled('assigned')) {
                if ($request->assigned === 'me') {
                    $query->where('assigned_to', $this->admin->id);
                } elseif ($request->assigned === 'unassigned') {
                    $query->whereNull('assigned_to');
                }
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $reports = $query->orderBy('created_at', 'desc')
                ->paginate(config('admin-panel.pagination.per_page'));

            $statuses = ['pending', 'investigating', 'resolved', 'dismissed'];
            $reasons = ['spam', 'inappropriate', 'fake', 'harassment', 'other'];

            return view('admin.reports.index', [
                'reports' => $reports,
                'statuses' => $statuses,
                'reasons' => $reasons,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reports: ' . $e->getMessage());
            return back()->with('error', 'Error loading reports');
        }
    }

    /**
     * Show report details
     */
    public function show(Report $report)
    {
        try {
            $report->load('user');

            return view('admin.reports.show', [
                'report' => $report,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading report: ' . $e->getMessage());
            return back()->with('error', 'Error loading report');
        }
    }

    /**
     * Assign report to admin
     */
    public function assign(Report $report)
    {
        try {
            $oldAssignedTo = $report->assigned_to;
            $report->update([
                'assigned_to' => $this->admin->id,
                'status' => $report->status ?? 'investigating',
            ]);

            $this->logActivity(
                'assign_report',
                'report',
                $report->id,
                ['assigned_to' => $oldAssignedTo],
                ['assigned_to' => $this->admin->id]
            );

            return back()->with('success', 'Report assigned to you');
        } catch (\Exception $e) {
            Log::error('Error assigning report: ' . $e->getMessage());
            return back()->with('error', 'Error assigning report');
        }
    }

    /**
     * Update report status
     */
    public function updateStatus(Report $report, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,investigating,resolved,dismissed',
            ]);

            $oldStatus = $report->status;
            $report->update([
                'status' => $request->status,
                'assigned_to' => $report->assigned_to ?? $this->admin->id,
            ]);

            $this->logActivity(
                'update_report_status',
                'report',
                $report->id,
                ['status' => $oldStatus],
                ['status' => $request->status]
            );

            return back()->with('success', 'Report status updated');
        } catch (\Exception $e) {
            Log::error('Error updating report status: ' . $e->getMessage());
            return back()->with('error', 'Error updating report status');
        }
    }

    /**
     * Take action on report
     */
    public function takeAction(Report $report, Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|in:delete_content,warn_user,ignore',
                'action_reason' => 'required|string|max:500',
            ]);

            $oldAction = $report->action_taken;
            $report->update([
                'action_taken' => $request->action,
                'action_reason' => $request->action_reason,
                'status' => 'resolved',
                'assigned_to' => $this->admin->id,
            ]);

            $this->logActivity(
                'take_report_action',
                'report',
                $report->id,
                ['action_taken' => $oldAction],
                ['action_taken' => $request->action, 'action_reason' => $request->action_reason]
            );

            return back()->with('success', 'Action taken on report');
        } catch (\Exception $e) {
            Log::error('Error taking action on report: ' . $e->getMessage());
            return back()->with('error', 'Error taking action on report');
        }
    }

    /**
     * Delete report
     */
    public function destroy(Report $report)
    {
        try {
            $reportId = $report->id;
            $report->delete();

            $this->logActivity('delete_report', 'report', $reportId);

            return redirect()
                ->route('admin.reports.index')
                ->with('success', 'Report deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting report: ' . $e->getMessage());
            return back()->with('error', 'Error deleting report');
        }
    }
}
