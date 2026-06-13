<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoDestination;
use App\Models\MongoDB\MongoReview;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrendingDestinationController extends BaseAdminController
{
    /**
     * Display trending destinations dashboard
     */
    public function index()
    {
        $mode = AppSetting::get('trending_mode', 'manual');
        $manualList = AppSetting::get('trending_list', []);
        
        $trendingDestinations = [];
        if ($mode === 'manual' && !empty($manualList)) {
            // Fetch destinations in the order they appear in the manual list
            // Ensure IDs are strings for matching
            $trendingDestinations = collect($manualList)->map(function($id) {
                $dest = MongoDestination::find((string)$id);
                if ($dest) {
                    $dest->id_str = (string)$dest->_id;
                }
                return $dest;
            })->filter()->values();
        } else {
            // Automatic mode: fetch active destinations, calculate trending score in-memory
            // Score logic: heavily favor total_reviews (popularity) combined with rating
            $trendingDestinations = MongoDestination::where('is_active', true)
                ->get()
                ->sortByDesc(function($dest) {
                    $reviewsCount = $dest->total_reviews;
                    $avgRating = $dest->average_rating;
                    // Formula: (Reviews * 10) + Rating
                    return ($reviewsCount * 10) + $avgRating;
                })
                ->take(10)
                ->map(function($dest) {
                    $dest->id_str = (string)$dest->_id;
                    return $dest;
                })->values();
        }

        $stats = [
            'total_destinations' => MongoDestination::where('is_active', true)->count(),
            'total_wishlist'     => DB::connection('mongodb')->table('favorites')->count(),
            'total_review'       => MongoReview::count(),
            'destinations_increase' => 0,
            'wishlist_increase'  => 0,
            'review_increase'    => 0,
        ];

        return view('admin.destinations.trending', [
            'mode' => $mode,
            'trendingDestinations' => $trendingDestinations,
            'stats' => $stats
        ]);
    }

    /**
     * Update trending mode
     */
    public function updateMode(Request $request)
    {
        $request->validate(['mode' => 'required|in:manual,automatic']);
        AppSetting::set('trending_mode', $request->mode);
        
        return response()->json(['success' => true, 'message' => 'Mode trending diperbarui']);
    }

    /**
     * Update manual trending order
     */
    public function updateOrder(Request $request)
    {
        try {
            $request->validate(['orders' => 'required|array']);
            // Ensure all IDs are strings
            $orders = array_map('strval', $request->orders);
            AppSetting::set('trending_list', $orders, 'json');
            
            return response()->json(['success' => true, 'message' => 'Urutan trending diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Add destination to manual trending list
     */
    public function addDestination(Request $request)
    {
        $request->validate(['destination_id' => 'required']);
        
        $currentList = AppSetting::get('trending_list', []);
        $id = (string)$request->destination_id;
        
        if (!in_array($id, $currentList)) {
            $currentList[] = $id;
            AppSetting::set('trending_list', $currentList, 'json');
        }
        
        return response()->json(['success' => true, 'message' => 'Destinasi ditambahkan ke trending']);
    }

    /**
     * Remove destination from manual trending list
     */
    public function removeDestination(Request $request, $id)
    {
        $currentList = AppSetting::get('trending_list', []);
        $id = (string)$id;
        $newList = array_values(array_filter($currentList, fn($item) => (string)$item !== $id));
        AppSetting::set('trending_list', $newList, 'json');
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Destinasi dihapus dari trending']);
        }
        
        return redirect()->back()->with('success', 'Destinasi berhasil dihapus dari trending');
    }

    /**
     * Reset to automatic mode
     */
    public function resetToAutomatic()
    {
        AppSetting::set('trending_mode', 'automatic');
        return response()->json(['success' => true, 'message' => 'Sistem dikembalikan ke mode otomatis']);
    }

    /**
     * Search destinations
     */
    public function searchDestinations(Request $request)
    {
        $search = $request->query('q');
        $excludeIds = AppSetting::get('trending_list', []);
        
        $destinations = MongoDestination::where('name', 'like', "%{$search}%")
            ->limit(5)
            ->get()
            ->map(function($dest) {
                return [
                    '_id' => (string)$dest->_id,
                    'name' => $dest->name,
                    'location' => $dest->location,
                    'category' => $dest->category,
                    'average_rating' => $dest->average_rating,
                    'images' => $dest->images
                ];
            });
            
        return response()->json($destinations);
    }
}
