@extends('admin.layouts.app')

@section('title', 'Daftar Event')
@section('page_title', 'Event')
@section('page_description', 'Kelola konten event dan promosi destinasi')

@section('content')
<!-- Tabs / Filters -->
<div class="flex flex-wrap items-center gap-4 mb-8">
    <div class="flex bg-white rounded-xl shadow-sm border border-gray-100 p-1">
        <a href="{{ route('admin.events.index') }}" class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ !request('status') || request('status') === 'all' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-gray-500 hover:bg-gray-50' }}">Semua</a>
        <a href="{{ route('admin.events.index', ['status' => 'upcoming']) }}" class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ request('status') === 'upcoming' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-gray-500 hover:bg-gray-50' }}">Akan Datang</a>
        <a href="{{ route('admin.events.index', ['status' => 'ongoing']) }}" class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ request('status') === 'ongoing' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-gray-500 hover:bg-gray-50' }}">Berlangsung</a>
        <a href="{{ route('admin.events.index', ['status' => 'completed']) }}" class="px-6 py-2 rounded-lg text-sm font-bold transition-all {{ request('status') === 'completed' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-gray-500 hover:bg-gray-50' }}">Selesai</a>
    </div>

    <div class="flex-1"></div>

    <a href="{{ route('admin.events.create') }}" class="flex items-center gap-2 px-6 py-3 bg-[#006666] text-white rounded-xl font-bold hover:opacity-90 transition-opacity shadow-lg shadow-[#006666]/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Event
    </a>
</div>

<div class="flex flex-wrap items-center gap-4 mb-6">
    <form method="GET" action="{{ route('admin.events.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
        <div class="relative w-full md:w-64">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, lokasi, kategori..."
                class="w-full pl-10 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm transition-all shadow-sm">
        </div>

        <select name="category" class="px-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-primary/20 outline-none text-sm shadow-sm transition-all">
            <option value="">Semua Kategori</option>
            <option value="Budaya" @selected(request('category') === 'Budaya')>Budaya</option>
            <option value="Adat" @selected(request('category') === 'Adat')>Adat</option>
            <option value="Olahraga" @selected(request('category') === 'Olahraga')>Olahraga</option>
            <option value="Kuliner" @selected(request('category') === 'Kuliner')>Kuliner</option>
        </select>

        <div class="flex items-center gap-2">
            <input type="date" name="from" class="px-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-primary/20 outline-none text-sm shadow-sm transition-all">
            <input type="date" name="to" class="px-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-primary/20 outline-none text-sm shadow-sm transition-all">
        </div>

        <button type="submit" class="px-6 py-3 border border-primary text-primary font-bold rounded-xl hover:bg-primary hover:text-white transition-all text-sm">Kelola Kategori</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-8 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nama Event</th>
                    <th class="px-8 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Banner</th>
                    <th class="px-8 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                    <th class="px-8 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Lokasi</th>
                    <th class="px-8 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                    <th class="px-8 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-8 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse($events as $event)
                    @php
                        $now = now();
                        if ($event->start_date > $now) {
                            $statusLabel = 'Akan Datang';
                            $statusClass = 'text-blue-600';
                        } elseif ($event->end_date < $now) {
                            $statusLabel = 'Selesai';
                            $statusClass = 'text-gray-400';
                        } else {
                            $statusLabel = 'Berlangsung';
                            $statusClass = 'text-green-600';
                        }

                        $categoryColors = [
                            'Budaya' => 'bg-green-50 text-green-600',
                            'Adat' => 'bg-emerald-50 text-emerald-600',
                            'Olahraga' => 'bg-blue-50 text-blue-600',
                            'Kuliner' => 'bg-teal-50 text-teal-600',
                        ];
                        $catClass = $categoryColors[$event->category] ?? 'bg-gray-50 text-gray-600';
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="text-sm font-bold text-gray-800">{{ $event->name }}</div>
                        </td>
                        <td class="px-8 py-6">
                            @if($event->banner_url)
                                <img src="{{ asset('storage/' . $event->banner_url) }}" alt="{{ $event->name }}" class="w-16 h-10 object-cover rounded-lg shadow-sm border border-gray-100">
                            @else
                                <div class="w-16 h-10 bg-gray-50 rounded-lg border border-dashed border-gray-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-xs text-gray-500 font-medium">
                                {{ $event->start_date->format('j M Y') }}
                                @if($event->start_date != $event->end_date)
                                     - {{ $event->end_date->format('j M Y') }}
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-xs text-gray-500 font-medium">{{ $event->location ?? '-' }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 text-[10px] font-bold rounded-full {{ $catClass }}">
                                {{ $event->category ?? '-' }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</div>
                        </td>
                        <td class="px-8 py-6 text-right space-x-2">
                            <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-lg hover:bg-green-100 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit
                            </a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-400 text-[10px] font-bold rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm font-medium">Tidak ada event yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $events->links() }}
</div>
@endsection
