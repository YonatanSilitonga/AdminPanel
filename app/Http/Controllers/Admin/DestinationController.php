<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoDestination;
use Illuminate\Http\Request;
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
        $categories = ['park', 'beach', 'museum', 'historical', 'nature', 'cultural', 'religi'];

        // --- Trending Logic (Only if tab is trending) ---
        $trendingData = [];
        if ($activeTab === 'trending') {
            $mode = \App\Models\AppSetting::get('trending_mode', 'manual');
            $manualList = \App\Models\AppSetting::get('trending_list', []);
            
            $trendingDestinations = [];
            if ($mode === 'manual' && !empty($manualList)) {
                $trendingDestinations = collect($manualList)->map(function($id) {
                    $dest = MongoDestination::find((string)$id);
                    if ($dest) $dest->id_str = (string)$dest->_id;
                    return $dest;
                })->filter()->values();
            } else {
                $trendingDestinations = MongoDestination::where('is_active', true)
                    ->get()
                    ->sortByDesc(function($dest) {
                        return ($dest->total_reviews * 10) + $dest->average_rating;
                    })
                    ->take(10)
                    ->map(function($dest) {
                        $dest->id_str = (string)$dest->_id;
                        return $dest;
                    })->values();
            }

            $trendingData = [
                'mode' => $mode,
                'trendingDestinations' => $trendingDestinations,
                'stats' => [
                    'total_search' => 7842,
                    'total_wishlist' => 1543,
                    'total_review' => 842,
                    'search_increase' => 12,
                    'wishlist_increase' => 12,
                    'review_increase' => 18
                ]
            ];
        }

        return view('admin.destinations.index', array_merge([
            'destinations' => $destinations,
            'categories' => $categories,
            'activeTab' => $activeTab
        ], $trendingData));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = ['park', 'beach', 'museum', 'historical', 'nature', 'cultural', 'religi'];

        return view('admin.destinations.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store destination into MongoDB
     */
    public function store(Request $request)
    {
        Log::info('Destination store attempt', $request->except(['thumbnail', 'images']));
        
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:200',
            'description' => 'required|string|min:10|max:500',
            'location' => 'required|string|max:500',
            'category' => 'required|in:park,beach,museum,historical,nature,cultural,religi',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'facilities' => 'nullable|string',
            'thumbnail' => 'required|file|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm,ogg|max:51200',
            'images.*' => 'nullable|file|mimes:jpeg,png,webp,jpg,mp4,mov,avi,webm,ogg|max:51200', // Max 50MB
            'opening_hours' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|string|max:255',
            'best_time' => 'nullable|string|max:255',
            'start_time' => 'nullable|integer|min:0',
        ]);

        try {
            $destination = new MongoDestination();
            $destination->name = $validated['name'];
            $destination->description = $validated['description'];
            $destination->location = $validated['location'];
            $destination->category = $validated['category'];
            $destination->latitude = (float) $validated['latitude'];
            $destination->longitude = (float) $validated['longitude'];
            
            $facilities = [];
            if (!empty($request->facilities)) {
                $facilities = array_map('trim', explode(',', $request->facilities));
            }
            $destination->facilities = array_values(array_filter($facilities));
            
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
            if ($request->hasFile('thumbnail')) {
                $path = $this->processImage($request->file('thumbnail'), 'destinations');
                if ($path) {
                    $currentImages[] = $path;
                }
            }

            // Upload additional images
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

            return redirect()
                ->route('admin.destinations.index')
                ->with('success', 'Destinasi berhasil ditambahkan');

        } catch (\Exception $e) {
            Log::error('Error creating destination in Mongo: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error creating destination: ' . $e->getMessage());
        }
    }

    public function edit(string $id, Request $request)
    {
        $destination = MongoDestination::findOrFail($id);
        $categories = ['park', 'beach', 'museum', 'historical', 'nature', 'cultural', 'religi'];

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
        $destination = MongoDestination::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|min:3|max:200',
            'description' => 'required|string|min:10|max:500',
            'location' => 'required|string|max:500',
            'category' => 'required|in:park,beach,museum,historical,nature,cultural,religi',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'facilities' => 'nullable|string',
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm,ogg|max:51200',
            'images.*' => 'nullable|file|mimes:jpeg,png,webp,jpg,mp4,mov,avi,webm,ogg|max:51200', // Max 50MB
            'opening_hours' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|string|max:255',
            'best_time' => 'nullable|string|max:255',
            'start_time' => 'nullable|integer|min:0',
            'end_time' => 'nullable|integer|min:0',
        ]);

        try {
            $oldValues = $destination->toArray();

            $destination->name = $validated['name'];
            $destination->description = $validated['description'];
            $destination->location = $validated['location'];
            $destination->category = $validated['category'];
            $destination->latitude = (float) $validated['latitude'];
            $destination->longitude = (float) $validated['longitude'];

            if ($request->has('facilities')) {
                $facilities = [];
                if (!empty($request->facilities)) {
                    $facilities = array_map('trim', explode(',', $request->facilities));
                }
                $destination->facilities = array_values(array_filter($facilities));
            }

            $destination->opening_hours = $validated['opening_hours'] ?? $destination->opening_hours;
            $destination->ticket_price = $validated['ticket_price'] ?? $destination->ticket_price;
            $destination->best_time = $validated['best_time'] ?? $destination->best_time;
            $destination->video_duration = (int) ($validated['video_duration'] ?? ($destination->video_duration ?? 10));
            $destination->video_autoplay = $request->boolean('video_autoplay', $destination->video_autoplay ?? true);
            $destination->video_loop = $request->boolean('video_loop', $destination->video_loop ?? true);
            $destination->video_wait_until_ready = $request->boolean('video_wait_until_ready', $destination->video_wait_until_ready ?? true);

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
            if ($request->hasFile('thumbnail')) {
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

            // Wrap logActivity in try-catch to prevent it from breaking the response
            try {
                $this->logActivity('update_mongo', 'destination', (string)$destination->_id, $oldValues, $destination->toArray());
            } catch (\Exception $logEx) {
                Log::warning('Log activity failed: ' . $logEx->getMessage());
            }

            if ($request->ajax() || $request->wantsJson()) {
                session()->flash('success', 'Destinasi berhasil diperbarui');
                return response()->json([
                    'success' => true,
                    'message' => 'Destinasi berhasil diperbarui',
                    'destination' => $destination
                ]);
            }

            return redirect()
                ->route('admin.destinations.index')
                ->with('success', 'Destinasi berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Error updating destination in Mongo: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', 'Error updating destination: ' . $e->getMessage());
        }
    }

    /**
     * Delete destination from MongoDB
     */
    public function destroy(string $id)
    {
        try {
            $destination = MongoDestination::findOrFail($id);
            
            // Delete files
            if ($destination->images) {
                foreach ($destination->images as $img) {
                    $this->deleteFile($img);
                }
            }
            
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
}
