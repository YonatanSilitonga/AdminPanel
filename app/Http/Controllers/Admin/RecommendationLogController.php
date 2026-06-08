<?php

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoRecommendation;
use App\Models\MongoDB\MongoDestination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RecommendationLogController extends BaseAdminController
{
    /**
     * Display recommendation logs dashboard
     */
    public function index(Request $request)
    {
        try {
            $stats = Cache::remember('admin.recommendations.stats_summary', now()->addMinutes(10), function () {
                // Today's stats
                $today = Carbon::now()->startOfDay();
                $todayLogs = MongoRecommendation::where('created_at', '>=', $today)->count();

                // This week stats
                $weekStart = Carbon::now()->startOfWeek();
                $weekEnd   = Carbon::now()->endOfWeek();
                $weekLogs  = MongoRecommendation::where('created_at', '>=', $weekStart)
                    ->where('created_at', '<=', $weekEnd)
                    ->count();

                // This month stats
                $monthStart = Carbon::now()->startOfMonth();
                $monthEnd   = Carbon::now()->endOfMonth();
                $monthLogs  = MongoRecommendation::where('created_at', '>=', $monthStart)
                    ->where('created_at', '<=', $monthEnd)
                    ->count();

                // Average recommendation_score (interpreted as trip duration in days)
                $totalCount  = MongoRecommendation::count();
                $avgDuration = $totalCount > 0
                    ? round(MongoRecommendation::avg('recommendation_score'), 1)
                    : 0;

                // Popular destinations — group by destination_id on last 500 logs
                $popularDestinations = MongoRecommendation::with('destination')
                    ->orderBy('created_at', 'desc')
                    ->limit(500)
                    ->get()
                    ->groupBy('destination_id')
                    ->map(fn ($group) => (object) [
                        'destination_id' => $group->first()->destination_id,
                        'count'          => $group->count(),
                        'destination'    => $group->first()->destination,
                    ])
                    ->sortByDesc('count')
                    ->take(5)
                    ->values();

                // Click rate
                $clickedCount = MongoRecommendation::where('is_clicked', true)->count();
                $clickRate    = $totalCount > 0
                    ? round(($clickedCount / $totalCount) * 100, 1)
                    : 0;

                // Distribution of trip duration (recommendation_score = duration in days)
                $distributionData = [
                    '1-3 Hari' => MongoRecommendation::where('recommendation_score', '<=', 3)->count(),
                    '4-7 Hari' => MongoRecommendation::whereBetween('recommendation_score', [4, 7])->count(),
                    '8+ Hari'  => MongoRecommendation::where('recommendation_score', '>=', 8)->count(),
                ];

                // User preferences — computed from behavior_data.categories stored by the Go backend.
                // We aggregate across all logs that have behavior_data with a categories field.
                $categoryMap = [];
                $categoryLabels = [
                    'wisata_alam'    => 'Wisata Alam',
                    'wisata_budaya'  => 'Wisata Budaya',
                    'wisata_kuliner' => 'Wisata Kuliner',
                    'wisata_religi'  => 'Wisata Religi',
                    'wisata_sejarah' => 'Wisata Sejarah',
                ];

                // Pull logs that have behavior_data
                $logsWithBehavior = MongoRecommendation::whereNotNull('behavior_data')
                    ->limit(2000)
                    ->get(['behavior_data']);

                foreach ($logsWithBehavior as $rec) {
                    $cats = $rec->behavior_data['categories'] ?? [];
                    if (is_array($cats)) {
                        foreach ($cats as $cat) {
                            $key = is_string($cat) ? strtolower(trim($cat)) : null;
                            if ($key) {
                                $categoryMap[$key] = ($categoryMap[$key] ?? 0) + 1;
                            }
                        }
                    }
                }

                // Build userPreferences as percentage relative to the top count
                $userPreferences = [];
                if (!empty($categoryMap)) {
                    arsort($categoryMap);
                    $maxVal = max($categoryMap);
                    foreach (array_slice($categoryMap, 0, 5, true) as $key => $count) {
                        $label = $categoryLabels[$key] ?? ucwords(str_replace('_', ' ', $key));
                        $userPreferences[$label] = $maxVal > 0 ? round(($count / $maxVal) * 100) : 0;
                    }
                }

                // Fallback: if no behavior_data exists yet, derive from destination categories
                if (empty($userPreferences)) {
                    $destIds = MongoRecommendation::orderBy('created_at', 'desc')
                        ->limit(500)
                        ->pluck('destination_id')
                        ->filter()
                        ->map(fn ($id) => (string) $id)
                        ->toArray();

                    if (!empty($destIds)) {
                        $destinations = MongoDestination::whereIn('_id', $destIds)
                            ->get(['_id', 'category']);

                        $catCounts = [];
                        foreach ($destinations as $dest) {
                            $cat = $dest->category ?? 'unknown';
                            $catCounts[$cat] = ($catCounts[$cat] ?? 0) + 1;
                        }

                        arsort($catCounts);
                        $maxVal = !empty($catCounts) ? max($catCounts) : 1;
                        foreach (array_slice($catCounts, 0, 5, true) as $key => $count) {
                            $label = $categoryLabels[$key] ?? ucwords(str_replace('_', ' ', $key));
                            $userPreferences[$label] = round(($count / $maxVal) * 100);
                        }
                    }
                }

                return compact(
                    'todayLogs', 'weekLogs', 'monthLogs', 'avgDuration',
                    'clickRate', 'popularDestinations', 'distributionData', 'userPreferences'
                );
            });

            // Trip planner logs (paginated — not cached)
            $logs = MongoRecommendation::with(['destination', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('admin.recommendations.index', array_merge($stats, compact('logs')));

        } catch (\Exception $e) {
            Log::error('RecommendationLog index error: ' . $e->getMessage());
            return view('admin.recommendations.index', [
                'todayLogs'          => 0,
                'weekLogs'           => 0,
                'monthLogs'          => 0,
                'avgDuration'        => 0,
                'clickRate'          => 0,
                'popularDestinations'=> collect(),
                'logs'               => collect(),
                'distributionData'   => [],
                'userPreferences'    => [],
                'error'              => 'Gagal memuat data rekomendasi.',
            ]);
        }
    }

    /**
     * Show recommendation detail
     */
    public function show(string $id)
    {
        try {
            $log = MongoRecommendation::where('_id', $id)->first();

            if (!$log) {
                return redirect()->route('admin.recommendations.index')
                    ->with('error', 'Rekomendasi tidak ditemukan');
            }

            $log->load(['destination', 'user']);

            // Enrich destination with real review stats
            $destinationStats = null;
            if ($log->destination) {
                $destId = (string) $log->destination->_id;

                $avgRating   = Cache::remember("dest_avg_rating_{$destId}", now()->addMinutes(15), function () use ($destId) {
                    return \App\Models\MongoDB\MongoReview::where('destination_id', $destId)
                        ->where('status', 'approved')
                        ->avg('rating') ?? 0;
                });

                $totalReviews = Cache::remember("dest_total_reviews_{$destId}", now()->addMinutes(15), function () use ($destId) {
                    return \App\Models\MongoDB\MongoReview::where('destination_id', $destId)
                        ->where('status', 'approved')
                        ->count();
                });

                $destinationStats = [
                    'avg_rating'    => round($avgRating, 1),
                    'total_reviews' => $totalReviews,
                ];
            }

            // Real user preferences from behavior_data
            $preferences = [];
            if (!empty($log->behavior_data['categories']) && is_array($log->behavior_data['categories'])) {
                $categoryLabels = [
                    'wisata_alam'    => 'Alam',
                    'wisata_budaya'  => 'Budaya',
                    'wisata_kuliner' => 'Kuliner',
                    'wisata_religi'  => 'Religi',
                    'wisata_sejarah' => 'Sejarah',
                ];
                foreach ($log->behavior_data['categories'] as $cat) {
                    $key   = strtolower(trim((string) $cat));
                    $label = $categoryLabels[$key] ?? ucwords(str_replace('_', ' ', $key));
                    $preferences[] = $label;
                }
            }

            // Trip itinerary from behavior_data or trip_plans collection
            $itinerary    = $log->behavior_data['itinerary']    ?? [];
            $tripDuration = $log->behavior_data['duration']     ?? round($log->recommendation_score);
            $budget       = $log->behavior_data['budget']       ?? null;
            $tripTitle    = $log->behavior_data['trip_title']   ?? null;

            return view('admin.recommendations.show', compact(
                'log', 'destinationStats', 'preferences', 'itinerary', 'tripDuration', 'budget', 'tripTitle'
            ));

        } catch (\Exception $e) {
            Log::error('RecommendationLog show error: ' . $e->getMessage());
            return redirect()->route('admin.recommendations.index')
                ->with('error', 'Gagal memuat detail rekomendasi');
        }
    }

    /**
     * Export recommendations to CSV
     */
    public function export(Request $request)
    {
        try {
            $logs = MongoRecommendation::with(['destination', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            $filename = 'recommendations_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($logs) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
                fputcsv($file, [
                    'Trip ID', 'Pengguna', 'Tipe Pengguna', 'Destinasi',
                    'Durasi (Hari)', 'Kategori Preferensi', 'Diklik', 'Tanggal',
                ], ';');

                foreach ($logs as $index => $log) {
                    $isRegistered = $log->user
                        && !empty($log->user->password)
                        && (!empty($log->user->email) || !empty($log->user->name));

                    $userName = $log->behavior_data['user_name']
                        ?? ($isRegistered ? ($log->user->name ?? 'User Terdaftar') : 'Tamu');

                    $userType = $isRegistered ? 'User' : 'Guest';

                    // Categories from behavior_data
                    $categories = [];
                    if (!empty($log->behavior_data['categories']) && is_array($log->behavior_data['categories'])) {
                        $categories = $log->behavior_data['categories'];
                    }

                    fputcsv($file, [
                        '#TRP-' . date('Y') . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                        $userName,
                        $userType,
                        $log->destination?->name ?? 'N/A',
                        round($log->recommendation_score),
                        implode(', ', $categories) ?: '-',
                        $log->is_clicked ? 'Ya' : 'Tidak',
                        $log->created_at?->format('d-m-Y H:i') ?? '-',
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('RecommendationLog export error: ' . $e->getMessage());
            return redirect()->route('admin.recommendations.index')
                ->with('error', 'Gagal mengekspor data');
        }
    }
}
