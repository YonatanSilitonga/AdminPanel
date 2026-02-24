<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DestinationController extends BaseAdminController
{
    /**
     * Display list of destinations
     */
    public function index(Request $request)
    {
        $query = Destination::query();

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

        $destinations = $query->with('galleryImages', 'facilities')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $categories = ['park', 'beach', 'museum', 'historical', 'nature', 'cultural'];

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
        $categories = ['park', 'beach', 'museum', 'historical', 'nature', 'cultural'];

        return view('admin.destinations.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store destination
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:200',
            'description' => 'required|string|min:10|max:500',
            'long_description' => 'nullable|string|max:5000',
            'category' => 'required|in:park,beach,museum,historical,nature,cultural',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'thumbnail' => 'required|image|mimes:jpeg,png,webp|max:5120',
            'cover' => 'required|image|mimes:jpeg,png,webp|max:5120',
            'rating' => 'nullable|numeric|between:1,5',
        ]);

        try {
            $destination = new Destination();
            $destination->name = $validated['name'];
            $destination->slug = Str::slug($validated['name']);
            $destination->description = $validated['description'];
            $destination->long_description = $validated['long_description'];
            $destination->category = $validated['category'];
            $destination->latitude = $validated['latitude'];
            $destination->longitude = $validated['longitude'];
            $destination->rating = $validated['rating'] ?? 0;
            $destination->admin_id = $this->admin->id;
            $destination->is_active = true;

            // Upload thumbnai
            if ($request->hasFile('thumbnail')) {
                $destination->thumbnail_url = $this->processImage(
                    $request->file('thumbnail'),
                    'destinations/thumbnails'
                );
            }

            // Upload cover
            if ($request->hasFile('cover')) {
                $destination->cover_url = $this->processImage(
                    $request->file('cover'),
                    'destinations/covers'
                );
            }

            $destination->save();

            // Log action
            $this->logActivity('create', 'destination', $destination->id, null, $destination->toArray());

            return redirect()
                ->route('admin.destinations.edit', $destination)
                ->with('success', 'Destination created successfully');

        } catch (\Exception $e) {
            Log::error('Error creating destination: ' . $e->getMessage());
            return back()->with('error', 'Error creating destination: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit(Destination $destination)
    {
        $categories = ['park', 'beach', 'museum', 'historical', 'nature', 'cultural'];

        return view('admin.destinations.edit', [
            'destination' => $destination->load('galleryImages', 'facilities'),
            'categories' => $categories,
        ]);
    }

    /**
     * Update destination
     */
    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:200',
            'description' => 'required|string|min:10|max:500',
            'long_description' => 'nullable|string|max:5000',
            'category' => 'required|in:park,beach,museum,historical,nature,cultural',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'cover' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'rating' => 'nullable|numeric|between:1,5',
        ]);

        try {
            $oldValues = $destination->toArray();

            $destination->name = $validated['name'];
            $destination->slug = Str::slug($validated['name']);
            $destination->description = $validated['description'];
            $destination->long_description = $validated['long_description'];
            $destination->category = $validated['category'];
            $destination->latitude = $validated['latitude'];
            $destination->longitude = $validated['longitude'];
            $destination->rating = $validated['rating'] ?? $destination->rating;

            // Update thumbnail
            if ($request->hasFile('thumbnail')) {
                $this->deleteFile($destination->thumbnail_url);
                $destination->thumbnail_url = $this->processImage(
                    $request->file('thumbnail'),
                    'destinations/thumbnails'
                );
            }

            // Update cover
            if ($request->hasFile('cover')) {
                $this->deleteFile($destination->cover_url);
                $destination->cover_url = $this->processImage(
                    $request->file('cover'),
                    'destinations/covers'
                );
            }

            $destination->save();

            // Log action
            $this->logActivity('update', 'destination', $destination->id, $oldValues, $destination->toArray());

            return redirect()
                ->route('admin.destinations.edit', $destination)
                ->with('success', 'Destination updated successfully');

        } catch (\Exception $e) {
            Log::error('Error updating destination: ' . $e->getMessage());
            return back()->with('error', 'Error updating destination: ' . $e->getMessage());
        }
    }

    /**
     * Delete destination (soft delete)
     */
    public function destroy(Destination $destination)
    {
        try {
            $destination->delete();

            // Log action
            $this->logActivity('soft_delete', 'destination', $destination->id);

            return redirect()
                ->route('admin.destinations.index')
                ->with('success', 'Destination deleted successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting destination: ' . $e->getMessage());
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Destination $destination)
    {
        $oldValue = $destination->is_featured;
        $destination->update(['is_featured' => !$destination->is_featured]);

        $this->logActivity(
            'update_featured',
            'destination',
            $destination->id,
            ['is_featured' => $oldValue],
            ['is_featured' => $destination->is_featured]
        );

        return back()->with('success', 'Featured status updated');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Destination $destination)
    {
        $oldValue = $destination->is_active;
        $destination->update(['is_active' => !$destination->is_active]);

        $this->logActivity(
            'update_status',
            'destination',
            $destination->id,
            ['is_active' => $oldValue],
            ['is_active' => $destination->is_active]
        );

        return back()->with('success', 'Status updated');
    }
}
