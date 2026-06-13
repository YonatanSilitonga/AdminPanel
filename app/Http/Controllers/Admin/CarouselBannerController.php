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
        return view('admin.carousel_banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_badge' => 'required|string|max:50',
            'image_url' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'content_id' => 'nullable|string',
            'content_type' => 'nullable|in:destinasi,event,berita_promosi,budaya',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $data = $request->except(['image_url', '_token']);
            
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $path = $this->uploadFile($file, 'carousel_banners');
                $data['image_url'] = $path;
            }

            $data['admin_id'] = auth('admin')->id() ?? null;
            $data['is_active'] = $request->has('is_active') && $request->is_active === 'on';
            $data['order'] = (int)($request->order ?? 0);

            // Handle optional dates
            if (empty($data['start_date'])) $data['start_date'] = null;
            if (empty($data['end_date'])) $data['end_date'] = null;

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
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'content_id' => 'nullable|string',
            'content_type' => 'nullable|in:destinasi,event,berita_promosi,budaya',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $data = $request->except(['image_url', '_token', '_method']);
            
            if ($request->hasFile('image_url')) {
                // Delete old image if exists
                if ($banner->image_url) {
                    $this->deleteFile($banner->image_url);
                }

                $file = $request->file('image_url');
                $path = $this->uploadFile($file, 'carousel_banners');
                $data['image_url'] = $path;
            }

            $data['is_active'] = $request->has('is_active') && $request->is_active === 'on';
            $data['order'] = (int)($request->order ?? 0);
            
            // Handle optional dates
            if (empty($data['start_date'])) $data['start_date'] = null;
            if (empty($data['end_date'])) $data['end_date'] = null;

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

        // Soft delete — hanya set deleted_at, file fisik tetap ada
        $banner->delete();

        return redirect()->back()->with('success', 'Banner berhasil dihapus');
    }

    public function forceDestroy(string $id)
    {
        $banner = CarouselBanner::withTrashed()->findOrFail($id);

        // Hapus file fisik hanya saat force delete
        if ($banner->image_url) {
            $this->deleteFile($banner->image_url);
        }

        $banner->forceDelete();

        return redirect()->back()->with('success', 'Banner berhasil dihapus permanen');
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
}
