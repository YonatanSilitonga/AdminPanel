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
     * Display list of destinations from MongoDB
     */
    public function index(Request $request)
    {
        Log::info('Destination index accessed', ['count' => MongoDestination::count()]);
        $query = MongoDestination::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured === 'true');
        }

        $destinations = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        $categories = ['park', 'beach', 'museum', 'historical', 'nature', 'cultural', 'religi'];

        return view('admin.destinations.index', [
            'destinations' => $destinations,
            'categories' => $categories,
        ]);
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
            'average_rating' => 'nullable|numeric|between:0,5',
            'total_reviews' => 'nullable|integer|min:0',
            'facilities' => 'nullable|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,webp|max:5120',
            'images.*' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        try {
            $destination = new MongoDestination();
            $destination->name = $validated['name'];
            $destination->description = $validated['description'];
            $destination->location = $validated['location'];
            $destination->category = $validated['category'];
            $destination->latitude = (float) $validated['latitude'];
            $destination->longitude = (float) $validated['longitude'];
            $destination->average_rating = (float) ($request->average_rating ?? 0);
            $destination->total_reviews = (int) ($request->total_reviews ?? 0);
            
            $facilities = [];
            if (!empty($request->facilities)) {
                $facilities = array_map('trim', explode(',', $request->facilities));
            }
            $destination->facilities = array_values(array_filter($facilities));

            $destination->is_active = true;
            $destination->is_featured = false;

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
                ->with('success', 'Destination created in MongoDB successfully');

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
            'average_rating' => 'nullable|numeric|between:0,5',
            'total_reviews' => 'nullable|integer|min:0',
            'facilities' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        try {
            $oldValues = $destination->toArray();

            $destination->name = $validated['name'];
            $destination->description = $validated['description'];
            $destination->location = $validated['location'];
            $destination->category = $validated['category'];
            $destination->latitude = (float) $validated['latitude'];
            $destination->longitude = (float) $validated['longitude'];

            if ($request->has('average_rating')) {
                $destination->average_rating = (float) $request->average_rating;
            }
            if ($request->has('total_reviews')) {
                $destination->total_reviews = (int) $request->total_reviews;
            }
            
            if ($request->has('facilities')) {
                $facilities = [];
                if (!empty($request->facilities)) {
                    $facilities = array_map('trim', explode(',', $request->facilities));
                }
                $destination->facilities = array_values(array_filter($facilities));
            }

            $currentImages = $destination->images ?? [];

            if ($request->hasFile('thumbnail')) {
                $newThumb = $this->processImage($request->file('thumbnail'), 'destinations');
                if (count($currentImages) > 0) {
                    $this->deleteFile($currentImages[0]);
                    $currentImages[0] = $newThumb;
                } else {
                    array_unshift($currentImages, $newThumb);
                }
            }

            $destination->images = $currentImages;
            $destination->save();

            $this->logActivity('update_mongo', 'destination', (string)$destination->_id, $oldValues, $destination->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Destination updated successfully.']);
            }

            return redirect()->route('admin.destinations.index')->with('success', 'Destination updated in MongoDB successfully');

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

            return redirect()
                ->route('admin.destinations.index')
                ->with('success', 'Destination deleted from MongoDB successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting destination: ' . $e->getMessage());
        }
    }

    /**
     * Toggle featured status in MongoDB
     */
    public function toggleFeatured(string $id)
    {
        $destination = MongoDestination::findOrFail($id);
        $oldValue = $destination->is_featured;
        $destination->is_featured = !$oldValue;
        $destination->save();

        $this->logActivity(
            'update_featured_mongo',
            'destination',
            $id,
            ['is_featured' => $oldValue],
            ['is_featured' => $destination->is_featured]
        );

        return back()->with('success', 'Featured status updated in MongoDB');
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

        return back()->with('success', 'Status updated in MongoDB');
    }
}
