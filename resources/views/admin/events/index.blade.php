@extends('admin.layouts.app')

@section('title', 'Daftar Event')
@section('navbar_title', 'Event')
@section('page_title', 'Event')
@section('page_description', 'Kelola konten event dan promosi destinasi')

@section('page_actions')
<button @click="showCreateModal = true" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
    Tambah Event
</button>
@endsection

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Content Management</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Kelola Event</span>
</nav>
@endsection

@section('content')
<div x-data="{ 
    showEditModal: false,
    showCreateModal: false,
    editingEvent: null,
    loading: false,
    fileName: '',
    createFileName: '',
    schedule: [],
    createSchedule: [],
    openTime: '08:00',
    closeTime: '17:00',
    editOpenTime: '08:00',
    editCloseTime: '17:00',
    is_active: true,
    edit_is_active: true,
    
    async openEditModal(id) {
        this.loading = true;
        this.showEditModal = true;
        this.editingEvent = null;
        try {
            const response = await fetch(`/admin/events/${id}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.editingEvent = await window.safeParseJSON(response);
            this.schedule = this.editingEvent.schedule || [];
            this.fileName = this.editingEvent.banner_url ? 'Banner saat ini' : '';
            
            if (this.editingEvent.opening_hours && this.editingEvent.opening_hours.includes(' - ')) {
                const parts = this.editingEvent.opening_hours.split(' - ');
                this.editOpenTime = parts[0];
                this.editCloseTime = parts[1];
            } else {
                this.editOpenTime = '08:00';
                this.editCloseTime = '17:00';
            }
            this.edit_is_active = this.editingEvent.is_active ?? true;
        } catch (error) {
            alert('Gagal mengambil data event');
            this.showEditModal = false;
        } finally {
            this.loading = false;
        }
    },
    
    addSchedule() {
        this.schedule.push({ time: '09:00', activity: '' });
    },
    
    removeSchedule(index) {
        this.schedule.splice(index, 1);
    },

    addCreateSchedule() {
        this.createSchedule.push({ time: '09:00', activity: '' });
    },
    
    removeCreateSchedule(index) {
        this.createSchedule.splice(index, 1);
    },
    
    async submitUpdate() {
        this.loading = true;
        const form = document.getElementById('editEventForm');
        const formData = new FormData(form);
        
        this.schedule.forEach((item, index) => {
            formData.set(`schedule[${index}][time]`, item.time);
            formData.set(`schedule[${index}][activity]`, item.activity);
        });
        
        formData.set('opening_hours', this.editOpenTime + ' - ' + this.editCloseTime);
        formData.set('is_active', this.edit_is_active ? '1' : '0');

        const eventId = this.editingEvent._id || this.editingEvent.id;
        try {
            const response = await fetch(`/admin/events/${eventId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                },
                body: formData
            });
            
            const result = await window.safeParseJSON(response);
            if (result.success) {
                window.location.reload();
            } else {
                alert(result.message || 'Gagal memperbarui event');
            }
        } catch (error) {
            alert('Terjadi kesalahan saat menyimpan data');
        } finally {
            this.loading = false;
        }
    },

    async submitCreate() {
        this.loading = true;
        const form = document.getElementById('createEventForm');
        const formData = new FormData(form);
        
        this.createSchedule.forEach((item, index) => {
            formData.set(`schedule[${index}][time]`, item.time);
            formData.set(`schedule[${index}][activity]`, item.activity);
        });

        formData.set('opening_hours', this.openTime + ' - ' + this.closeTime);
        formData.set('is_active', this.is_active ? '1' : '0');

        try {
            const response = await fetch('/admin/events', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                },
                body: formData
            });
            
            const result = await window.safeParseJSON(response);
            if (result.success) {
                window.location.reload();
            } else {
                alert(result.message || 'Gagal membuat event');
            }
        } catch (error) {
            alert('Terjadi kesalahan saat menyimpan data');
        } finally {
            this.loading = false;
        }
    }
}">
    <!-- Search & Filters -->
    <div class="flex flex-wrap items-center gap-4 mb-8">
        <form method="GET" action="{{ route('admin.events.index') }}" class="flex flex-wrap items-center gap-4 w-full">
            <div class="relative flex-1 min-w-[280px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, lokasi, kategori..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm transition-all shadow-sm placeholder-gray-300">
            </div>

            <select name="status" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium">
                <option value="all" @selected(!request('status') || request('status') === 'all')>Semua Status</option>
                <option value="upcoming" @selected(request('status') === 'upcoming')>Akan Datang</option>
                <option value="ongoing" @selected(request('status') === 'ongoing')>Berlangsung</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
            </select>

            <select name="category" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium">
                <option value="">Semua Kategori</option>
                <option value="Budaya" @selected(request('category') === 'Budaya')>Budaya</option>
                <option value="Adat" @selected(request('category') === 'Adat')>Adat</option>
                <option value="Olahraga" @selected(request('category') === 'Olahraga')>Olahraga</option>
                <option value="Kuliner" @selected(request('category') === 'Kuliner')>Kuliner</option>
            </select>
        </form>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Nama Event</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($events as $index => $event)
                        @php
                            $now = now();
                            if ($event->start_date > $now) {
                                $statusLabel = 'Akan Datang';
                                $statusClass = 'bg-[#E6F6F2] text-[#00A884] px-4 py-1.5 rounded-xl font-bold text-xs inline-block';
                            } elseif ($event->end_date < $now) {
                                $statusLabel = 'Selesai';
                                $statusClass = 'bg-gray-100 text-gray-400 px-4 py-1.5 rounded-xl font-bold text-xs inline-block';
                            } else {
                                $statusLabel = 'Berlangsung';
                                $statusClass = 'bg-[#F0FDF4] text-[#16A34A] px-4 py-1.5 rounded-xl font-bold text-xs inline-block';
                            }

                            $categoryColors = [
                                'Budaya' => 'text-[#066466]',
                                'Adat' => 'text-[#066466]',
                                'Olahraga' => 'text-[#066466]',
                                'Kuliner' => 'text-[#066466]',
                            ];
                            $catColor = $categoryColors[$event->category] ?? 'text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-8 py-5 text-sm font-semibold text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-10 py-7">
                                <div class="text-[15px] font-bold text-gray-800">{{ $event->name }}</div>
                            </td>
                            <td class="px-10 py-7">
                                <div class="text-[14px] text-gray-500 font-medium">
                                    {{ $event->start_date->format('d M Y') }}
                                    @if($event->start_date != $event->end_date)
                                         - {{ $event->end_date->format('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-10 py-7">
                                <div class="text-[14px] text-gray-500 font-medium">{{ $event->location ?? '-' }}</div>
                            </td>
                            <td class="px-10 py-7">
                                <span class="font-bold text-xs {{ $catColor }}">
                                    {{ $event->category ?? '-' }}
                                </span>
                            </td>
                            <td class="px-10 py-7">
                                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-10 py-7 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openEditModal('{{ $event->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.events.destroy', $event->_id) }}', title: 'Hapus Event', type: 'event', name: {{ json_encode($event->name) }} })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
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

    <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between">
        <div class="text-gray-400 text-sm font-medium">
            Menampilkan {{ $events->count() }} dari {{ $events->total() }} Event
        </div>
        <div>
            {{ $events->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}
        </div>
    </div>

    <!-- Edit Modal Overlay -->
    <div x-show="showEditModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <!-- Background Backdrop -->
            <div x-show="showEditModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" 
                 @click="showEditModal = false"></div>

            <!-- Modal Panel -->
            <div x-show="showEditModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white shadow-2xl rounded-[2rem] text-gray-800 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Edit Event</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !editingEvent" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <div x-show="editingEvent">
                    <form id="editEventForm" @submit.prevent="submitUpdate()" class="space-y-6">
                        @method('PUT')
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Event</label>
                            <input type="text" name="name" x-model="editingEvent.name" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori</label>
                            <select name="category" x-model="editingEvent.category" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700 appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22currentColor%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')">
                                <option value="Budaya">Budaya</option>
                                <option value="Adat">Adat</option>
                                <option value="Olahraga">Olahraga</option>
                                <option value="Kuliner">Kuliner</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tanggal Mulai</label>
                                <input type="date" name="start_date" x-model="editingEvent.start_date.substring(0, 10)" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tanggal Selesai</label>
                                <input type="date" name="end_date" x-model="editingEvent.end_date.substring(0, 10)" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi (Nama Tempat)</label>
                            <input type="text" name="location" x-model="editingEvent.location" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Latitude</label>
                                <input type="text" name="latitude" x-model="editingEvent.latitude" placeholder="Contoh: 2.3361" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                                <input type="text" name="longitude" x-model="editingEvent.longitude" placeholder="Contoh: 99.0494" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Penyelenggara</label>
                            <input type="text" name="organizer" x-model="editingEvent.organizer" placeholder="Contoh: BPODT" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <!-- Info Operasional -->
                        <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                    <div class="flex items-center gap-2">
                                        <input type="time" x-model="editOpenTime" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                                        <span class="text-gray-400">-</span>
                                        <input type="time" x-model="editCloseTime" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                    <input type="text" name="ticket_price" x-model="editingEvent.ticket_price" placeholder="Gratis / Rp 10rb" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                    <input type="text" name="best_time" x-model="editingEvent.best_time" placeholder="Pagi / Sore" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                            <textarea name="description" rows="3" x-model="editingEvent.description" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700 placeholder-gray-300"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tags / Label</label>
                            <input type="text" name="tags" :value="editingEvent.tags ? editingEvent.tags.join(', ') : ''" placeholder="Pisahkan dengan koma. Contoh: Culture, Tradition, Arts" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <!-- Jadwal Kegiatan -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jadwal Kegiatan</label>
                                <button type="button" @click="addSchedule()" class="flex items-center gap-1 text-sidebar bg-sidebar/5 px-3 py-1.5 rounded-xl text-[10px] font-bold hover:bg-sidebar/10 transition-all uppercase tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, index) in schedule" :key="index">
                                    <div class="flex items-center gap-3">
                                        <input type="time" x-model="item.time" class="w-28 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium">
                                        <input type="text" x-model="item.activity" placeholder="Keterangan kegiatan" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium">
                                        <button type="button" @click="removeSchedule(index)" class="p-2 text-red-300 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Foto Event -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Foto Event</label>
                            <div class="relative group">
                                <input type="file" name="banner" id="banner_modal" class="hidden" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                <label for="banner_modal" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <div class="p-3 bg-white rounded-2xl shadow-sm mb-3 group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700" x-text="fileName || 'Klik atau seret file ke sini'"></p>
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tight">PNG, JPG (Maks. 2MB, Rekomendasi 1920x1080px)</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" x-model="edit_is_active" id="edit_is_active_check" class="w-4 h-4 text-sidebar border-gray-200 rounded-lg focus:ring-sidebar/20">
                            <label for="edit_is_active_check" class="text-sm font-bold text-gray-600 cursor-pointer">Setel sebagai Aktif</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" @click="showEditModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors border border-gray-200 rounded-xl">Batal</button>
                            <button type="submit" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2" :disabled="loading">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Simpan Event</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Modal Overlay -->
    <div x-show="showCreateModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex min-h-screen items-center justify-center px-4 py-8 text-center sm:block sm:p-0">
            <!-- Background Backdrop -->
            <div x-show="showCreateModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" 
                 @click="showCreateModal = false"></div>

            <!-- Modal Panel -->
            <div x-show="showCreateModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-2xl px-8 py-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[2rem] sm:my-8 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Event</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div>
                    <form id="createEventForm" @submit.prevent="submitCreate()" class="space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Event</label>
                            <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori</label>
                            <select name="category" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700 appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22currentColor%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')">
                                <option value="Budaya">Budaya</option>
                                <option value="Adat">Adat</option>
                                <option value="Olahraga">Olahraga</option>
                                <option value="Kuliner">Kuliner</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tanggal Mulai</label>
                                <input type="date" name="start_date" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tanggal Selesai</label>
                                <input type="date" name="end_date" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi (Nama Tempat)</label>
                            <input type="text" name="location" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Penyelenggara</label>
                            <input type="text" name="organizer" placeholder="Contoh: BPODT" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Latitude</label>
                                <input type="text" name="latitude" placeholder="Contoh: 2.3361" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                                <input type="text" name="longitude" placeholder="Contoh: 99.0494" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                        </div>

                        <!-- Info Operasional -->
                        <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                    <div class="flex items-center gap-2">
                                        <input type="time" x-model="openTime" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                                        <span class="text-gray-400">-</span>
                                        <input type="time" x-model="closeTime" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                    <input type="text" name="ticket_price" placeholder="Gratis / Rp 10rb" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                    <input type="text" name="best_time" placeholder="Pagi / Sore" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                            <textarea name="description" rows="3" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700 placeholder-gray-300"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tags / Label</label>
                            <input type="text" name="tags" placeholder="Pisahkan dengan koma. Contoh: Culture, Tradition, Arts" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <!-- Jadwal Kegiatan -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jadwal Kegiatan</label>
                                <button type="button" @click="addCreateSchedule()" class="flex items-center gap-1 text-sidebar bg-sidebar/5 px-3 py-1.5 rounded-xl text-[10px] font-bold hover:bg-sidebar/10 transition-all uppercase tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, index) in createSchedule" :key="index">
                                    <div class="flex items-center gap-3">
                                        <input type="time" x-model="item.time" class="w-28 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium">
                                        <input type="text" x-model="item.activity" placeholder="Keterangan kegiatan" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium">
                                        <button type="button" @click="removeCreateSchedule(index)" class="p-2 text-red-300 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Foto Event -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Foto Event</label>
                            <div class="relative group">
                                <input type="file" name="banner" id="create_banner_modal" class="hidden" @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                <label for="create_banner_modal" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <div class="p-3 bg-white rounded-2xl shadow-sm mb-3 group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Klik atau seret file ke sini'"></p>
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tight">PNG, JPG (Maks. 2MB, Rekomendasi 1920x1080px)</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" x-model="is_active" id="is_active_check" class="w-4 h-4 text-sidebar border-gray-200 rounded-lg focus:ring-sidebar/20">
                            <label for="is_active_check" class="text-sm font-bold text-gray-600 cursor-pointer">Setel sebagai Aktif</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" @click="showCreateModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors border border-gray-200 rounded-xl">Batal</button>
                            <button type="submit" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2" :disabled="loading">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Simpan Event</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
