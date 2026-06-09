<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoBudaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BudayaController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the budaya contents.
     */
    public function index(Request $request)
    {
        $query = MongoBudaya::query();

        // Search across title and description
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'regexp', "/{$search}/i")
                  ->orWhere('description', 'regexp', "/{$search}/i");
            });
        }

        // Filter by Category
        if ($request->has('category') && !empty($request->category) && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        // Filter by Status
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Advanced Sorting
        $sortColumn = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['name', 'category', 'is_active', 'created_at'];
        
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int)$request->get('per_page', 15);
        $budayas = $query->paginate($perPage)->withQueryString();
        $categories = ['Sejarah', 'Tradisi', 'Rumah Adat', 'Cerita Rakyat', 'Kuliner'];

        return view('admin.budaya.index', compact('budayas', 'categories'));
    }

    /**
     * Normalize budaya media response for the admin UI.
     */
    protected function appendMediaMetadata(MongoBudaya $budaya): MongoBudaya
    {
        if ($budaya->image_url) {
            $budaya->image_url_full = image_url($budaya->image_url);
            $budaya->image_url_type = media_is_video($budaya->image_url) ? 'video' : 'image';
        }

        $images = $budaya->images;
        if (is_string($images)) {
            $images = json_decode($images, true) ?: [];
        }
        if ($images && is_array($images)) {
            $budaya->images_url = array_map(fn ($img) => image_url($img), $images);
            $budaya->images_data = array_map(function ($img) {
                return [
                    'path' => $img,
                    'url' => image_url($img),
                    'type' => media_is_video($img) ? 'video' : 'image',
                ];
            }, $images);
        }

        return $budaya;
    }

    /**
     * Store a newly created budaya in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|in:Sejarah,Tradisi,Rumah Adat,Cerita Rakyat,Kuliner',
                'category_mobile' => 'nullable|string|max:100',
                'location' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'description' => 'required|string',
                'is_active' => 'boolean',
                'video_duration' => 'nullable|integer|min:1|max:600',
                'video_autoplay' => 'nullable|boolean',
                'video_loop' => 'nullable|boolean',
                'video_wait_until_ready' => 'nullable|boolean',
                'images' => 'nullable|array',
                'images.*' => $request->hasFile('images') ? 'file|mimes:jpeg,png,webp,jpg,mp4,mov,avi,webm,ogg|max:51200' : 'string',
                'thumbnail' => 'nullable|' . ($request->hasFile('thumbnail') ? 'file|mimes:jpeg,png,webp,jpg,mp4,mov,avi,webm,ogg|max:51200' : 'string'),
            ]);

            $validated['is_active'] = $request->has('is_active');
            $validated['video_autoplay'] = $request->has('video_autoplay');
            $validated['video_loop'] = $request->has('video_loop');
            $validated['video_wait_until_ready'] = $request->has('video_wait_until_ready');
            $validated['video_duration'] = $validated['video_duration'] ?? null;
            
            $uploadedImages = [];
            if ($request->filled('thumbnail') && is_string($request->input('thumbnail')) && (str_starts_with($request->input('thumbnail'), 'http://') || str_starts_with($request->input('thumbnail'), 'https://'))) {
                $uploadedImages[] = $request->input('thumbnail');
            } elseif ($request->hasFile('thumbnail')) {
                $uploadedImages[] = $this->processImage($request->file('thumbnail'), 'budaya');
            }

            if ($request->filled('images')) {
                foreach ($request->input('images') as $img) {
                    if (is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                        $uploadedImages[] = $img;
                    }
                }
            }
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $uploadedImages[] = $this->processImage($file, 'budaya');
                }
            }
            if (count($uploadedImages) > 0) {
                $validated['image_url'] = $uploadedImages[0];
                $validated['images'] = $uploadedImages;
            }
            
            if (isset($validated['thumbnail'])) {
                unset($validated['thumbnail']);
            }
            
            // Set mobile category shorthand
            if (empty($validated['category_mobile'])) {
                $validated['category_mobile'] = strtoupper($validated['category']);
            }

            $validated['admin_id'] = $this->admin->id;

            $budaya = MongoBudaya::create($validated);
            
            $this->logActivity('create', 'budaya', (string)$budaya->_id, null, $budaya->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil Ditambahkan',
                    'budaya' => $this->appendMediaMetadata($budaya)
                ]);
            }

            $this->clearDashboardCache();
            return redirect()->route('admin.budaya.index')
                ->with('success', 'Berhasil Ditambahkan');
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
            Log::error('Error creating budaya: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan konten: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->with('error', 'Gagal menambahkan konten: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified budaya.
     */
    public function edit(string $id, Request $request)
    {
        $budaya = MongoBudaya::findOrFail($id);
        $budaya->load('admin');
        $this->appendMediaMetadata($budaya);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($budaya);
        }

        return response()->json($budaya);
    }

    /**
     * Update the specified budaya in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $budaya = MongoBudaya::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|in:Sejarah,Tradisi,Rumah Adat,Cerita Rakyat,Kuliner',
                'category_mobile' => 'nullable|string|max:100',
                'location' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'description' => 'required|string',
                'is_active' => 'boolean',
                'video_duration' => 'nullable|integer|min:1|max:600',
                'video_autoplay' => 'nullable|boolean',
                'video_loop' => 'nullable|boolean',
                'video_wait_until_ready' => 'nullable|boolean',
                'images' => 'nullable|array',
                'images.*' => $request->hasFile('images') ? 'file|mimes:jpeg,png,webp,jpg,mp4,mov,avi,webm,ogg|max:51200' : 'string',
                'thumbnail' => 'nullable|' . ($request->hasFile('thumbnail') ? 'file|mimes:jpeg,png,webp,jpg,mp4,mov,avi,webm,ogg|max:51200' : 'string'),
            ]);

            $validated['is_active'] = $request->has('is_active');
            $validated['video_autoplay'] = $request->has('video_autoplay');
            $validated['video_loop'] = $request->has('video_loop');
            $validated['video_wait_until_ready'] = $request->has('video_wait_until_ready');
            $validated['video_duration'] = $validated['video_duration'] ?? null;
            
            $deleteImages = $request->input('delete_images', []);
            $existingImages = $budaya->images ?? [];
            if (is_string($existingImages)) {
                $existingImages = json_decode($existingImages, true) ?: [];
            }
            if ($budaya->image_url && empty($existingImages)) {
                $existingImages = [$budaya->image_url];
            }

            if (!empty($deleteImages) && is_array($deleteImages)) {
                foreach ($deleteImages as $delImg) {
                    $this->deleteFile($delImg);
                    $existingImages = array_filter($existingImages, function($img) use ($delImg) {
                        return !$this->pathsMatch($img, $delImg);
                    });
                }
                $existingImages = array_values($existingImages);
            }

            $uploadedImages = [];
            if ($request->filled('thumbnail') && is_string($request->input('thumbnail')) && (str_starts_with($request->input('thumbnail'), 'http://') || str_starts_with($request->input('thumbnail'), 'https://'))) {
                $path = $request->input('thumbnail');
                if (count($existingImages) > 0) {
                    $this->deleteFile($existingImages[0]);
                    $existingImages[0] = $path;
                } else {
                    array_unshift($existingImages, $path);
                }
            } elseif ($request->hasFile('thumbnail')) {
                $path = $this->processImage($request->file('thumbnail'), 'budaya');
                if ($path) {
                    if (count($existingImages) > 0) {
                        $this->deleteFile($existingImages[0]);
                        $existingImages[0] = $path;
                    } else {
                        array_unshift($existingImages, $path);
                    }
                }
            }

            if ($request->filled('images')) {
                foreach ($request->input('images') as $img) {
                    if (is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                        $uploadedImages[] = $img;
                    }
                }
            }
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $this->processImage($file, 'budaya');
                    if ($path) $uploadedImages[] = $path;
                }
            }
            $existingImages = array_merge($existingImages, $uploadedImages);

            if (count($existingImages) > 0) {
                $validated['image_url'] = $existingImages[0];
                $validated['images'] = $existingImages;
            } else {
                $validated['image_url'] = null;
                $validated['images'] = [];
            }
            
            if (empty($validated['category_mobile'])) {
                $validated['category_mobile'] = strtoupper($validated['category']);
            }
            
            $oldValues = $budaya->toArray();
            $budaya->update($validated);
            
            $this->logActivity('update', 'budaya', (string)$budaya->_id, $oldValues, $budaya->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil Diperbarui',
                    'budaya' => $this->appendMediaMetadata($budaya)
                ]);
            }

            $this->clearDashboardCache();
            return redirect()->route('admin.budaya.index')
                ->with('success', 'Berhasil Diperbarui');
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
            Log::error('Error updating budaya: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui konten: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->with('error', 'Gagal memperbarui konten: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified budaya from storage.
     */
    public function destroy(string $id)
    {
        $budaya = MongoBudaya::findOrFail($id);

        try {
            $this->logActivity('delete', 'budaya', (string)$budaya->_id, $budaya->toArray());
            
            $images = $budaya->images;
            if (is_string($images)) {
                $images = json_decode($images, true) ?: [];
            }
            if ($images && is_array($images)) {
                foreach($images as $img) {
                    $this->deleteFile($img);
                }
            } elseif ($budaya->image_url) {
                $this->deleteFile($budaya->image_url);
            }
            
            $budaya->delete();

            $this->clearDashboardCache();
            return redirect()->route('admin.budaya.index')
                ->with('success', 'Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting budaya: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus konten.');
        }
    }
    
    /**
     * Toggle active status.
     */
    public function toggleStatus(string $id)
    {
        $budaya = MongoBudaya::findOrFail($id);
        $budaya->is_active = !$budaya->is_active;
        $budaya->save();
        
        $this->logActivity('toggle_status', 'budaya', (string)$budaya->_id, ['is_active' => !$budaya->is_active], ['is_active' => $budaya->is_active]);
        
        return redirect()->route('admin.budaya.index')->with('success', 'Status berhasil diubah');
    }
}
