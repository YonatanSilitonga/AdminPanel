@extends('admin.layouts.app')

@section('title', 'Edit Event')
@section('page_title', 'Edit Event')
@section('page_description', 'Perbarui detail event dan jadwal pelaksanaan')

@section('content')
<div x-data="{ 
    schedule: {{ json_encode(old('schedule', $event->schedule ?? [['time' => '09:00', 'activity' => '']])) }},
    imagesData: {{ json_encode($event->images_data ?? []) }},
    deletedImages: [],
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Latitude</label>
                    <input type="text" name="latitude" value="{{ old('latitude', $event->latitude) }}" placeholder="Contoh: 2.3361" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    @error('latitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Longitude</label>
                    <input type="text" name="longitude" value="{{ old('longitude', $event->longitude) }}" placeholder="Contoh: 99.0494" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    @error('longitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Quick Info Section (Jam Operasional, Tiket, Best Time) -->
            <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Informasi Operasional & Tiket</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $opening = $event->opening_hours ?? '08:00 - 17:00';
                        $times = explode(' - ', $opening);
                        $open = $times[0] ?? '08:00';
                        $close = $times[1] ?? '17:00';
                    @endphp
                    <div class="space-y-2" x-data="{ open_time: '{{ $open }}', close_time: '{{ $close }}' }">
                        <label class="block text-sm font-semibold text-gray-700">Jam Operasional</label>
                        <div class="flex items-center gap-2">
                            <input type="time" x-model="open_time" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <span class="text-gray-400">-</span>
                            <input type="time" x-model="close_time" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        </div>
                        <input type="hidden" name="opening_hours" :value="open_time + ' - ' + close_time">
                        @error('opening_hours')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Tiket Masuk</label>
                        <input type="text" name="ticket_price" value="{{ old('ticket_price', $event->ticket_price) }}" placeholder="Gratis / Rp 10.000" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        @error('ticket_price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Waktu Terbaik</label>
                        <input type="text" name="best_time" value="{{ old('best_time', $event->best_time) }}" placeholder="Pagi Hari / Malam Hari" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        @error('best_time')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
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
                <label class="block text-sm font-semibold text-gray-700">Foto (Bisa pilih lebih dari 1)</label>
                
                <template x-if="imagesData.length > 0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                        <template x-for="(imgObj, index) in imagesData" :key="imgObj.path">
                            <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100">
                                <img :src="imgObj.url" alt="Foto Event" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                
                                <!-- Tombol Hapus overlay -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" @click="
                                        deletedImages.push(imgObj.path);
                                        imagesData.splice(index, 1);
                                    " class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transform hover:scale-110 transition-all shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="imagesData.length === 0 && '{{ $event->banner_url ?? '' }}' !== ''">
                    <div class="mb-3 relative rounded-xl overflow-hidden group">
                        <img src="{{ image_url($event->banner_url ?? '') }}" alt="Banner saat ini" class="w-full h-48 object-cover shadow-sm border border-gray-100">
                        <p class="text-[10px] text-gray-500 mt-2">Banner saat ini</p>
                    </div>
                </template>
                
                <template x-for="delImg in deletedImages">
                    <input type="hidden" name="delete_images[]" :value="delImg">
                </template>
                
                <div class="relative group">
                    <input type="file" name="images[]" id="images" multiple class="hidden" @change="fileName = $event.target.files.length > 1 ? $event.target.files.length + ' file dipilih' : $event.target.files[0].name">
                    <label for="images" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-primary/50 transition-all">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 text-gray-400 mb-2 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <p class="text-xs text-gray-500 group-hover:text-primary transition-colors" x-text="fileName || 'Tambah foto event'"></p>
                        </div>
                    </label>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">* Mengunggah foto baru akan menambahkannya ke galeri saat ini.</p>
                @error('images')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
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
