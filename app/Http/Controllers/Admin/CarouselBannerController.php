<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\CarouselBanner;
use App\Models\MongoDB\MongoDestination;
use App\Models\MongoDB\MongoEvent;
use App\Models\MongoDB\MongoBeritaPromosi;
use App\Models\MongoDB\MongoBudaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarouselBannerController extends BaseAdminController
{
    public function index(Request $request)
    {
        $banners = CarouselBanner::orderBy('order', 'asc')->get();
        $autoplayDuration = \App\Models\AppSetting::get('carousel_autoplay_duration', 5);
        return view('admin.carousel_banners.index', compact('banners', 'autoplayDuration'));
    }

    public function signUpload(Request $request)
    {
        $cloudUrl = config('cloudinary.cloud_url') ?: env('CLOUDINARY_URL');
        
        if (!$cloudUrl && !env('CLOUDINARY_CLOUD_NAME')) {
            return response()->json([
                'success' => true,
                'mode' => 'local'
            ]);
        }

        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $cloudName = env('CLOUDINARY_CLOUD_NAME');

        if ($cloudUrl) {
            $parsed = parse_url($cloudUrl);
            if ($parsed && isset($parsed['user']) && isset($parsed['pass']) && isset($parsed['host'])) {
                $apiKey = $parsed['user'];
                $apiSecret = $parsed['pass'];
                $cloudName = $parsed['host'];
            }
        }

        if (!$apiKey || !$apiSecret || !$cloudName) {
            return response()->json([
                'success' => true,
                'mode' => 'local'
            ]);
        }

        $timestamp = time();
        $module = $request->input('module', 'carousel_banners');
        $folder = 'smarttourism/' . trim($module, '/');

        $params = [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ];

        ksort($params);

        $signParts = [];
        foreach ($params as $key => $val) {
            $signParts[] = "$key=$val";
        }
        $stringToSign = implode('&', $signParts) . $apiSecret;
        $signature = sha1($stringToSign);

        return response()->json([
            'success' => true,
            'mode' => 'cloudinary',
            'cloud_name' => $cloudName,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
            'folder' => $folder,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_badge' => 'required|string|max:50',
            'image_url' => 'required|' . ($request->hasFile('image_url') ? 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm,ogg|max:204800' : 'string'),
            'content_id' => 'nullable|string',
            'content_type' => 'nullable|in:destinasi,event,berita_promosi,budaya',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'media_type' => 'required|in:image,video',
            'play_duration' => 'nullable|integer|min:1',
        ]);

        try {
            $data = $request->except(['image_url', '_token']);
            
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $mime = $file->getMimeType();
                $isVid = str_starts_with($mime, 'video/');
                $resourceType = $isVid ? 'video' : 'image';
                
                $path = $this->uploadFile($file, 'carousel_banners', [
                    'mimes' => ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm', 'video/ogg'],
                    'max_size' => 200,
                    'resource_type' => $resourceType
                ]);
                $data['image_url'] = $path;
                $data['media_type'] = $isVid ? 'video' : 'image';
            } elseif ($request->filled('image_url') && (str_starts_with($request->input('image_url'), 'http://') || str_starts_with($request->input('image_url'), 'https://'))) {
                $data['image_url'] = $request->input('image_url');
                $url = $request->input('image_url');
                $isVid = preg_match('/\.(mp4|mov|avi|webm|ogg)/i', $url) || str_contains($url, '/video/upload/');
                $data['media_type'] = $isVid ? 'video' : 'image';
            } else {
                $data['media_type'] = $request->input('media_type', 'image');
            }

            $data['admin_id'] = auth('admin')->id() ?? null;
            $data['is_active'] = $request->has('is_active') && $request->is_active === 'on';
            $data['order'] = (int)($request->order ?? 0);

            // Handle optional dates
            if (empty($data['start_date'])) $data['start_date'] = null;
            if (empty($data['end_date'])) $data['end_date'] = null;

            // Handle display settings
            $data['play_duration'] = $request->filled('play_duration') ? (int)$request->play_duration : ($data['media_type'] === 'video' ? 10 : 5);
            $data['video_loop'] = $request->has('video_loop') && $request->video_loop === 'on';
            $data['video_muted'] = $request->has('video_muted') && $request->video_muted === 'on';
            $data['video_autoplay'] = $request->has('video_autoplay') && $request->video_autoplay === 'on';

            CarouselBanner::create($data);

            session()->flash('success', 'Slide Carousel berhasil ditambahkan');

            return response()->json([
                'success' => true,
                'message' => 'Slide Carousel berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $banner = CarouselBanner::findOrFail($id);
        
        // Format dates for the form
        if ($banner->start_date) {
            $banner->start_date_formatted = $banner->start_date->format('Y-m-d');
        }
        if ($banner->end_date) {
            $banner->end_date_formatted = $banner->end_date->format('Y-m-d');
        }

        return response()->json($banner);
    }

    public function update(Request $request, string $id)
    {
        $banner = CarouselBanner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_badge' => 'required|string|max:50',
            'image_url' => 'nullable|' . ($request->hasFile('image_url') ? 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi,webm,ogg|max:204800' : 'string'),
            'content_id' => 'nullable|string',
            'content_type' => 'nullable|in:destinasi,event,berita_promosi,budaya',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'media_type' => 'required|in:image,video',
            'play_duration' => 'nullable|integer|min:1',
        ]);

        try {
            $data = $request->except(['image_url', '_token', '_method']);
            
            if ($request->hasFile('image_url')) {
                // Delete old image if exists
                if ($banner->image_url) {
                    $this->deleteFile($banner->image_url);
                }

                $file = $request->file('image_url');
                $mime = $file->getMimeType();
                $isVid = str_starts_with($mime, 'video/');
                $resourceType = $isVid ? 'video' : 'image';
                
                $path = $this->uploadFile($file, 'carousel_banners', [
                    'mimes' => ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm', 'video/ogg'],
                    'max_size' => 200,
                    'resource_type' => $resourceType
                ]);
                $data['image_url'] = $path;
                $data['media_type'] = $isVid ? 'video' : 'image';
            } elseif ($request->filled('image_url') && (str_starts_with($request->input('image_url'), 'http://') || str_starts_with($request->input('image_url'), 'https://'))) {
                // Delete old image if exists
                if ($banner->image_url) {
                    $this->deleteFile($banner->image_url);
                }

                $data['image_url'] = $request->input('image_url');
                $url = $request->input('image_url');
                $isVid = preg_match('/\.(mp4|mov|avi|webm|ogg)/i', $url) || str_contains($url, '/video/upload/');
                $data['media_type'] = $isVid ? 'video' : 'image';
            } else {
                $data['media_type'] = $request->input('media_type', $banner->media_type ?? 'image');
            }

            $data['is_active'] = $request->has('is_active') && $request->is_active === 'on';
            $data['order'] = (int)($request->order ?? 0);
            
            // Handle optional dates
            if (empty($data['start_date'])) $data['start_date'] = null;
            if (empty($data['end_date'])) $data['end_date'] = null;

            // Handle display settings
            $data['play_duration'] = $request->filled('play_duration') ? (int)$request->play_duration : ($data['media_type'] === 'video' ? 10 : 5);
            $data['video_loop'] = $request->has('video_loop') && $request->video_loop === 'on';
            $data['video_muted'] = $request->has('video_muted') && $request->video_muted === 'on';
            $data['video_autoplay'] = $request->has('video_autoplay') && $request->video_autoplay === 'on';

            $banner->update($data);

            session()->flash('success', 'Slide Carousel berhasil diperbarui');

            return response()->json([
                'success' => true,
                'message' => 'Slide Carousel berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Carousel Update Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $banner = CarouselBanner::findOrFail($id);
        
        if ($banner->image_url) {
            $this->deleteFile($banner->image_url);
        }
        
        $banner->delete();
        
        return redirect()->back()->with('success', 'Banner berhasil dihapus');
    }

    public function updateOrder(Request $request)
    {
        $orders = $request->input('orders', []);
        
        try {
            foreach ($orders as $item) {
                if (isset($item['id']) && isset($item['order'])) {
                    $banner = CarouselBanner::find($item['id']);
                    if ($banner) {
                        $banner->update(['order' => (int) $item['order']]);
                    }
                }
            }
            session()->flash('success', 'Urutan carousel berhasil disimpan');
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleActive(string $id, Request $request)
    {
        try {
            $banner = CarouselBanner::findOrFail($id);
            $isActive = $request->input('is_active', false);
            
            $banner->update(['is_active' => (bool) $isActive]);
            
            return response()->json([
                'success' => true,
                'message' => $isActive ? 'Slide diaktifkan' : 'Slide dinonaktifkan',
                'is_active' => $banner->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get content list by category for carousel selection
     */
    public function getContentsByCategory(Request $request)
    {
        try {
            $category = $request->input('category');
            $contents = [];

            switch ($category) {
                case 'DESTINASI':
                    $contents = MongoDestination::select('_id', 'name', 'description')
                        ->limit(100)
                        ->get()
                        ->map(fn($item) => [
                            'id' => (string)$item->_id,
                            'title' => $item->name,
                            'description' => substr($item->description ?? '', 0, 100)
                        ]);
                    break;
                case 'EVENT':
                    $contents = MongoEvent::select('_id', 'name', 'description')
                        ->limit(100)
                        ->get()
                        ->map(fn($item) => [
                            'id' => (string)$item->_id,
                            'title' => $item->name,
                            'description' => substr($item->description ?? '', 0, 100)
                        ]);
                    break;
                case 'BERITA_PROMOSI':
                    $contents = MongoBeritaPromosi::select('_id', 'judul', 'konten')
                        ->limit(100)
                        ->get()
                        ->map(fn($item) => [
                            'id' => (string)$item->_id,
                            'title' => $item->judul,
                            'description' => substr($item->konten ?? '', 0, 100)
                        ]);
                    break;
                case 'BUDAYA':
                    $contents = MongoBudaya::select('_id', 'name', 'description')
                        ->limit(100)
                        ->get()
                        ->map(fn($item) => [
                            'id' => (string)$item->_id,
                            'title' => $item->name,
                            'description' => substr($item->description ?? '', 0, 100)
                        ]);
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $contents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update global autoplay duration setting
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'carousel_autoplay_duration' => 'required|integer|min:1|max:60',
        ]);

        try {
            \App\Models\AppSetting::set(
                'carousel_autoplay_duration',
                (int)$request->input('carousel_autoplay_duration'),
                'integer'
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan durasi autoplay berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
