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
        try {
            // In MongoDB, we might not have a separate 'user' collection yet that maps 1:1 with SQLite's User
            // So we'll fetch reports and handle the display of user info gracefully
            $query = MongoReport::with('destination');

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by reason
            if ($request->filled('reason')) {
                $query->where('reason', $request->reason);
            }

            // Filter by assigned (local SQLite admin ID)
            if ($request->filled('assigned')) {
                if ($request->assigned === 'me') {
                    $query->where('assigned_to', (string)$this->admin->id);
                } elseif ($request->assigned === 'unassigned') {
                    $query->whereNull('assigned_to');
                }
            }

            // Search in description
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('description', 'like', "%{$search}%");
            }

            $reports = $query->orderBy('created_at', 'desc')
                ->paginate(15);

            $statuses = ['pending', 'reviewed', 'resolved'];
            $reasons = ['spam', 'inappropriate', 'fake', 'harassment', 'facility_damage', 'other'];

            return view('admin.reports.index', [
                'reports' => $reports,
                'statuses' => $statuses,
                'reasons' => $reasons,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reports from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error loading reports from MongoDB');
        }
    }

    /**
     * Show report details from MongoDB
     */
    public function show(string $id)
    {
        try {
            $report = MongoReport::with('destination')->findOrFail($id);

            return view('admin.reports.show', [
                'report' => $report,
            ]);
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

    /**
     * Update report status in MongoDB
     */
    public function updateStatus(string $id, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,reviewed,resolved',
            ]);

            $report = MongoReport::findOrFail($id);
            $oldStatus = $report->status;
            
            $report->status = $request->status;
            $report->assigned_to = $report->assigned_to ?? (string)$this->admin->id;
            $report->save();

            $this->logActivity(
                'update_report_status_mongo',
                'report',
                $id,
                ['status' => $oldStatus],
                ['status' => $request->status]
            );

            return back()->with('success', 'Report status updated in MongoDB');
        } catch (\Exception $e) {
            Log::error('Error updating report status in Mongo: ' . $e->getMessage());
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
}
