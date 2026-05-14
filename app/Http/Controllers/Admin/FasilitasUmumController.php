<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoFasilitasUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FasilitasUmumController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the facilities.
     */
    public function index(Request $request)
    {
        $query = MongoFasilitasUmum::query();

        // Search across name, address, and type
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Filter by Type
        if ($request->has('type') && !empty($request->type) && $request->type !== 'Semua') {
            $query->where('type', $request->type);
        }

        // Filter by Status
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Advanced Sorting
        $sortColumn = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['name', 'type', 'is_active', 'created_at'];
        
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int)$request->get('per_page', 15);
        $facilities = $query->paginate($perPage)->withQueryString();

        return view('admin.fasilitas_umum.index', compact('facilities'));
    }

    /**
     * Store a newly created facility in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:SPBU,Hotel,Resto,RS/Puskesmas,ATM',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone_number' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'available_services' => 'nullable|string',
            'tags' => 'nullable|string',
            'operational_hours' => 'required|string|max:255',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
        ]);

        try {
            $validated['is_active'] = $request->boolean('is_active');
            
            if (isset($validated['available_services']) && $validated['available_services']) {
                $validated['available_services'] = array_values(array_filter(array_map('trim', explode(',', $validated['available_services']))));
            } else {
                $validated['available_services'] = [];
            }

            if (isset($validated['tags']) && $validated['tags']) {
                $validated['tags'] = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
            } else {
                $validated['tags'] = [];
            }
            
            if ($request->hasFile('image')) {
                $validated['image_url'] = $this->uploadFile($request->file('image'), 'fasilitas_umum');
            }

            $validated['admin_id'] = $this->admin->id;
            
            $facility = MongoFasilitasUmum::create($validated);
            
            $this->logActivity('create', 'facility', (string)$facility->_id, null, $facility->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fasilitas berhasil ditambahkan',
                    'facility' => $facility
                ]);
            }

            return redirect()->route('admin.fasilitas_umum.index')
                ->with('success', 'Fasilitas berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error creating facility: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan fasilitas: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->with('error', 'Gagal menambahkan fasilitas: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified facility.
     */
    public function edit(string $id, Request $request)
    {
        $facility = MongoFasilitasUmum::findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($facility);
        }

        return response()->json($facility);
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(Request $request, string $id)
    {
        $facility = MongoFasilitasUmum::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:SPBU,Hotel,Resto,RS/Puskesmas,ATM',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone_number' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'available_services' => 'nullable|string',
            'tags' => 'nullable|string',
            'operational_hours' => 'required|string|max:255',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
        ]);

        try {
            $validated['is_active'] = $request->boolean('is_active');
            
            if (isset($validated['available_services']) && $validated['available_services']) {
                $validated['available_services'] = array_values(array_filter(array_map('trim', explode(',', $validated['available_services']))));
            } else {
                $validated['available_services'] = [];
            }

            if (isset($validated['tags']) && $validated['tags']) {
                $validated['tags'] = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
            } else {
                $validated['tags'] = [];
            }
            
            if ($request->hasFile('image')) {
                if ($facility->image_url) {
                    $this->deleteFile($facility->image_url);
                }
                $validated['image_url'] = $this->uploadFile($request->file('image'), 'fasilitas_umum');
            }
            
            $oldValues = $facility->toArray();
            $facility->update($validated);
            
            $this->logActivity('update', 'facility', (string)$facility->_id, $oldValues, $facility->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fasilitas berhasil diperbarui',
                    'facility' => $facility
                ]);
            }

            return redirect()->route('admin.fasilitas_umum.index')
                ->with('success', 'Fasilitas berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating facility: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui fasilitas: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->with('error', 'Gagal memperbarui fasilitas: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the active status of a facility.
     */
    public function toggleStatus(string $id)
    {
        $facility = MongoFasilitasUmum::findOrFail($id);

        try {
            $oldValues = $facility->toArray();
            $facility->update(['is_active' => !$facility->is_active]);

            $this->logActivity('update', 'facility', (string)$facility->_id, $oldValues, $facility->toArray());

            return response()->json([
                'success' => true,
                'is_active' => $facility->is_active,
                'message' => $facility->is_active ? 'Fasilitas diaktifkan.' : 'Fasilitas dinonaktifkan.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling facility status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status.'], 500);
        }
    }

    /**
     */
    public function destroy(string $id)
    {
        $facility = MongoFasilitasUmum::findOrFail($id);

        try {
            $this->logActivity('delete', 'facility', (string)$facility->_id, $facility->toArray());
            
            if ($facility->image_url) {
                $this->deleteFile($facility->image_url);
            }
            
            $facility->delete();

            return redirect()->route('admin.fasilitas_umum.index')
                ->with('success', 'Fasilitas berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting facility: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus fasilitas.');
        }
    }
}
