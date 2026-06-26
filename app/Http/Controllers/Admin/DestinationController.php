<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoDestination;
use App\Models\MongoDB\MongoReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DestinationController extends BaseAdminController
{
    /**
     * Display list of destinations and trending analytics from MongoDB
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'manage');
        
        // --- Manage Destinations Logic ---
        $query = MongoDestination::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'regexp', "/{$search}/i")
                  ->orWhere('description', 'regexp', "/{$search}/i");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sortColumn = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['name', 'category', 'is_active', 'created_at'];
        
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 10);
        $destinations = $query->paginate($perPage)->withQueryString();
        $categories = ['Alam', 'Budaya & Sejarah', 'Alam dan Budaya', 'Religi', 'Alam dan Religi', 'Budaya'];

        // --- Trending Logic (Always loaded to align with AlpineJS SPA tab switcher) ---
        $mode = \App\Models\AppSetting::get('trending_mode', 'manual');
        $manualList = \App\Models\AppSetting::get('trending_list', []);
        
        $trendingDestinations = [];
        if ($mode === 'manual' && !empty($manualList)) {
            $ids = array_map('strval', $manualList);
            $destinationsData = MongoDestination::whereIn('_id', $ids)->get()->keyBy(function($dest) {
                return (string)$dest->_id;
            });
            $trendingDestinations = collect($manualList)->map(function($id) use ($destinationsData) {
                $dest = $destinationsData->get((string)$id);
                if ($dest) $dest->id_str = (string)$dest->_id;
                return $dest;
            })->filter()->values();
        } else {
            $trendingDestinations = MongoDestination::where('is_active', true)
                ->get()
                ->sortByDesc(function($dest) {
                    $reviewsCount    = $dest->total_reviews ?? 0;
                    $avgRating       = $dest->average_rating ?? 0;
                    // Sentiment bonus: range -100 sd +100
                    // Bobot kecil (x0.5) agar tidak mendominasi volume ulasan
                    $sentimentBonus  = ($dest->sentiment_score ?? 0) * 0.5;

                    // Formula: (Jumlah Ulasan x10) + (Rating x10) + Sentiment Bonus
                    return ($reviewsCount * 10) + ($avgRating * 10) + $sentimentBonus;
                })
                ->take(10)
                ->map(function($dest) {
                    $dest->id_str = (string)$dest->_id;
                    return $dest;
                })->values();
        }

        $cachedStats = \Illuminate\Support\Facades\Cache::remember('admin.destinations.trending_stats', now()->addMinutes(15), function () {
            return [
                'stats' => $this->buildTrendingStats(),
                'trendChartData' => $this->buildWeeklyReviewTrend(),
            ];
        });

        $trendingData = [
            'mode' => $mode,
            'trendingDestinations' => $trendingDestinations,
            'stats' => $cachedStats['stats'],
            'trendChartData' => $cachedStats['trendChartData'],
        ];

        return view('admin.destinations.index', array_merge([
            'destinations' => $destinations,
            'categories' => $categories,
            'activeTab' => $activeTab
        ], $trendingData));
    }

    /**
     * Build real trending stats from MongoDB collections.
     */
    private function buildTrendingStats(): array
    {
        $now   = now();
        $thisWeekStart = $now->copy()->startOfWeek();
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd   = $now->copy()->subWeek()->endOfWeek();

        // --- Total Reviews (collection: ratings) ---
        $totalReview = (int) MongoReview::count();

        $reviewThisWeek = (int) MongoReview::where('created_at', '>=', $thisWeekStart)->count();
        $reviewLastWeek = (int) MongoReview::where('created_at', '>=', $lastWeekStart)
                            ->where('created_at', '<=', $lastWeekEnd)->count();
        $reviewIncrease = $reviewLastWeek > 0
            ? (int) round((($reviewThisWeek - $reviewLastWeek) / (float) $reviewLastWeek) * 100)
            : ($reviewThisWeek > 0 ? 100 : 0);

        // --- Total Wishlist (collection: favorites) ---
        $totalWishlist = (int) DB::connection('mongodb')
                            ->table('favorites')->count();

        $wishlistThisWeek = (int) DB::connection('mongodb')
                            ->table('favorites')
                            ->where('created_at', '>=', $thisWeekStart)->count();
        $wishlistLastWeek = (int) DB::connection('mongodb')
                            ->table('favorites')
                            ->where('created_at', '>=', $lastWeekStart)
                            ->where('created_at', '<=', $lastWeekEnd)->count();
        $wishlistIncrease = $wishlistLastWeek > 0
            ? (int) round((($wishlistThisWeek - $wishlistLastWeek) / (float) $wishlistLastWeek) * 100)
            : ($wishlistThisWeek > 0 ? 100 : 0);

        // --- Total Active Destinations sebagai pengganti search ---
        $totalActive = (int) MongoDestination::where('is_active', true)->count();

        $activeThisWeek = (int) MongoDestination::where('is_active', true)
                            ->where('created_at', '>=', $thisWeekStart)->count();
        $activeLastWeek = (int) MongoDestination::where('is_active', true)
                            ->where('created_at', '>=', $lastWeekStart)
                            ->where('created_at', '<=', $lastWeekEnd)->count();
        $activeIncrease = $activeLastWeek > 0
            ? (int) round((($activeThisWeek - $activeLastWeek) / (float) $activeLastWeek) * 100)
            : ($activeThisWeek > 0 ? 100 : 0);

        return [
            'total_destinations' => $totalActive,
            'total_wishlist'     => $totalWishlist,
            'total_review'       => $totalReview,
            'destinations_increase' => $activeIncrease,
            'wishlist_increase'  => $wishlistIncrease,
            'review_increase'    => $reviewIncrease,
        ];
    }

    /**
     * Build weekly review trend data (last 7 days) from MongoDB.
     */
    private function buildWeeklyReviewTrend(): array
    {
        $labels = [];
        $data   = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($i = 6; $i >= 0; $i--) {
            $date  = now()->subDays($i)->startOfDay();
            $end   = now()->subDays($i)->endOfDay();
            $count = MongoReview::where('created_at', '>=', $date)
                        ->where('created_at', '<=', $end)
                        ->count();

            $labels[] = $dayNames[$date->dayOfWeek];
            $data[]   = $count;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = ['Alam', 'Budaya & Sejarah', 'Alam dan Budaya', 'Religi', 'Alam dan Religi', 'Budaya'];

        return view('admin.destinations.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store destination into MongoDB
     */
    public function store(Request $request)
    {
        try {
            Log::info('Destination store attempt', $request->except(['thumbnail', 'images']));
            
            $validated = $request->validate([
                'name'          => 'required|string|min:3|max:200',
                'description'   => 'required|string|min:10|max:5000',
                'location'      => 'required|string|max:500',
                'category'      => 'required|in:Alam,Budaya & Sejarah,Alam dan Budaya,Religi,Alam dan Religi,Budaya',
                'latitude'      => 'nullable|numeric|between:-90,90',
                'longitude'     => 'nullable|numeric|between:-180,180',
                'facilities'    => 'nullable|string',
                'thumbnail'     => 'required',
                'images.*'      => 'nullable',
                'opening_hours' => 'nullable|string|max:255',
                'ticket_price'  => 'nullable|string|max:255',
                'best_time'     => 'nullable|string|max:255',
                'start_time'    => 'nullable|integer|min:0',
            ], [
                'name.required'        => 'Nama destinasi wajib diisi.',
                'name.min'             => 'Nama destinasi minimal 3 karakter.',
                'description.required' => 'Deskripsi wajib diisi.',
                'description.min'      => 'Deskripsi minimal 10 karakter.',
                'location.required'    => 'Lokasi wajib diisi.',
                'category.required'    => 'Kategori wajib dipilih.',
                'thumbnail.required'   => 'Media utama (thumbnail) wajib diunggah.',
            ]);

            $destination = new MongoDestination();
            $destination->name = $validated['name'];
            $destination->description = $validated['description'];
            $destination->location = $validated['location'];
            $destination->category = $validated['category'];
            $destination->latitude  = isset($validated['latitude'])  ? (float) $validated['latitude']  : null;
            $destination->longitude = isset($validated['longitude']) ? (float) $validated['longitude'] : null;
            
            // Normalisasi facilities: handle input koma-separated dari form
            $destination->facilities = $this->parseFacilitiesInput($request->facilities);
            
            $destination->opening_hours = $validated['opening_hours'] ?? '08:00 - 17:00';
            $destination->ticket_price = $validated['ticket_price'] ?? 'Gratis';
            $destination->best_time = $validated['best_time'] ?? 'Kapan saja';
            $destination->video_duration = (int) ($validated['video_duration'] ?? 10);
            $destination->video_autoplay = $request->boolean('video_autoplay', true);
            $destination->video_loop = $request->boolean('video_loop', true);
            $destination->video_wait_until_ready = $request->boolean('video_wait_until_ready', true);

            $destination->is_active = true;
            $destination->is_featured = false;
            $destination->admin_id = $this->admin->id;

            $currentImages = [];

            // Upload thumbnail
            if ($request->filled('thumbnail') && is_string($request->input('thumbnail')) && (str_starts_with($request->input('thumbnail'), 'http://') || str_starts_with($request->input('thumbnail'), 'https://'))) {
                $currentImages[] = $request->input('thumbnail');
            } elseif ($request->hasFile('thumbnail')) {
                $path = $this->processImage($request->file('thumbnail'), 'destinations');
                if ($path) {
                    $currentImages[] = $path;
                }
            }

            // Upload additional images
            if ($request->filled('images')) {
                foreach ($request->input('images') as $img) {
                    if (is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                        $currentImages[] = $img;
                    }
                }
            }
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $this->processImage($file, 'destinations');
                    if ($path) {
                        $currentImages[] = $path;
                    }
                }
            }

            $destination->images = $currentImages;
            $saved = $destination->save();

            if ($saved) {
                Log::info('Destination saved to MongoDB', ['id' => (string)$destination->_id]);
            } else {
                Log::warning('Destination save() returned false');
            }

            $this->logActivity('create_mongo', 'destination', (string)$destination->_id, null, $destination->toArray());
            $this->clearDashboardCache();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Destinasi berhasil ditambahkan',
                    'destination' => $destination
                ]);
            }

            return redirect()
                ->route('admin.destinations.index')
                ->with('success', 'Destinasi berhasil ditambahkan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat kesalahan validasi pada formulir.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error creating destination in Mongo: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', 'Error creating destination: ' . $e->getMessage());
        }
    }

    public function edit(string $id, Request $request)
    {
        $destination = MongoDestination::findOrFail($id);
        $categories = ['Alam', 'Budaya & Sejarah', 'Alam dan Budaya', 'Religi', 'Alam dan Religi', 'Budaya'];

        if ($request->ajax() || $request->wantsJson()) {
            if ($destination->images && is_array($destination->images)) {
                $destination->images_url = array_map(function($img) {
                    $media = get_media_info($img);
                    return $media['url'];
                }, $destination->images);
                $destination->images_data = array_map(function($img) {
                    $media = get_media_info($img);
                    return [
                        'path' => is_array($img) ? ($img['url'] ?? '') : $img,
                        'url' => $media['url'],
                        'type' => $media['type'],
                    ];
                }, $destination->images);
            }
            return response()->json($destination);
        }

        return view('admin.destinations.edit', [
            'destination' => $destination,
            'categories' => $categories,
        ]);
    }

    /**
     * Update destination in MongoDB
     */
    public function update(Request $request, string $id)
    {
        try {
            $destination = MongoDestination::findOrFail($id);

            $validated = $request->validate([
                'name'          => 'required|string|min:3|max:200',
                'description'   => 'required|string|min:10|max:5000',
                'location'      => 'required|string|max:500',
                'category'      => 'required|in:Alam,Budaya & Sejarah,Alam dan Budaya,Religi,Alam dan Religi,Budaya',
                'latitude'      => 'nullable|numeric|between:-90,90',
                'longitude'     => 'nullable|numeric|between:-180,180',
                'facilities'    => 'nullable|string',
                'thumbnail'     => 'nullable',
                'images.*'      => 'nullable',
                'opening_hours' => 'nullable|string|max:255',
                'ticket_price'  => 'nullable|string|max:255',
                'best_time'     => 'nullable|string|max:255',
                'start_time'    => 'nullable|integer|min:0',
                'end_time'      => 'nullable|integer|min:0',
            ], [
                'name.required'        => 'Nama destinasi wajib diisi.',
                'name.min'             => 'Nama destinasi minimal 3 karakter.',
                'description.required' => 'Deskripsi wajib diisi.',
                'description.min'      => 'Deskripsi minimal 10 karakter.',
                'location.required'    => 'Lokasi wajib diisi.',
                'category.required'    => 'Kategori wajib dipilih.',
            ]);

            $oldValues = $destination->toArray();

            $destination->name = $validated['name'];
            $destination->description = $validated['description'];
            $destination->location = $validated['location'];
            $destination->category = $validated['category'];
            $destination->latitude  = isset($validated['latitude'])  ? (float) $validated['latitude']  : $destination->latitude;
            $destination->longitude = isset($validated['longitude']) ? (float) $validated['longitude'] : $destination->longitude;

            if ($request->has('facilities')) {
                $destination->facilities = $this->parseFacilitiesInput($request->facilities);
            }

            $destination->opening_hours = $validated['opening_hours'] ?? $destination->opening_hours;
            $destination->ticket_price = $validated['ticket_price'] ?? $destination->ticket_price;
            $destination->best_time = $validated['best_time'] ?? $destination->best_time;
            $destination->video_duration = (int) ($validated['video_duration'] ?? ($destination->video_duration ?? 10));
            $destination->video_autoplay = $request->boolean('video_autoplay', $destination->video_autoplay ?? true);
            $destination->video_loop = $request->boolean('video_loop', $destination->video_loop ?? true);
            $destination->video_wait_until_ready = $request->boolean('video_wait_until_ready', $destination->video_wait_until_ready ?? true);

            // Update status aktif/nonaktif
            if ($request->has('is_active')) {
                $destination->is_active = $request->boolean('is_active');
            }

            $currentImages = $destination->images ?? [];

            $deleteImages = $request->input('delete_images', []);
            if (!empty($deleteImages) && is_array($deleteImages)) {
                foreach ($deleteImages as $delImg) {
                    $this->deleteFile($delImg);
                    $currentImages = array_filter($currentImages, function($img) use ($delImg) {
                        $imgUrl = is_array($img) ? $img['url'] : $img;
                        return !$this->pathsMatch($imgUrl, $delImg);
                    });
                }
                $currentImages = array_values($currentImages);
            }

            // --- Logika Update Thumbnail (Index 0) ---
            if ($request->filled('thumbnail') && is_string($request->input('thumbnail')) && (str_starts_with($request->input('thumbnail'), 'http://') || str_starts_with($request->input('thumbnail'), 'https://'))) {
                $newThumb = $request->input('thumbnail');
                if (count($currentImages) > 0) {
                    // Hapus thumbnail lama dari storage
                    $oldThumb = is_array($currentImages[0]) ? ($currentImages[0]['url'] ?? $currentImages[0]) : $currentImages[0];
                    $this->deleteFile($oldThumb);
                    $currentImages[0] = $newThumb;
                } else {
                    array_unshift($currentImages, $newThumb);
                }
            } elseif ($request->hasFile('thumbnail')) {
                $newThumb = $this->processImage($request->file('thumbnail'), 'destinations');
                if ($newThumb) {
                    if (count($currentImages) > 0) {
                        // Hapus thumbnail lama dari storage
                        $oldThumb = is_array($currentImages[0]) ? ($currentImages[0]['url'] ?? $currentImages[0]) : $currentImages[0];
                        $this->deleteFile($oldThumb);
                        $currentImages[0] = $newThumb;
                    } else {
                        array_unshift($currentImages, $newThumb);
                    }
                }
            }

            // --- Logika Tambah Gambar/Video ke Gallery ---
            if ($request->filled('images')) {
                foreach ($request->input('images') as $img) {
                    if (is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                        $currentImages[] = $img;
                    }
                }
            }
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $this->processImage($file, 'destinations');
                    if ($path) {
                        $currentImages[] = $path;
                    }
                }
            }

            $destination->images = $currentImages;
            $destination->save();

            $this->clearDashboardCache();

            // Wrap logActivity in try-catch to prevent it from breaking the response
            try {
                $this->logActivity('update_mongo', 'destination', (string)$destination->_id, $oldValues, $destination->toArray());
            } catch (\Exception $logEx) {
                Log::warning('Log activity failed: ' . $logEx->getMessage());
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Destinasi berhasil diperbarui',
                    'destination' => $destination
                ]);
            }

            return redirect()
                ->route('admin.destinations.index')
                ->with('success', 'Destinasi berhasil diperbarui');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat kesalahan validasi pada formulir.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating destination in Mongo: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', 'Error updating destination: ' . $e->getMessage());
        }
    }

    /**
     * Delete destination from MongoDB (soft delete)
     */
    public function destroy(string $id)
    {
        try {
            $destination = MongoDestination::findOrFail($id);

            // Soft delete — hanya set deleted_at, file fisik tetap ada
            $destination->delete();

            // Log action
            $this->logActivity('delete_mongo', 'destination', $id);
            $this->clearDashboardCache();

            return redirect()
                ->route('admin.destinations.index')
                ->with('success', 'Destinasi berhasil dihapus');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting destination: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete destination and its files from MongoDB
     */
    public function forceDestroy(string $id)
    {
        try {
            $destination = MongoDestination::withTrashed()->findOrFail($id);

            // Hapus file fisik hanya saat force delete
            if ($destination->images) {
                foreach ($destination->images as $img) {
                    $this->deleteFile($img);
                }
            }

            $destination->forceDelete();

            $this->logActivity('force_delete_mongo', 'destination', $id);
            $this->clearDashboardCache();

            return redirect()
                ->route('admin.destinations.index')
                ->with('success', 'Destinasi berhasil dihapus permanen');

        } catch (\Exception $e) {
            return back()->with('error', 'Error force deleting destination: ' . $e->getMessage());
        }
    }


    /**
     * Toggle active status in MongoDB
     */
    public function toggleStatus(string $id)
    {
        $destination = MongoDestination::findOrFail($id);
        $oldValue = $destination->is_active;
        $destination->is_active = !$oldValue;
        $destination->save();

        $this->logActivity(
            'update_status_mongo',
            'destination',
            $id,
            ['is_active' => $oldValue],
            ['is_active' => $destination->is_active]
        );

        return back()->with('success', 'Status destinasi berhasil diperbarui');
    }

    // --- Trending Methods ---

    public function updateTrendingMode(Request $request)
    {
        $request->validate(['mode' => 'required|in:manual,automatic']);
        \App\Models\AppSetting::set('trending_mode', $request->mode);
        return response()->json(['success' => true, 'message' => 'Mode trending diperbarui']);
    }

    public function updateTrendingOrder(Request $request)
    {
        try {
            $request->validate(['orders' => 'required|array']);
            $orders = array_map('strval', $request->orders);
            \App\Models\AppSetting::set('trending_list', $orders, 'json');
            return response()->json(['success' => true, 'message' => 'Urutan trending diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function addTrendingDestination(Request $request)
    {
        $request->validate(['destination_id' => 'required']);
        $currentList = \App\Models\AppSetting::get('trending_list', []);
        $id = (string)$request->destination_id;
        if (!in_array($id, $currentList)) {
            $currentList[] = $id;
            \App\Models\AppSetting::set('trending_list', $currentList, 'json');
        }
        return response()->json(['success' => true, 'message' => 'Destinasi ditambahkan ke trending']);
    }

    public function removeTrendingDestination(Request $request, $id)
    {
        $currentList = \App\Models\AppSetting::get('trending_list', []);
        $id = (string)$id;
        $newList = array_values(array_filter($currentList, fn($item) => (string)$item !== $id));
        \App\Models\AppSetting::set('trending_list', $newList, 'json');
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Destinasi dihapus dari trending']);
        }
        
        return redirect()->back()->with('success', 'Destinasi berhasil dihapus dari trending');
    }

    public function resetTrendingToAutomatic()
    {
        \App\Models\AppSetting::set('trending_mode', 'automatic');
        return response()->json(['success' => true, 'message' => 'Sistem dikembalikan ke mode otomatis']);
    }

    public function searchTrendingDestinations(Request $request)
    {
        $search = $request->query('q');
        $destinations = MongoDestination::where('name', 'regexp', "/{$search}/i")
            ->limit(5)
            ->get()
            ->map(function($dest) {
                return [
                    'id_str' => (string)$dest->_id,
                    'name' => $dest->name,
                    'location' => $dest->location,
                    'category' => $dest->category,
                    'average_rating' => $dest->average_rating,
                    'images' => $dest->images
                ];
            });
        return response()->json($destinations);
    }

    /**
     * Normalisasi input facilities dari berbagai format menjadi array PHP bersih.
     * Menangani:
     *   - String koma-separated dari form: "Toilet, Parkir"
     *   - JSON string dari data lama di DB: "[\"Toilet Umum\"]"
     *   - Array PHP (jika sudah benar)
     */
    private function parseFacilitiesInput($input): array
    {
        if (empty($input)) {
            return [];
        }

        // Jika sudah berupa array PHP
        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }

        if (!is_string($input)) {
            return [];
        }

        $trimmed = trim($input);

        // Coba decode sebagai JSON (format lama: "[\"Toilet Umum\"]")
        if (str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('trim', $decoded)));
            }
        }

        // Format normal dari form: koma-separated string
        return array_values(array_filter(array_map('trim', explode(',', $trimmed))));
    }
}
