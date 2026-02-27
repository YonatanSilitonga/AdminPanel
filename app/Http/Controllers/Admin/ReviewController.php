<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoReview;
use App\Models\MongoDB\MongoDestination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends BaseAdminController
{
    /**
     * Display list of reviews from MongoDB
     */
    public function index(Request $request)
    {
        try {
            $query = MongoReview::with('destination');

            // Filter by rating
            if ($request->filled('rating')) {
                $query->where('rating', (int)$request->rating);
            }

            // Search in review text
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('review', 'like', "%{$search}%");
            }

            $reviews = $query->orderBy('created_at', 'desc')
                ->paginate(15);

            $ratings = [1, 2, 3, 4, 5];

            return view('admin.reviews.index', [
                'reviews' => $reviews,
                'ratings' => $ratings,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reviews from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error loading reviews from MongoDB');
        }
    }

    /**
     * Show review details from MongoDB
     */
    public function show(string $id)
    {
        try {
            $review = MongoReview::with('destination')->findOrFail($id);

            return view('admin.reviews.show', [
                'review' => $review,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading review from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error loading review');
        }
    }

    /**
     * Delete review from MongoDB
     */
    public function destroy(string $id)
    {
        try {
            $review = MongoReview::findOrFail($id);
            $review->delete();

            $this->logActivity('delete_review_mongo', 'review', $id);

            return redirect()
                ->route('admin.reviews.index')
                ->with('success', 'Review deleted from MongoDB successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting review from Mongo: ' . $e->getMessage());
            return back()->with('error', 'Error deleting review');
        }
    }
}
