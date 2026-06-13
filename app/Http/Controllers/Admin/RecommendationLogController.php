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
            // 1. Counting stats — gunakan Carbon langsung (MongoDB Laravel driver
            //    menangani konversi Carbon ke UTCDateTime secara otomatis sejak v4.x)
            $today      = Carbon::now()->startOfDay();
            $todayLogs  = MongoRecommendation::where('created_at', '>=', $today)->count();

            $weekStart  = Carbon::now()->startOfWeek();
            $weekEnd    = Carbon::now()->endOfWeek();
            $weekLogs   = MongoRecommendation::where('created_at', '>=', $weekStart)
                ->where('created_at', '<=', $weekEnd)
                ->count();

            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd   = Carbon::now()->endOfMonth();
            $monthLogs  = MongoRecommendation::where('created_at', '>=', $monthStart)
                ->where('created_at', '<=', $monthEnd)
                ->count();

            $totalCount  = MongoRecommendation::count();
            $avgDuration = $totalCount > 0
                ? round(MongoRecommendation::avg('recommendation_score'), 1)
                : 0;

            $clickedCount = MongoRecommendation::where('is_clicked', true)->count();
            $clickRate    = $totalCount > 0
                ? round(($clickedCount / $totalCount) * 100, 1)
                : 0;

            // 2. Fetch heavier metrics cached for 1 minute (for speed, but short enough for responsive testing)
            $heavyStats = Cache::remember('admin.recommendations.heavy_stats', now()->addMinutes(1), function () {
                // Popular destinations — MongoDB aggregation dari SELURUH data (bukan limit 500)
                try {
                    $pipeline = [
                        ['$match' => ['destination_id' => ['$exists' => true, '$ne' => null, '$ne' => '']]],
                        ['$group' => ['_id' => '$destination_id', 'count' => ['$sum' => 1]]],
                        ['$sort'  => ['count' => -1]],
                        ['$limit' => 5],
                    ];
                    $aggResult = MongoRecommendation::raw(fn ($col) => $col->aggregate($pipeline));

                    $destIds = collect($aggResult)->pluck('_id')->filter()->map(fn ($id) => (string) $id)->toArray();
                    $destinations = MongoDestination::whereIn('_id', $destIds)->get()->keyBy(fn ($d) => (string) $d->_id);

                    $popularDestinations = collect($aggResult)->map(function ($row) use ($destinations) {
                        $id   = (string) ($row['_id'] ?? '');
                        $dest = $destinations->get($id);
                        return (object) [
                            'destination_id' => $id,
                            'count'          => (int) ($row['count'] ?? 0),
                            'destination'    => $dest,
                        ];
                    })->filter(fn ($r) => $r->destination !== null)->values();
                } catch (\Exception $e) {
                    Log::warning('Popular destinations aggregation failed, falling back: ' . $e->getMessage());
                    // Fallback ke method lama jika aggregation tidak tersedia
                    $popularDestinations = MongoRecommendation::with('destination')
                        ->orderBy('created_at', 'desc')
                        ->limit(1000)
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
                }

                // Distribution of trip duration (recommendation_score = duration in days)
                $distributionData = [
                    '1-3 Hari' => MongoRecommendation::where('recommendation_score', '<=', 3)->count(),
                    '4-7 Hari' => MongoRecommendation::whereBetween('recommendation_score', [4, 7])->count(),
                    '8+ Hari'  => MongoRecommendation::where('recommendation_score', '>=', 8)->count(),
                ];

                // User preferences
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

                return compact('popularDestinations', 'distributionData', 'userPreferences');
            });

            // 3. Build query for history table with search & filters
            $query = MongoRecommendation::with(['destination', 'user']);

            // Search Filter (Trip title, user name, destination name, or ID)
            if ($request->filled('search')) {
                $search = $request->input('search');
                
                // Find matching destinations in MongoDB
                $destIds = MongoDestination::where('name', 'like', "%{$search}%")->pluck('_id')->toArray();
                
                // Find matching users in MySQL
                $userIds = \App\Models\User::where('name', 'like', "%{$search}%")->pluck('id')->toArray();

                $query->where(function ($q) use ($search, $destIds, $userIds) {
                    $q->where('behavior_data.trip_title', 'like', "%{$search}%")
                      ->orWhere('behavior_data.user_name', 'like', "%{$search}%");
                    
                    if (preg_match('/^[a-f\d]{24}$/i', $search)) {
                        $q->orWhere('_id', $search);
                    }
                    
                    if (!empty($destIds)) {
                        foreach ($destIds as $id) {
                            $q->orWhere('destination_id', (string)$id);
                        }
                    }
                    
                    if (!empty($userIds)) {
                        $q->orWhereIn('user_id', $userIds);
                    }
                });
            }

            // Duration Filter
            if ($request->filled('duration')) {
                $dur = $request->input('duration');
                if ($dur === '1-3') {
                    $query->where('recommendation_score', '<=', 3);
                } elseif ($dur === '4-7') {
                    $query->whereBetween('recommendation_score', [4, 7]);
                } elseif ($dur === '8+') {
                    $query->where('recommendation_score', '>=', 8);
                }
            }

            // Click Status Filter
            if ($request->filled('status')) {
                $status = $request->input('status');
                if ($status === 'clicked') {
                    $query->where('is_clicked', true);
                } elseif ($status === 'ignored') {
                    $query->where('is_clicked', false);
                }
            }

            // User Type Filter
            if ($request->filled('user_type')) {
                $ut = $request->input('user_type');
                if ($ut === 'registered') {
                    $query->whereNotNull('user_id')->where('user_id', '!=', '');
                } elseif ($ut === 'guest') {
                    $query->where(function($q) {
                        $q->whereNull('user_id')->orWhere('user_id', '');
                    });
                }
            }

            // Destination Filter
            if ($request->filled('destination_id')) {
                $query->where('destination_id', $request->input('destination_id'));
            }

            // Pagination
            $perPage = (int) $request->input('per_page', 15);
            $logs = $query->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            // Ambil daftar destinasi yang pernah direkomendasikan untuk dropdown filter
            // Gunakan data dari popularDestinations + query distinct untuk efisiensi
            $filterDestinations = MongoDestination::whereIn(
                '_id',
                MongoRecommendation::distinct('destination_id')->get()->pluck('destination_id')
                    ->filter()->map(fn ($id) => (string) $id)->toArray()
            )->orderBy('name', 'asc')->get(['_id', 'name'])->map(fn ($d) => [
                'id'   => (string) $d->_id,
                'name' => $d->name,
            ]);

            // Merge stats cards, heavy cached stats, and logs for view
            $dataForView = array_merge(
                compact('todayLogs', 'weekLogs', 'monthLogs', 'avgDuration', 'clickRate'),
                $heavyStats,
                compact('logs', 'filterDestinations')
            );

            return view('admin.recommendations.index', $dataForView);

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
                'filterDestinations' => collect(),
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

            // Itinerary: ambil dari behavior_data dulu, fallback ke trip_plans
            $itinerary    = $log->behavior_data['itinerary'] ?? [];
            $tripDuration = $log->behavior_data['duration']  ?? round($log->recommendation_score);
            $budget       = $log->behavior_data['budget']    ?? null;
            $tripTitle    = $log->behavior_data['trip_title'] ?? null;

            // Trip plans dari SmartTrip — ambil trip milik user ini (5 terbaru)
            $tripPlans = collect();
            if (!empty($log->user_id)) {
                $tripPlans = \App\Models\MongoDB\MongoTripPlan::where('user_id', (string) $log->user_id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                // Jika itinerary kosong di behavior_data, coba ambil dari trip_plans terdekat
                // Struktur SmartTrip: summary.days[] bukan itinerary[]
                if (empty($itinerary) && $tripPlans->isNotEmpty()) {
                    $closestPlan = $tripPlans->first();
                    $summaryDays = $closestPlan->summary['days'] ?? [];
                    if (!empty($summaryDays)) {
                        // Normalisasi struktur summary.days ke format itinerary yang blade sudah handle
                        $itinerary = array_map(function ($day) {
                            return [
                                'day'         => $day['day_number']  ?? 1,
                                'title'       => $day['date_label']  ?? ('Hari Ke-' . ($day['day_number'] ?? 1)),
                                'description' => ($day['start_from'] ? 'Mulai dari ' . $day['start_from'] : '') .
                                                 ($day['smart_tip']  ? ' — ' . $day['smart_tip'] : ''),
                                'activities'  => array_map(fn ($act) =>
                                    ($act['time'] ? '[' . $act['time'] . '] ' : '') .
                                    ($act['name'] ?? '') .
                                    ($act['travel_mode'] ? ' (' . $act['travel_mode'] . ')' : ''),
                                $day['activities'] ?? []),
                            ];
                        }, $summaryDays);
                        $tripDuration = $closestPlan->summary['total_days'] ?? $tripDuration;
                        $tripTitle    = $closestPlan->summary['title']      ?? $tripTitle;
                    }
                }
            }

            return view('admin.recommendations.show', compact(
                'log', 'destinationStats', 'preferences', 'itinerary',
                'tripDuration', 'budget', 'tripTitle', 'tripPlans'
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
                        '#TRP-' . strtoupper(substr((string)$log->_id, -6)),
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
