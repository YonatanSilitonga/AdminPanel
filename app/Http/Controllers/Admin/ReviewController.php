<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends BaseAdminController
{
    /**
     * Display list of reviews
     */
    public function index(Request $request)
    {
        try {
            $query = Review::with('user', 'destination');

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by rating
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // Filter by reported
            if ($request->filled('reported')) {
                if ($request->reported === 'true') {
                    $query->where('reported_count', '>', 0);
                }
            }

            $reviews = $query->orderBy('created_at', 'desc')
                ->paginate(config('admin-panel.pagination.per_page'));

            $statuses = ['pending', 'approved', 'rejected'];
            $ratings = [1, 2, 3, 4, 5];

            return view('admin.reviews.index', [
                'reviews' => $reviews,
                'statuses' => $statuses,
                'ratings' => $ratings,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reviews: ' . $e->getMessage());
            return back()->with('error', 'Error loading reviews');
        }
    }

    /**
     * Show review details
     */
    public function show(Review $review)
    {
        try {
            $review->load('user', 'destination');

            return view('admin.reviews.show', [
                'review' => $review,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading review: ' . $e->getMessage());
            return back()->with('error', 'Error loading review');
        }
    }

    /**
     * Approve review
     */
    public function approve(Review $review)
    {
        try {
            if ($review->status === 'approved') {
                return back()->with('warning', 'Review is already approved');
            }

            $oldStatus = $review->status;
            $review->update([
                'status' => 'approved',
                'approved_by' => $this->admin->id,
            ]);

            // Log action
            $this->logActivity(
                'approve_review',
                'review',
                $review->id,
                ['status' => $oldStatus],
                ['status' => 'approved']
            );

            return back()->with('success', 'Review approved successfully');
        } catch (\Exception $e) {
            Log::error('Error approving review: ' . $e->getMessage());
            return back()->with('error', 'Error approving review');
        }
    }

    /**
     * Reject review
     */
    public function reject(Review $review, Request $request)
    {
        try {
            if ($review->status === 'rejected') {
                return back()->with('warning', 'Review is already rejected');
            }

            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $oldStatus = $review->status;
            $review->update([
                'status' => 'rejected',
                'approved_by' => $this->admin->id,
            ]);

            // Log action
            $this->logActivity(
                'reject_review',
                'review',
                $review->id,
                ['status' => $oldStatus],
                ['status' => 'rejected', 'reason' => $request->reason]
            );

            return back()->with('success', 'Review rejected successfully');
        } catch (\Exception $e) {
            Log::error('Error rejecting review: ' . $e->getMessage());
            return back()->with('error', 'Error rejecting review');
        }
    }

    /**
     * Delete review
     */
    public function destroy(Review $review)
    {
        try {
            $reviewId = $review->id;
            $review->delete();

            // Log action
            $this->logActivity('delete_review', 'review', $reviewId);

            return redirect()
                ->route('admin.reviews.index')
                ->with('success', 'Review deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting review: ' . $e->getMessage());
            return back()->with('error', 'Error deleting review');
        }
    }

    /**
     * Toggle review reported status
     */
    public function clearReports(Review $review)
    {
        try {
            $oldCount = $review->reported_count;
            $review->update(['reported_count' => 0]);

            $this->logActivity(
                'clear_reports',
                'review',
                $review->id,
                ['reported_count' => $oldCount],
                ['reported_count' => 0]
            );

            return back()->with('success', 'Review reports cleared');
        } catch (\Exception $e) {
            Log::error('Error clearing reports: ' . $e->getMessage());
            return back()->with('error', 'Error clearing reports');
        }
    }
}
