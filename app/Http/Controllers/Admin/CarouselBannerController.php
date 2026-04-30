<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\CarouselBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CarouselBannerController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = CarouselBanner::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->has('category_badge') && $request->category_badge != '') {
            $query->where('category_badge', $request->category_badge);
        }

        $banners = $query->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.carousel_banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_badge' => 'required|string|max:50',
            'image_url' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
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
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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

            return response()->json([
                'success' => true,
                'message' => 'Slide Carousel berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $banner = CarouselBanner::findOrFail($id);
        
        if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
            Storage::disk('public')->delete($banner->image_url);
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
                    CarouselBanner::where('_id', $item['id'])->update(['order' => (int) $item['order']]);
                }
            }
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
}
