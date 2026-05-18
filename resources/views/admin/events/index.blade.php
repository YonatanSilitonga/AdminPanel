@extends('admin.layouts.app')

@push('styles')
<style>
    /* Fix Google Autocomplete Suggestions in Modals */
    .pac-container {
        z-index: 9999 !important;
        border-radius: 1rem;
        border: none;
        margin-top: 5px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        font-family: inherit;
    }
    .pac-item {
        padding: 10px 15px;
        cursor: pointer;
        border-top: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .pac-item:first-child { border-top: none; }
    .pac-item:hover { background-color: #f9fafb; }
    .pac-item-query { font-size: 14px; color: #374151; font-weight: 600; }
    .pac-matched { color: #066466; }
    .pac-icon { display: none; }
</style>
@endpush

@section('title', 'Daftar Event')
@section('navbar_title', 'Event')
@section('page_title', 'Event')
@section('page_description', 'Kelola konten event dan promosi destinasi')

@section('page_actions')
<<<<<<< HEAD
<button @click="$dispatch('open-create-modal')" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
=======
<button type="button" onclick="document.querySelector('[data-open-create-modal]')?.click()" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
>>>>>>> c877ab79b93880db5dabcb4655b2ab956c1d3c35
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
<div id="event-manager" x-data="{ 
    showEditModal: false,
    showCreateModal: false,
    editingEvent: null,
    showViewModal: false,
    viewingEvent: null,
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
            
            if (this.editingEvent.opening_hours && String(this.editingEvent.opening_hours).includes(' - ')) {
                const parts = this.editingEvent.opening_hours.split(' - ');
                this.editOpenTime = parts[0];
                this.editCloseTime = parts[1];
            } else {
                this.editOpenTime = '08:00';
                this.editCloseTime = '17:00';
            }
            this.edit_is_active = Boolean(this.editingEvent.is_active);
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

        const eventId = this.editingEvent ? (this.editingEvent._id || this.editingEvent.id) : null;
        if (!eventId) {
            alert('ID Event tidak ditemukan');
            this.loading = false;
            return;
        }

        try {
            const response = await fetch(`/admin/events/${eventId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
<<<<<<< HEAD
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
=======
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
>>>>>>> c877ab79b93880db5dabcb4655b2ab956c1d3c35
                },
                body: formData
            });
            
            const result = await window.safeParseJSON(response);
            if (result && result.success) {
                window.location.reload();
            } else {
                alert(result?.message || 'Gagal memperbarui event');
            }
        } catch (error) {
            console.error('Update error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        } finally {
            this.loading = false;
        }
    },

    async openViewModal(id) {
        if (!id) return;
        this.loading = true;
        this.showViewModal = true;
        this.viewingEvent = null;
        try {
            const response = await fetch(`/admin/events/${id}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await window.safeParseJSON(response);
            if (data) {
                this.viewingEvent = data;
            } else {
                throw new Error('Data tidak valid');
            }
        } catch (error) {
            console.error('View error:', error);
            alert('Gagal memuat detail event');
            this.showViewModal = false;
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
<<<<<<< HEAD
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
=======
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
>>>>>>> c877ab79b93880db5dabcb4655b2ab956c1d3c35
                },
                body: formData
            });
            
            const result = await window.safeParseJSON(response);
            if (result && result.success) {
                window.location.reload();
            } else {
                alert(result?.message || 'Gagal membuat event');
            }
        } catch (error) {
            console.error('Create error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        } finally {
            this.loading = false;
        }
    }
<<<<<<< HEAD
}" @open-create-modal.window="showCreateModal = true">
=======
}">

    <button type="button" class="hidden" data-open-create-modal @click="showCreateModal = true"></button>

>>>>>>> c877ab79b93880db5dabcb4655b2ab956c1d3c35
    <!-- Search & Filters -->
    <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
        <form method="GET" action="{{ route('admin.events.index') }}" class="flex flex-wrap items-center gap-4 w-full">
            <!-- Persist current sorting -->
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <div class="relative flex-1 min-w-[280px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, lokasi, kategori..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm transition-all shadow-sm placeholder-gray-300">
            </div>

            <div class="flex items-center gap-3">
                <span class="text-[13px] font-bold text-gray-400">Tampilkan:</span>
                <select name="per_page" onchange="this.form.submit()" 
                    class="px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-emerald-500 transition-all cursor-pointer">
                    @foreach([10, 20, 50, 100] as $val)
                        <option value="{{ $val }}" @selected(request('per_page', 10) == $val)>{{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <select name="status" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-emerald-500 transition-all cursor-pointer">
                <option value="all" @selected(!request('status') || request('status') === 'all')>Semua Status</option>
                <option value="upcoming" @selected(request('status') === 'upcoming')>Akan Datang</option>
                <option value="ongoing" @selected(request('status') === 'ongoing')>Berlangsung</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
            </select>

            <select name="category" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-emerald-500 transition-all cursor-pointer">
                <option value="">Semua Kategori</option>
                <option value="Budaya" @selected(request('category') === 'Budaya')>Budaya</option>
                <option value="Adat" @selected(request('category') === 'Adat')>Adat</option>
                <option value="Olahraga" @selected(request('category') === 'Olahraga')>Olahraga</option>
                <option value="Kuliner" @selected(request('category') === 'Kuliner')>Kuliner</option>
            </select>
        </form>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    <tr class="bg-white border-b border-gray-50">
                        @php
                            $sortOrder = request('sort_order') === 'asc' ? 'desc' : 'asc';
                            $currentSort = request('sort_by', 'created_at');
                        @endphp
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => ($currentSort === 'name' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Event
                                <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'name' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'start_date', 'sort_order' => ($currentSort === 'start_date' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Tanggal
                                <svg class="w-4 h-4 {{ $currentSort === 'start_date' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'start_date' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'location', 'sort_order' => ($currentSort === 'location' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Lokasi
                                <svg class="w-4 h-4 {{ $currentSort === 'location' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'location' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_order' => ($currentSort === 'category' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Kategori
                                <svg class="w-4 h-4 {{ $currentSort === 'category' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'category' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'is_active', 'sort_order' => ($currentSort === 'is_active' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Status
                                <svg class="w-4 h-4 {{ $currentSort === 'is_active' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'is_active' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
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
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    @if(isset($event->banner_url) && $event->banner_url)
                                        <img src="{{ image_url($event->banner_url) }}" alt="{{ $event->name }}" class="w-24 h-16 object-cover rounded-xl shadow-sm border border-gray-100">
                                    @else
                                        <div class="w-24 h-16 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="text-[15px] font-bold text-gray-800 max-w-[200px] truncate" title="{{ $event->name }}">{{ $event->name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5 max-w-[150px] truncate">{{ $event->organizer ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <div class="text-[14px] text-gray-500 font-medium whitespace-nowrap">
                                    {{ $event->start_date->format('d M Y') }}
                                    @if($event->start_date != $event->end_date)
                                         - {{ $event->end_date->format('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <div class="text-[14px] text-gray-500 font-medium max-w-[150px] truncate">{{ $event->location ?? '-' }}</div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="font-bold text-xs {{ $catColor }}">
                                    {{ $event->category ?? '-' }}
                                </span>
                            </td>
                            <td class="px-10 py-6">
                                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openViewModal('{{ $event->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button @click="openEditModal('{{ $event->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Edit">
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
<<<<<<< HEAD
                 class="relative w-full max-w-2xl bg-white shadow-2xl rounded-[2rem] text-gray-800 px-8 py-8 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
=======
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 text-gray-800 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
>>>>>>> c877ab79b93880db5dabcb4655b2ab956c1d3c35
                
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
                    <form :action="`/admin/events/${editingEvent._id || editingEvent.id}`" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @method('PUT')
                        @csrf
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
                                <input type="text" name="latitude" id="edit_latitude" x-model="editingEvent.latitude" placeholder="Contoh: 2.3361" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                                <input type="text" name="longitude" id="edit_longitude" x-model="editingEvent.longitude" placeholder="Contoh: 99.0494" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                        </div>

                        {{-- Map Picker for Edit --}}
                        <div class="col-span-2 space-y-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Event (Klik/Geser untuk mengubah)</label>
                            
                            {{-- Search Box --}}
                            <div class="flex gap-2">
                                <div class="relative flex-1 group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" id="edit_location_search" placeholder="Ketik nama lokasi atau alamat..." class="w-full pl-10 pr-12 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all" autocomplete="off">
                                    <button type="button" onclick="performSearch('edit_location_search', 'edit_map_picker')" class="absolute inset-y-1.5 right-1.5 px-3 bg-sidebar text-white rounded-lg hover:opacity-90 transition-all flex items-center justify-center shadow-sm" title="Cari Lokasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </button>
                                </div>
                                <button type="button" onclick="getCurrentLocation('edit_latitude', 'edit_longitude', 'edit_map_picker')" class="px-4 py-3.5 bg-white border border-gray-100 text-gray-500 rounded-xl hover:bg-gray-50 hover:text-sidebar transition-all shadow-sm flex items-center gap-2" title="Gunakan Lokasi Saya">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-xs font-bold hidden sm:inline">Lokasi Saya</span>
                                </button>
                            </div>

                            <div id="edit_map_picker" style="width: 100%; height: 300px; border-radius: 1.5rem; border: 1px solid #f3f4f6; background-color: #f9fafb;" class="flex items-center justify-center text-gray-400 text-xs">Memuat peta...</div>
                            <p class="text-[10px] text-gray-400 italic">*Cari lokasi di atas atau klik/geser marker pada peta</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Penyelenggara</label>
                            <input type="text" name="organizer" x-model="editingEvent.organizer" placeholder="Contoh: BPODT" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                        </div>

                        <!-- Info Operasional -->
                        <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-4">
<<<<<<< HEAD
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                    <div class="flex items-center gap-2">
                                        <input type="time" x-model="editOpenTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium">
                                        <span class="text-gray-400">-</span>
                                        <input type="time" x-model="editCloseTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium">
                                    </div>
                                </div>
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                    <input type="text" name="ticket_price" x-model="editingEvent.ticket_price" placeholder="Gratis / Rp 10rb" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium">
                                </div>
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                    <input type="text" name="best_time" x-model="editingEvent.best_time" placeholder="Pagi / Sore" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium">
=======
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                    <div class="flex items-center gap-2">
                                        <input type="time" name="opening_hours_start" x-model="editOpenTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                        <span class="text-gray-400">-</span>
                                        <input type="time" name="opening_hours_end" x-model="editCloseTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    </div>
                                </div>
                                <div class="space-y-2 md:col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                    <input type="text" name="ticket_price" x-model="editingEvent.ticket_price" placeholder="Gratis / Rp 10rb" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                </div>
                                <div class="space-y-2 md:col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                    <input type="text" name="best_time" x-model="editingEvent.best_time" placeholder="Pagi / Sore" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
>>>>>>> c877ab79b93880db5dabcb4655b2ab956c1d3c35
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
                                        <input type="time" :name="`schedule[${index}][time]`" x-model="item.time" class="w-28 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium">
                                        <input type="text" :name="`schedule[${index}][activity]`" x-model="item.activity" placeholder="Keterangan kegiatan" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium">
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
                                <input type="file" name="banner" id="banner_modal" class="absolute inset-0 opacity-0 cursor-pointer" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
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
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" x-model="edit_is_active" id="edit_is_active_check" class="w-4 h-4 text-sidebar border-gray-200 rounded-lg focus:ring-sidebar/20">
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
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <!-- Background Backdrop -->
            <div x-show="showCreateModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" 
                 @click="showCreateModal = false"></div>

            <!-- Modal Panel -->
            <div x-show="showCreateModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Event</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div>
                    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                                <input type="text" name="latitude" id="create_latitude" placeholder="Contoh: 2.3361" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                                <input type="text" name="longitude" id="create_longitude" placeholder="Contoh: 99.0494" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium text-gray-700">
                            </div>
                        </div>

                        {{-- Map Picker for Create --}}
                        <div class="col-span-2 space-y-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Event (Klik/Geser untuk mengubah)</label>
                            
                            {{-- Search Box --}}
                            <div class="flex gap-2">
                                <div class="relative flex-1 group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" id="create_location_search" placeholder="Ketik nama lokasi atau alamat..." class="w-full pl-10 pr-12 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all" autocomplete="off">
                                    <button type="button" onclick="performSearch('create_location_search', 'create_map_picker')" class="absolute inset-y-1.5 right-1.5 px-3 bg-sidebar text-white rounded-xl hover:opacity-90 transition-all flex items-center justify-center shadow-sm" title="Cari Lokasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </button>
                                </div>
                                <button type="button" onclick="getCurrentLocation('create_latitude', 'create_longitude', 'create_map_picker')" class="px-4 py-3.5 bg-white border border-gray-100 text-gray-500 rounded-xl hover:bg-gray-50 hover:text-sidebar transition-all shadow-sm flex items-center gap-2" title="Gunakan Lokasi Saya">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-xs font-bold hidden sm:inline">Lokasi Saya</span>
                                </button>
                            </div>

                            <div id="create_map_picker" style="width: 100%; height: 300px; border-radius: 1.5rem; border: 1px solid #f3f4f6; background-color: #f9fafb;" class="flex items-center justify-center text-gray-400 text-xs">Memuat peta...</div>
                            <p class="text-[10px] text-gray-400 italic">*Cari lokasi di atas atau klik/geser marker pada peta</p>
                        </div>

                        <!-- Info Operasional -->
                        <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-4">
<<<<<<< HEAD
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                    <div class="flex items-center gap-2">
                                        <input type="time" x-model="openTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium">
                                        <span class="text-gray-400">-</span>
                                        <input type="time" x-model="closeTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium">
                                    </div>
                                </div>
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                    <input type="text" name="ticket_price" placeholder="Gratis / Rp 10rb" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium">
                                </div>
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                    <input type="text" name="best_time" placeholder="Pagi / Sore" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium">
=======
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                    <div class="flex items-center gap-2">
                                        <input type="time" name="opening_hours_start" x-model="openTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                        <span class="text-gray-400">-</span>
                                        <input type="time" name="opening_hours_end" x-model="closeTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    </div>
                                </div>
                                <div class="space-y-2 md:col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                    <input type="text" name="ticket_price" placeholder="Gratis / Rp 10rb" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                </div>
                                <div class="space-y-2 md:col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                    <input type="text" name="best_time" placeholder="Pagi / Sore" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
>>>>>>> c877ab79b93880db5dabcb4655b2ab956c1d3c35
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
                                        <input type="time" :name="`schedule[${index}][time]`" x-model="item.time" class="w-28 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium">
                                        <input type="text" :name="`schedule[${index}][activity]`" x-model="item.activity" placeholder="Keterangan kegiatan" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all text-sm font-medium">
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
                                <input type="file" name="banner" id="create_banner_modal" class="absolute inset-0 opacity-0 cursor-pointer" @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
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
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" x-model="is_active" id="is_active_check" class="w-4 h-4 text-sidebar border-gray-200 rounded-lg focus:ring-sidebar/20">
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

    {{-- DETAIL EVENT MODAL --}}
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showViewModal = false"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <!-- Header -->
                <div class="flex items-center justify-between px-10 pt-8 pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Detail Event</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Informasi lengkap kegiatan dan acara</p>
                    </div>
                    <button @click="showViewModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-10">
                    <div x-show="loading && !viewingEvent" class="py-12 flex flex-col items-center justify-center gap-4">
                        <div class="w-12 h-12 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-emerald-600 animate-pulse">Memuat data...</p>
                    </div>

                    <div x-show="viewingEvent" class="space-y-8">
                        <!-- Banner Image -->
                        <div class="relative rounded-[2rem] overflow-hidden bg-gray-100 aspect-video group">
                            <template x-if="viewingEvent?.banner_url">
                                <img :src="viewingEvent.banner_url.startsWith('http') ? viewingEvent.banner_url : '/storage/' + viewingEvent.banner_url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="">
                            </template>
                            <template x-if="!viewingEvent?.banner_url">
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-xs font-bold uppercase tracking-widest">Tidak ada foto</p>
                                </div>
                            </template>
                            <div class="absolute top-6 right-6">
                                <span :class="viewingEvent?.is_active ? 'bg-emerald-500' : 'bg-gray-400'" class="px-4 py-2 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg" x-text="viewingEvent?.is_active ? 'AKTIF' : 'NONAKTIF'"></span>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Event</h4>
                                    <p class="text-lg font-bold text-gray-900" x-text="viewingEvent?.name || '-'"></p>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Penyelenggara</h4>
                                    <p class="text-sm font-bold text-emerald-600" x-text="viewingEvent?.organizer || '-'"></p>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Lokasi</h4>
                                    <p class="text-sm font-medium text-gray-600 leading-relaxed" x-text="viewingEvent?.location || '-'"></p>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Deskripsi</h4>
                                    <div class="text-sm text-gray-500 leading-relaxed max-h-40 overflow-y-auto custom-scrollbar pr-2" x-text="viewingEvent?.description || 'Tidak ada deskripsi.'"></div>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Jadwal Operasional</h4>
                                    <p class="text-sm font-bold text-gray-700" x-text="viewingEvent?.opening_hours || '-'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Timeline -->
                        <div class="space-y-4 pt-6 border-t border-gray-50">
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Rangkaian Kegiatan</h4>
                            <div class="space-y-4 relative pl-4">
                                <div class="absolute left-[19px] top-4 bottom-4 w-[1px] bg-gray-100"></div>
                                <template x-if="viewingEvent?.schedule && viewingEvent.schedule.length > 0">
                                    <div class="space-y-4">
                                        <template x-for="item in viewingEvent.schedule">
                                            <div class="flex items-center gap-4 relative z-10">
                                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white shadow-sm"></div>
                                                <div class="flex-1 bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex items-center justify-between">
                                                    <span class="text-sm font-bold text-gray-700" x-text="item.activity"></span>
                                                    <span class="text-xs font-mono font-bold text-emerald-600 bg-white px-2 py-1 rounded-lg" x-text="item.time"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!viewingEvent?.schedule || viewingEvent.schedule.length === 0">
                                    <p class="text-sm text-gray-400 italic">Belum ada rangkaian kegiatan yang ditambahkan.</p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-10 py-6 bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                         <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                         <p class="text-xs text-gray-400 font-medium">Terakhir diperbarui: <span x-text="viewingEvent?.updated_at ? new Date(viewingEvent.updated_at).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '-'"></span></p>
                    </div>
                    <button @click="showViewModal = false" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=Function.prototype" async defer></script>
<script>
    // Handler untuk error autentikasi Google Maps
    window.gm_authFailure = function() {
        console.error('Google Maps authentication failed! Periksa API Key Anda.');
        const pickers = document.querySelectorAll('[id*="map_picker"]');
        pickers.forEach(p => {
            p.innerHTML = '<div class="flex items-center justify-center h-full text-red-500 text-xs font-bold p-4 text-center">Google Maps Error: API Key tidak valid atau belum diaktifkan.</div>';
        });
    };

    let createMap, editMap, createMarker, editMarker;

    function initGoogleMap(elementId, latId, lngId, initialPos = { lat: 2.3361, lng: 99.0631 }) {
        if (typeof google === 'undefined') {
            console.warn('Google Maps API not yet loaded');
            return null;
        }

        const mapElement = document.getElementById(elementId);
        if (!mapElement) return null;

        const map = new google.maps.Map(mapElement, {
            zoom: 13,
            center: initialPos,
            mapTypeControl: false,
            streetViewControl: false,
        });

        const marker = new google.maps.Marker({
            position: initialPos,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
        });

        const updateInputs = (pos) => {
            const latInput = document.getElementById(latId);
            const lngInput = document.getElementById(lngId);
            if (latInput) {
                latInput.value = pos.lat().toFixed(8);
                latInput.dispatchEvent(new Event('input'));
            }
            if (lngInput) {
                lngInput.value = pos.lng().toFixed(8);
                lngInput.dispatchEvent(new Event('input'));
            }
        };

        const latInput = document.getElementById(latId);
        const lngInput = document.getElementById(lngId);
        if (latInput && lngInput) {
            const updateFromInput = () => {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    const pos = { lat, lng };
                    marker.setPosition(pos);
                    map.setCenter(pos);
                }
            };
            latInput.addEventListener('change', updateFromInput);
            lngInput.addEventListener('change', updateFromInput);
        }

        // --- Location Search Feature using standard Autocomplete ---
        const searchInputId = elementId.includes('create') ? 'create_location_search' : 'edit_location_search';
        const searchInput = document.getElementById(searchInputId);

        if (searchInput && typeof google.maps.places.Autocomplete !== 'undefined') {
            const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                componentRestrictions: { country: 'id' },
                fields: ['geometry', 'formatted_address', 'name'],
                types: ['geocode', 'establishment']
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    console.warn("No geometry for place: " + place.name);
                    return;
                }

                const pos = place.geometry.location;
                map.setCenter(pos);
                map.setZoom(17);
                marker.setPosition(pos);
                updateInputs(pos);
                searchInput.value = place.formatted_address || place.name;
            });

            // Handle Enter key
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch(searchInputId, elementId);
                }
            });
        }
        // --- End Search Feature ---

        // Ensure map renders correctly
        setTimeout(() => {
            google.maps.event.trigger(map, "resize");
            map.setCenter(initialPos);
        }, 300);

        map.addListener("click", (e) => {
            marker.setPosition(e.latLng);
            updateInputs(e.latLng);
        });

        marker.addListener("dragend", () => {
            updateInputs(marker.getPosition());
        });

        return { map, marker };
    }

    setInterval(() => {
        const el = document.getElementById('event-manager');
        if (el && window.Alpine) {
            const data = Alpine.$data(el);
            
            if (data) {
                const mapsReady = typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.Map !== 'undefined';
                
                // Initialize Create Map
                if (data.showCreateModal && !createMap && mapsReady) {
                    createMap = true; // Temporary flag
                    setTimeout(() => {
                        const res = initGoogleMap('create_map_picker', 'create_latitude', 'create_longitude');
                        if(res) {
                            createMap = res.map;
                            createMarker = res.marker;
                        } else { createMap = null; }
                    }, 500);
                } else if (!data.showCreateModal) {
                    createMap = null;
                }

                // Initialize Edit Map
                if (data.showEditModal && data.editingEvent && !editMap && mapsReady) {
                    editMap = true; // Temporary flag
                    setTimeout(() => {
                        const pos = { 
                            lat: parseFloat(data.editingEvent.latitude) || 2.3361, 
                            lng: parseFloat(data.editingEvent.longitude) || 99.0631 
                        };
                        const res = initGoogleMap('edit_map_picker', 'edit_latitude', 'edit_longitude', pos);
                        if(res) {
                            editMap = res.map;
                            editMarker = res.marker;
                        } else { editMap = null; }
                    }, 500);
                } else if (!data.showEditModal) {
                    editMap = null;
                }
            }
        }
    }, 500);

    // Get current user location
    function getCurrentLocation(latId, lngId, mapElementId) {
        if (!navigator.geolocation) {
            alert("Geolocation tidak didukung oleh browser Anda.");
            return;
        }

        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                const latInput = document.getElementById(latId);
                const lngInput = document.getElementById(lngId);
                if (latInput) {
                    latInput.value = pos.lat.toFixed(8);
                    latInput.dispatchEvent(new Event('input'));
                }
                if (lngInput) {
                    lngInput.value = pos.lng.toFixed(8);
                    lngInput.dispatchEvent(new Event('input'));
                }

                // Update Map & Marker if they exist
                const isCreate = mapElementId.includes('create');
                const map = isCreate ? createMap : editMap;
                const marker = isCreate ? createMarker : editMarker;

                if (map && marker && typeof map !== 'boolean') {
                    map.setCenter(pos);
                    map.setZoom(17);
                    marker.setPosition(pos);
                }

                btn.innerHTML = originalContent;
                btn.disabled = false;
            },
            (error) => {
                console.error("Geolocation error:", error);
                alert("Gagal mengambil lokasi: " + error.message);
                btn.innerHTML = originalContent;
                btn.disabled = false;
            },
            { enableHighAccuracy: true }
        );
    }

    // Perform Geocoding Search
    function performSearch(inputId, mapElementId) {
        const query = document.getElementById(inputId).value;
        if (!query || query.trim().length < 3) return;

        const isCreate = mapElementId.includes('create');
        const map = isCreate ? createMap : editMap;
        const marker = isCreate ? createMarker : editMarker;
        const latId = isCreate ? 'create_latitude' : 'edit_latitude';
        const lngId = isCreate ? 'create_longitude' : 'edit_longitude';

        if (!map || typeof map === 'boolean') return;

        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 
            address: query, 
            componentRestrictions: { country: 'id' } 
        }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const pos = results[0].geometry.location;
                map.setCenter(pos);
                map.setZoom(17);
                marker.setPosition(pos);
                
                const latInput = document.getElementById(latId);
                const lngInput = document.getElementById(lngId);
                if (latInput) {
                    latInput.value = pos.lat().toFixed(8);
                    latInput.dispatchEvent(new Event('input'));
                }
                if (lngInput) {
                    lngInput.value = pos.lng().toFixed(8);
                    lngInput.dispatchEvent(new Event('input'));
                }
                
                document.getElementById(inputId).value = results[0].formatted_address;
            } else {
                console.warn('Geocode failed:', status);
            }
        });
    }
</script>
@endpush
