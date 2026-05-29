<?php

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoRecommendation;
use Illuminate\Http\Request;
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
            // Today's stats
            $today = Carbon::now()->startOfDay();
            $todayLogs = MongoRecommendation::where('created_at', '>=', $today)->count();

            // This week stats
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();
            $weekLogs = MongoRecommendation::where('created_at', '>=', $weekStart)
                ->where('created_at', '<=', $weekEnd)
                ->count();

            // This month stats
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();
            $monthLogs = MongoRecommendation::where('created_at', '>=', $monthStart)
                ->where('created_at', '<=', $monthEnd)
                ->count();

            // Average duration (recommendation score)
            $allLogs = MongoRecommendation::get();
            $avgDuration = $allLogs->count() > 0 
                ? round($allLogs->avg('recommendation_score'), 1)
                : 0;

            // Popular destinations
            $popularDestinations = MongoRecommendation::with('destination')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->groupBy('destination_id')
                ->map(fn($group) => (object)[
                    'destination_id' => $group->first()->destination_id,
                    'count' => $group->count(),
                    'destination' => $group->first()->destination
                ])
                ->sortByDesc('count')
                ->take(5)
                ->values();

            // Clicked recommendations
            $clickedCount = MongoRecommendation::where('is_clicked', true)->count();
            $totalCount = MongoRecommendation::count();
            $clickRate = $totalCount > 0 
                ? round(($clickedCount / $totalCount) * 100, 1)
                : 0;

            // Trip planner logs (paginated)
            $logs = MongoRecommendation::with('destination')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            // Distribution data for chart
            $distributionData = [
                '1-3 Hari' => MongoRecommendation::where('recommendation_score', '<', 3)->count(),
                '4-7 Hari' => MongoRecommendation::whereBetween('recommendation_score', [3, 7])->count(),
                '8+ Hari' => MongoRecommendation::where('recommendation_score', '>', 7)->count(),
            ];

            // User preferences (mock data - dapat dari behavior_data jika ada)
            $userPreferences = [
                'Alam & Alam Budaya' => 78,
                'Pantai Relaksasi' => 62,
                'Kuliner Khas' => 45,
            ];

            return view('admin.recommendations.index', compact(
                'todayLogs',
                'weekLogs',
                'monthLogs',
                'avgDuration',
                'clickRate',
                'popularDestinations',
                'logs',
                'distributionData',
                'userPreferences'
            ));

        } catch (\Exception $e) {
            Log::error('RecommendationLog index error: ' . $e->getMessage());
            return view('admin.recommendations.index', [
                'todayLogs' => 0,
                'weekLogs' => 0,
                'monthLogs' => 0,
                'avgDuration' => 0,
                'clickRate' => 0,
                'popularDestinations' => collect(),
                'logs' => collect(),
                'distributionData' => [],
                'userPreferences' => [],
                'error' => 'Failed to load recommendation data'
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

            $log->load('destination');
            return view('admin.recommendations.show', compact('log'));

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
            $logs = MongoRecommendation::with('destination')
                ->orderBy('created_at', 'desc')
                ->get();

            $filename = 'recommendations_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($logs) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                fputcsv($file, ['Trip ID', 'Destinasi', 'Skor Rekomendasi', 'Diklik', 'Tanggal'], ';');

                foreach ($logs as $index => $log) {
                    fputcsv($file, [
                        '#TRP-2024-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                        $log->destination?->name ?? 'N/A',
                        round($log->recommendation_score, 1),
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
