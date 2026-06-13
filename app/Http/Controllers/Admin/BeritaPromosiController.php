<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoBeritaPromosi;
use Illuminate\Http\Request;

class BeritaPromosiController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = MongoBeritaPromosi::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('judul', 'regexp', "/{$search}/i");
        }

        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'aktif');
        }
        
        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('tanggal_tayang', '>=', \Carbon\Carbon::parse($request->start_date)->startOfDay());
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('tanggal_tayang', '<=', \Carbon\Carbon::parse($request->end_date)->endOfDay());
        }

        // Advanced Sorting
        $sortColumn = $request->get('sort_by', 'tanggal_tayang');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['judul', 'tanggal_tayang', 'tipe', 'is_active'];
        
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->orderBy('tanggal_tayang', 'desc');
        }

        $perPage = (int)$request->get('per_page', 15);
        $beritaPromosi = $query->paginate($perPage)->withQueryString();

        return view('admin.berita_promosi.index', compact('beritaPromosi'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'tipe' => 'required|in:BERITA,PROMO',
                'thumbnail' => 'nullable|' . ($request->hasFile('thumbnail') ? 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm,ogg|max:51200' : 'string'),
                'images' => 'nullable|array',
                'images.*' => $request->hasFile('images') ? 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm,ogg|max:51200' : 'string',
                'start_time' => 'nullable|integer|min:0',
                'end_time' => 'nullable|integer|min:0',
                'konten' => 'required|string',
                'tanggal_tayang' => 'required|date',
            ]);

            $data = $request->except(['images', 'thumbnail', 'start_time', 'end_time', '_token', 'is_active']);
            $currentMedia = [];

            // Thumbnail check (string URL or file upload)
            if ($request->filled('thumbnail') && is_string($request->input('thumbnail')) && (str_starts_with($request->input('thumbnail'), 'http://') || str_starts_with($request->input('thumbnail'), 'https://'))) {
                $path = $request->input('thumbnail');
                $isVid = preg_match('/\.(mp4|mov|avi|webm|ogg)/i', $path) || str_contains($path, '/video/upload/');
                $mediaType = $isVid ? 'video' : 'image';
                $mediaEntry = [
                    'url' => $path,
                    'type' => $mediaType,
                ];
                if ($mediaType === 'video') {
                    if ($request->filled('start_time')) {
                        $mediaEntry['start_time'] = (int)$request->input('start_time');
                    }
                    if ($request->filled('end_time')) {
                        $mediaEntry['end_time'] = (int)$request->input('end_time');
                    }
                }
                $currentMedia[] = $mediaEntry;
            } elseif ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $mediaType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
                
                if ($mediaType === 'video') {
                    $path = $this->uploadFile($file, 'berita_promosi/videos', ['resource_type' => 'video', 'max_size' => 50]);
                } else {
                    $path = $this->processImage($file, 'berita_promosi');
                }
                
                if ($path) {
                    $mediaEntry = [
                        'url' => $path,
                        'type' => $mediaType,
                    ];
                    
                    // Add timing if it's a video and timing is provided
                    if ($mediaType === 'video') {
                        if ($request->filled('start_time')) {
                            $mediaEntry['start_time'] = (int)$request->input('start_time');
                        }
                        if ($request->filled('end_time')) {
                            $mediaEntry['end_time'] = (int)$request->input('end_time');
                        }
                    }
                    
                    $currentMedia[] = $mediaEntry;
                }
            }

            // Additional images (string URLs)
            if ($request->filled('images')) {
                foreach ($request->input('images') as $img) {
                    if (is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                        $isVid = preg_match('/\.(mp4|mov|avi|webm|ogg)/i', $img) || str_contains($img, '/video/upload/');
                        $mediaType = $isVid ? 'video' : 'image';
                        $mediaEntry = [
                            'url' => $img,
                            'type' => $mediaType,
                        ];
                        if ($mediaType === 'video') {
                            if ($request->filled('start_time')) {
                                $mediaEntry['start_time'] = (int)$request->input('start_time');
                            }
                            if ($request->filled('end_time')) {
                                $mediaEntry['end_time'] = (int)$request->input('end_time');
                            }
                        }
                        $currentMedia[] = $mediaEntry;
                    }
                }
            }

            // Additional images (file uploads)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $mediaType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
                    
                    if ($mediaType === 'video') {
                        $path = $this->uploadFile($file, 'berita_promosi/videos', ['resource_type' => 'video', 'max_size' => 50]);
                    } else {
                        $path = $this->processImage($file, 'berita_promosi');
                    }

                    if ($path) {
                        $mediaEntry = [
                            'url' => $path,
                            'type' => $mediaType,
                        ];
                        
                        // Add timing if it's a video and timing is provided
                        if ($mediaType === 'video') {
                            if ($request->filled('start_time')) {
                                $mediaEntry['start_time'] = (int)$request->input('start_time');
                            }
                            if ($request->filled('end_time')) {
                                $mediaEntry['end_time'] = (int)$request->input('end_time');
                            }
                        }
                        
                        $currentMedia[] = $mediaEntry;
                    }
                }
            }

            // Separate thumbnail and images
            if (count($currentMedia) > 0) {
                $data['thumbnail'] = $currentMedia[0]['url'];
                $data['images'] = $currentMedia;
            }

            $data['admin_id'] = auth('admin')->id() ?? null;
            $data['is_active'] = $request->has('is_active');

            $bp = MongoBeritaPromosi::create($data);
            
            $this->logActivity('create', 'berita_promosi', (string)$bp->_id, null, $bp->toArray());
            $this->clearDashboardCache();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil Ditambahkan'
                ]);
            }

            return redirect()->back()->with('success', 'Berhasil Ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat kesalahan validasi pada formulir.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(string $id)
    {
        $bp = MongoBeritaPromosi::findOrFail($id);
        
        if ($bp->tanggal_tayang) {
            $bp->tanggal_tayang_formatted = $bp->tanggal_tayang->format('Y-m-d');
        }

        // Add full URL for thumbnail
        if ($bp->thumbnail) {
            $bp->thumbnail_url = image_url($bp->thumbnail);
        }
        
        // Process images array with proper formatting (supports both images and videos)
        if ($bp->images && is_array($bp->images)) {
            $bp->images_data = array_map(function($img) {
                if (is_array($img)) {
                    $mediaUrl = $img['url'] ?? $img;
                    return [
                        'path' => $mediaUrl,
                        'url' => image_url($mediaUrl),
                        'type' => media_is_video($mediaUrl) ? 'video' : 'image',
                        'start_time' => $img['start_time'] ?? null,
                        'end_time' => $img['end_time'] ?? null,
                    ];
                }
                return [
                    'path' => $img,
                    'url' => image_url($img),
                    'type' => media_is_video($img) ? 'video' : 'image',
                ];
            }, $bp->images);
        }
        
        $bp->load('admin');

        return response()->json($bp);
    }

    public function update(Request $request, string $id)
    {
        try {
            $bp = MongoBeritaPromosi::findOrFail($id);

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'tipe' => 'required|in:BERITA,PROMO',
                'thumbnail' => 'nullable|' . ($request->hasFile('thumbnail') ? 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm,ogg|max:51200' : 'string'),
                'images' => 'nullable|array',
                'images.*' => $request->hasFile('images') ? 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm,ogg|max:51200' : 'string',
                'start_time' => 'nullable|integer|min:0',
                'end_time' => 'nullable|integer|min:0',
                'konten' => 'required|string',
                'tanggal_tayang' => 'required|date',
            ]);

            $data = $request->except(['images', 'thumbnail', 'start_time', 'end_time', '_token', '_method', 'is_active', 'delete_images']);
            $deleteImages = $request->input('delete_images', []);
            $existingMedia = $bp->images ?? [];

            // Delete specified media
            if (!empty($deleteImages) && is_array($deleteImages)) {
                foreach ($deleteImages as $delImg) {
                    $this->deleteFile($delImg);
                    $existingMedia = array_filter($existingMedia, function($media) use ($delImg) {
                        $mediaUrl = is_array($media) ? ($media['url'] ?? $media) : $media;
                        return !$this->pathsMatch($mediaUrl, $delImg);
                    });
                }
                $existingMedia = array_values($existingMedia);
            }

            // Thumbnail check (string URL or file upload)
            if ($request->filled('thumbnail') && is_string($request->input('thumbnail')) && (str_starts_with($request->input('thumbnail'), 'http://') || str_starts_with($request->input('thumbnail'), 'https://'))) {
                $path = $request->input('thumbnail');
                $isVid = preg_match('/\.(mp4|mov|avi|webm|ogg)/i', $path) || str_contains($path, '/video/upload/');
                $mediaType = $isVid ? 'video' : 'image';
                $mediaEntry = [
                    'url' => $path,
                    'type' => $mediaType,
                ];
                if ($mediaType === 'video') {
                    if ($request->filled('start_time')) $mediaEntry['start_time'] = (int)$request->input('start_time');
                    if ($request->filled('end_time')) $mediaEntry['end_time'] = (int)$request->input('end_time');
                }
                array_unshift($existingMedia, $mediaEntry);
            } elseif ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $mediaType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
                
                if ($mediaType === 'video') {
                    $path = $this->uploadFile($file, 'berita_promosi/videos', ['resource_type' => 'video', 'max_size' => 50]);
                } else {
                    $path = $this->processImage($file, 'berita_promosi');
                }
                
                if ($path) {
                    $mediaEntry = [
                        'url' => $path,
                        'type' => $mediaType,
                    ];
                    
                    // Add timing if it's a video
                    if ($mediaType === 'video') {
                        if ($request->filled('start_time')) {
                            $mediaEntry['start_time'] = (int)$request->input('start_time');
                        }
                        if ($request->filled('end_time')) {
                            $mediaEntry['end_time'] = (int)$request->input('end_time');
                        }
                    }
                    
                    array_unshift($existingMedia, $mediaEntry);
                }
            }

            // Additional images (string URLs)
            if ($request->filled('images')) {
                foreach ($request->input('images') as $img) {
                    if (is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                        $isVid = preg_match('/\.(mp4|mov|avi|webm|ogg)/i', $img) || str_contains($img, '/video/upload/');
                        $mediaType = $isVid ? 'video' : 'image';
                        $mediaEntry = [
                            'url' => $img,
                            'type' => $mediaType,
                        ];
                        if ($mediaType === 'video') {
                            if ($request->filled('start_time')) $mediaEntry['start_time'] = (int)$request->input('start_time');
                            if ($request->filled('end_time')) $mediaEntry['end_time'] = (int)$request->input('end_time');
                        }
                        $existingMedia[] = $mediaEntry;
                    }
                }
            }

            // Upload additional images (files)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $mediaType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
                    
                    if ($mediaType === 'video') {
                        $path = $this->uploadFile($file, 'berita_promosi/videos', ['resource_type' => 'video', 'max_size' => 50]);
                    } else {
                        $path = $this->processImage($file, 'berita_promosi');
                    }

                    if ($path) {
                        $mediaEntry = [
                            'url' => $path,
                            'type' => $mediaType,
                        ];
                        
                        // Add timing if it's a video
                        if ($mediaType === 'video') {
                            if ($request->filled('start_time')) {
                                $mediaEntry['start_time'] = (int)$request->input('start_time');
                            }
                            if ($request->filled('end_time')) {
                                $mediaEntry['end_time'] = (int)$request->input('end_time');
                            }
                        }
                        
                        $existingMedia[] = $mediaEntry;
                    }
                }
            }

            // Update thumbnail and images
            if (count($existingMedia) > 0) {
                $data['thumbnail'] = $existingMedia[0]['url'] ?? $existingMedia[0];
                $data['images'] = array_values($existingMedia);
            } else {
                $data['thumbnail'] = null;
                $data['images'] = [];
            }

            $data['is_active'] = $request->has('is_active');

            $oldValues = $bp->toArray();
            $bp->update($data);
            
            $this->logActivity('update', 'berita_promosi', (string)$bp->_id, $oldValues, $bp->toArray());
            $this->clearDashboardCache();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil Diperbarui'
                ]);
            }

            return redirect()->back()->with('success', 'Berhasil Diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat kesalahan validasi pada formulir.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $bp = MongoBeritaPromosi::findOrFail($id);

        // Soft delete — hanya set deleted_at, file fisik tetap ada
        $this->logActivity('delete', 'berita_promosi', (string)$bp->_id, $bp->toArray());
        $bp->delete();
        $this->clearDashboardCache();

        return redirect()->back()->with('success', 'Berhasil Dihapus');
    }

    public function forceDestroy(string $id)
    {
        $bp = MongoBeritaPromosi::withTrashed()->findOrFail($id);

        // Hapus file fisik hanya saat force delete
        if ($bp->images) {
            foreach ($bp->images as $media) {
                $mediaUrl = is_array($media) ? ($media['url'] ?? $media) : $media;
                $this->deleteFile($mediaUrl);
            }
        }

        $this->logActivity('force_delete', 'berita_promosi', (string)$bp->_id, $bp->toArray());
        $bp->forceDelete();
        $this->clearDashboardCache();

        return redirect()->back()->with('success', 'Berhasil Dihapus Permanen');
    }
}
