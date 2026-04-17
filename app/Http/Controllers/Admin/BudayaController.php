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
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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

        $budayas = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = ['Sejarah', 'Tradisi', 'Rumah Adat', 'Cerita Rakyat', 'Kuliner'];

        return view('admin.budaya.index', compact('budayas', 'categories'));
    }

    /**
     * Store a newly created budaya in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Sejarah,Tradisi,Rumah Adat,Cerita Rakyat,Kuliner',
            'category_mobile' => 'nullable|string|max:100',
            'location' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'required|string',
            'is_active' => 'boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        try {
            $validated['is_active'] = $request->has('is_active');
            
            if ($request->hasFile('thumbnail')) {
                $validated['image_url'] = $this->uploadFile($request->file('thumbnail'), 'budaya');
                unset($validated['thumbnail']);
            }
            
            // Set mobile category shorthand
            if (empty($validated['category_mobile'])) {
                $validated['category_mobile'] = strtoupper($validated['category']);
            }

            $budaya = MongoBudaya::create($validated);
            
            $this->logActivity('create', 'budaya', (string)$budaya->_id, null, $budaya->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Konten Budaya berhasil ditambahkan.',
                    'budaya' => $budaya
                ]);
            }

            return redirect()->route('admin.budaya.index')
                ->with('success', 'Konten Budaya berhasil ditambahkan.');
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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        try {
            $validated['is_active'] = $request->has('is_active');
            
            if ($request->hasFile('thumbnail')) {
                if ($budaya->image_url) {
                    $this->deleteFile($budaya->image_url);
                }
                $validated['image_url'] = $this->uploadFile($request->file('thumbnail'), 'budaya');
                unset($validated['thumbnail']);
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
                    'message' => 'Konten Budaya berhasil diperbarui.',
                    'budaya' => $budaya
                ]);
            }

            return redirect()->route('admin.budaya.index')
                ->with('success', 'Konten Budaya berhasil diperbarui.');
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
            
            if ($budaya->image_url) {
                $this->deleteFile($budaya->image_url);
            }
            
            $budaya->delete();

            return redirect()->route('admin.budaya.index')
                ->with('success', 'Konten Budaya berhasil dihapus.');
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
        
        return redirect()->route('admin.budaya.index')->with('success', 'Status konten berhasil diubah.');
    }
}
