<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoBeritaPromosi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaPromosiController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = MongoBeritaPromosi::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('judul', 'like', "%{$search}%");
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

        $beritaPromosi = $query->orderBy('tanggal_tayang', 'desc')
            ->paginate(15);

        return view('admin.berita_promosi.index', compact('beritaPromosi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:BERITA,PROMO,EVENT',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'konten' => 'required|string',
            'tanggal_tayang' => 'required|date',
        ]);

        try {
            $data = $request->except(['thumbnail', '_token', 'tampilkan_di_carousel', 'is_active']);
            
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $this->uploadFile($request->file('thumbnail'), 'berita_promosi');
            }

            $data['admin_id'] = auth('admin')->id() ?? null;
            $data['is_active'] = $request->has('is_active');
            $data['tampilkan_di_carousel'] = $request->has('tampilkan_di_carousel');

            $bp = MongoBeritaPromosi::create($data);
            
            $this->logActivity('create', 'berita_promosi', (string)$bp->_id, null, $bp->toArray());

            return redirect()->back()->with('success', 'Berhasil Ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(string $id)
    {
        $bp = MongoBeritaPromosi::findOrFail($id);
        
        if ($bp->tanggal_tayang) {
            $bp->tanggal_tayang_formatted = $bp->tanggal_tayang->format('Y-m-d');
        }

        return response()->json($bp);
    }

    public function update(Request $request, string $id)
    {
        $bp = MongoBeritaPromosi::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:BERITA,PROMO,EVENT',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'konten' => 'required|string',
            'tanggal_tayang' => 'required|date',
        ]);

        try {
            $data = $request->except(['thumbnail', '_token', '_method', 'tampilkan_di_carousel', 'is_active']);
            
            if ($request->hasFile('thumbnail')) {
                if ($bp->thumbnail) {
                    $this->deleteFile($bp->thumbnail);
                }
                $data['thumbnail'] = $this->uploadFile($request->file('thumbnail'), 'berita_promosi');
            }

            $data['is_active'] = $request->has('is_active');
            $data['tampilkan_di_carousel'] = $request->has('tampilkan_di_carousel');

            $oldValues = $bp->toArray();
            $bp->update($data);
            
            $this->logActivity('update', 'berita_promosi', (string)$bp->_id, $oldValues, $bp->toArray());

            return redirect()->back()->with('success', 'Berhasil Diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $bp = MongoBeritaPromosi::findOrFail($id);
        
        if ($bp->thumbnail) {
            $this->deleteFile($bp->thumbnail);
        }
        
        $this->logActivity('delete', 'berita_promosi', (string)$bp->_id, $bp->toArray());
        $bp->delete();
        
        return redirect()->back()->with('success', 'Berhasil Dihapus');
    }
}
