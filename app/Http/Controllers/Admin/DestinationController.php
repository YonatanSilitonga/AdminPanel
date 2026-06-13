<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoDestination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'thumbnail' => 'required|image|mimes:jpeg,png,webp|max:10240',
            'images.*' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
            'opening_hours' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|string|max:255',
            'best_time' => 'nullable|string|max:255',
        ]);

        try {
            $destination = new MongoDestination();
            $destination->name = $validated['name'];
            $destination->description = $validated['description'];
            $destination->location = $validated['location'];
            $destination->category = $validated['category'];
            $destination->latitude = (float) $validated['latitude'];
            $destination->longitude = (float) $validated['longitude'];
            
            // Normalisasi facilities: handle input koma-separated dari form
            $destination->facilities = $this->parseFacilitiesInput($request->facilities);
            
            $destination->opening_hours = $validated['opening_hours'] ?? '08:00 - 17:00';
            $destination->ticket_price = $validated['ticket_price'] ?? 'Gratis';
            $destination->best_time = $validated['best_time'] ?? 'Kapan saja';

            $destination->is_active = true;
            $destination->is_featured = false;
            $destination->admin_id = $this->admin->id;

            $images = [];

            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                $path = $this->processImage($request->file('thumbnail'), 'destinations');
                if ($path) $images[] = $path; // Simpan relative path
            }

            // Upload additional images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $this->processImage($file, 'destinations');
                    if ($path) $images[] = $path; // Simpan relative path
                }
            }

            $destination->images = $images;
            $saved = $destination->save();

            if ($saved) {
                Log::info('Destination saved to MongoDB', ['id' => (string)$destination->_id]);
            } else {
                Log::warning('Destination save() returned false');
            }

            $this->logActivity('create_mongo', 'destination', (string)$destination->_id, null, $destination->toArray());

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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
            'images.*' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
            'opening_hours' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|string|max:255',
            'best_time' => 'nullable|string|max:255',
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
                $destination->facilities = $this->parseFacilitiesInput($request->facilities);
            }

            $destination->opening_hours = $validated['opening_hours'] ?? $destination->opening_hours;
            $destination->ticket_price = $validated['ticket_price'] ?? $destination->ticket_price;
            $destination->best_time = $validated['best_time'] ?? $destination->best_time;

            $currentImages = $destination->images ?? [];

            // --- Logika Update Thumbnail (Index 0) ---
            if ($request->hasFile('thumbnail')) {
                $newThumb = $this->processImage($request->file('thumbnail'), 'destinations');
                if ($newThumb) {
                    if (count($currentImages) > 0) {
                        // Hapus thumbnail lama dari storage
                        $this->deleteFile($currentImages[0]);
                        $currentImages[0] = $newThumb;
                    } else {
                        array_unshift($currentImages, $newThumb);
                    }
                }
            }

            // --- Logika Tambah Gambar ke Gallery ---
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $this->processImage($file, 'destinations');
                    if ($path) {
                        $currentImages[] = $path; // Tambahkan ke akhir array
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

    public function removeTrendingDestination($id)
    {
        $currentList = \App\Models\AppSetting::get('trending_list', []);
        $id = (string)$id;
        $newList = array_values(array_filter($currentList, fn($item) => (string)$item !== $id));
        \App\Models\AppSetting::set('trending_list', $newList, 'json');
        return response()->json(['success' => true, 'message' => 'Destinasi dihapus dari trending']);
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
