@extends('admin.layouts.app')

@section('title', 'Edit Event')
@section('page_title', 'Edit Event')
@section('page_description', 'Perbarui detail event dan jadwal pelaksanaan')

@section('content')
<div x-data="{ 
    schedule: {{ json_encode(old('schedule', $event->schedule ?? [['time' => '09:00', 'activity' => '']])) }},
    fileName: '',
    addSchedule() {
        this.schedule.push({ time: '09:00', activity: '' });
    },
    removeSchedule(index) {
        this.schedule.splice(index, 1);
    }
}" class="max-w-4xl mx-auto">
    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-8">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl font-bold text-gray-800">Edit Event</h2>
            <a href="{{ route('admin.events.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        </div>

        <div class="space-y-6">
            <!-- Nama Event -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Nama Event</label>
                <input type="text" name="name" value="{{ old('name', $event->name) }}" placeholder="Festival Danau Toba" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Kategori -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Kategori</label>
                <select name="category" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22currentColor%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')">
                    <option value="">Pilih Kategori</option>
                    <option value="Budaya" @selected(old('category', $event->category) == 'Budaya')>Budaya</option>
                    <option value="Adat" @selected(old('category', $event->category) == 'Adat')>Adat</option>
                    <option value="Olahraga" @selected(old('category', $event->category) == 'Olahraga')>Olahraga</option>
                    <option value="Kuliner" @selected(old('category', $event->category) == 'Kuliner')>Kuliner</option>
                </select>
                @error('category')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Tanggal Mulai</label>
                    <div class="relative">
                        <input type="date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                    @error('start_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Tanggal Selesai</label>
                    <div class="relative">
                        <input type="date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                    @error('end_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Lokasi -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" placeholder="Lapangan Balige" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                @error('location')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Deskripsi -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Masukkan deskripsi event..." class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">{{ old('description', $event->description) }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Jadwal Kegiatan -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-semibold text-gray-700">Jadwal Kegiatan</label>
                    <button type="button" @click="addSchedule()" class="flex items-center gap-1 text-primary bg-primary/10 px-3 py-1 rounded-lg text-xs font-bold hover:bg-primary/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in schedule" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="time" :name="`schedule[${index}][time]`" x-model="item.time" class="w-32 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <input type="text" :name="`schedule[${index}][activity]`" x-model="item.activity" placeholder="Pembukaan upacara adat" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <button type="button" @click="removeSchedule(index)" class="text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Foto -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Foto</label>
                @if($event->banner_url)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $event->banner_url) }}" alt="Banner saat ini" class="w-full h-48 object-cover rounded-xl shadow-sm border border-gray-100">
                        <p class="text-[10px] text-gray-500 mt-2">Banner saat ini</p>
                    </div>
                @endif
                <div class="relative group">
                    <input type="file" name="banner" id="banner" class="hidden" @change="fileName = $event.target.files[0].name">
                    <label for="banner" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-primary/50 transition-all">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 text-gray-400 mb-2 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <p class="text-xs text-gray-500 group-hover:text-primary transition-colors" x-text="fileName || 'Ganti foto event'"></p>
                        </div>
                    </label>
                </div>
                @error('banner')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $event->is_active)) class="w-4 h-4 text-primary border-gray-200 rounded-lg focus:ring-primary/20">
                <label for="is_active" class="text-sm font-semibold text-gray-600 cursor-pointer">Setel sebagai Aktif</label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6">
            <button type="submit" class="w-full bg-primary text-white font-bold py-4 rounded-xl hover:opacity-90 transition-opacity shadow-lg shadow-primary/20">Perbarui Event</button>
        </div>
    </form>
</div>

@endsection
