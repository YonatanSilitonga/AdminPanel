@extends('admin.layouts.app')

@section('title', 'Edit Destination')
@section('page_title', 'Edit Destination')
@section('page_description', 'Update destination information')

@section('content')
<form x-data="destinationForm()" action="{{ route('admin.destinations.update', $destination ?? 0) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name', $destination->name ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category" class="mt-1 w-full border rounded-lg px-4 py-2">
                @foreach(($categories ?? ['park','beach','museum','historical','nature','cultural','religi']) as $category)
                    <option value="{{ $category }}" @selected(old('category', $destination->category ?? '') === $category)>{{ ucfirst($category) }}</option>
                @endforeach
            </select>
            @error('category')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Latitude</label>
            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $destination->latitude ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2" readonly>
            @error('latitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Longitude</label>
            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $destination->longitude ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2" readonly>
            @error('longitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Map Picker with Location Search -->
        <div class="col-span-1 md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Destinasi</label>
            
            <!-- Search Location Box -->
            <div class="mb-3 relative">
                <div class="flex gap-2">
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            id="location_search" 
                            placeholder="Cari lokasi atau alamat..." 
                            class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            autocomplete="off"
                        >
                        <div id="search_suggestions" class="absolute top-full left-0 right-0 bg-white border border-t-0 rounded-b-lg shadow-lg max-h-64 overflow-y-auto z-50 hidden">
                            <!-- Search suggestions akan dimunculkan di sini -->
                        </div>
                    </div>
                    <button 
                        type="button" 
                        id="clear_location_btn"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                        title="Clear search"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <!-- Map -->
            <div id="map_picker" style="width: 100%; height: 400px; border-radius: 8px; border: 1px solid #ddd;"></div>
            <p class="text-xs text-gray-500 mt-2 italic">
                *Cari lokasi menggunakan search box di atas, atau klik/drag marker langsung di peta
            </p>
        </div>


    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Fasilitas (Pisahkan dengan koma)</label>
        <input type="text" name="facilities" value="{{ old('facilities', isset($destination->facilities) ? implode(', ', $destination->facilities) : '') }}" class="mt-1 w-full border rounded-lg px-4 py-2" placeholder="contoh: Toko Suvenir, Toilet Umum">
        @error('facilities')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- Jam Operasional, Tiket, Best Time -->
    @php
        $opening = $destination->opening_hours ?? '08:00 - 17:00';
        $times = explode(' - ', $opening);
        $open = $times[0] ?? '08:00';
        $close = $times[1] ?? '17:00';

        $mainMedia = count($destination->images ?? []) > 0 ? get_media_info($destination->images[0]) : null;
        $mainMediaType = $mainMedia['type'] ?? 'image';
        $mainMediaUrl = $mainMedia['url'] ?? '';

        $galleryImages = [];
        foreach (array_slice($destination->images ?? [], 1) as $img) {
            $galleryImages[] = is_array($img) ? ($img['url'] ?? '') : $img;
        }
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ open_time: '{{ $open }}', close_time: '{{ $close }}' }">
        <div>
            <label class="block text-sm font-medium text-gray-700">Jam Operasional</label>
            <div class="flex items-center gap-2 mt-1">
                <input type="time" x-model="open_time" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-sidebar/20 outline-none">
                <span class="text-gray-400">-</span>
                <input type="time" x-model="close_time" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-sidebar/20 outline-none">
            </div>
            <input type="hidden" name="opening_hours" :value="open_time + ' - ' + close_time">
            @error('opening_hours')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Tiket Masuk</label>
            <input type="text" name="ticket_price" value="{{ old('ticket_price', $destination->ticket_price ?? 'Gratis') }}" class="mt-1 w-full border rounded-lg px-4 py-2" placeholder="Gratis / Rp 10.000">
            @error('ticket_price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Waktu Terbaik</label>
            <input type="text" name="best_time" value="{{ old('best_time', $destination->best_time ?? 'Kapan saja') }}" class="mt-1 w-full border rounded-lg px-4 py-2" placeholder="Pagi Hari / Malam Hari">
            @error('best_time')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3" class="mt-1 w-full border rounded-lg px-4 py-2">{{ old('description', $destination->description ?? '') }}</textarea>
        @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Location</label>
        <input type="text" name="location" value="{{ old('location', $destination->location ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
        @error('location')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- Video settings -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Fitur Video (Khusus Media Utama berupa Video)</label>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Durasi Video -->
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5 pl-0.5">Durasi Video Tampil (Detik)</label>
                <input type="number" name="video_duration" min="1" max="120" value="{{ old('video_duration', $destination->video_duration ?? 10) }}" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                @error('video_duration')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            
            <!-- Autoplay saat siap -->
            <div class="p-4 bg-gray-50 rounded-xl flex items-center justify-between border border-gray-200">
                <div>
                    <p class="font-bold text-gray-800 text-sm">Autoplay saat siap</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Putar video otomatis saat halaman siap</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="video_autoplay" value="0">
                    <input type="checkbox" name="video_autoplay" value="1" @checked(old('video_autoplay', $destination->video_autoplay ?? true)) class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <!-- Loop video -->
            <div class="p-4 bg-gray-50 rounded-xl flex items-center justify-between border border-gray-200">
                <div>
                    <p class="font-bold text-gray-800 text-sm">Loop video</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Ulangi video secara terus menerus</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="video_loop" value="0">
                    <input type="checkbox" name="video_loop" value="1" @checked(old('video_loop', $destination->video_loop ?? true)) class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>

            <!-- Tunggu video siap diputar sebelum autoplay -->
            <div class="p-4 bg-gray-50 rounded-xl flex items-center justify-between border border-gray-200">
                <div>
                    <p class="font-bold text-gray-800 text-sm">Tunggu video siap</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Tunggu buffer video siap sebelum autoplay</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="video_wait_until_ready" value="0">
                    <input type="checkbox" name="video_wait_until_ready" value="1" @checked(old('video_wait_until_ready', $destination->video_wait_until_ready ?? true)) class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <!-- Tipe Media Selection -->
            <div class="space-y-3 mb-4">
                <label class="block text-sm font-medium text-gray-700">Tipe Media Utama</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="media_type" value="image" x-model="selectedMediaType" class="text-primary focus:ring-primary">
                        <span class="text-sm font-semibold text-gray-700">Gambar</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="media_type" value="video" x-model="selectedMediaType" class="text-primary focus:ring-primary">
                        <span class="text-sm font-semibold text-gray-700">Video</span>
                    </label>
                </div>
            </div>

            <!-- Current Media Preview -->
            @if($mainMediaUrl)
                <div class="space-y-2 mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest" x-text="selectedMediaType === 'video' ? 'Video Utama Saat Ini' : 'Gambar Utama Saat Ini'"></label>
                    <div class="relative rounded-2xl overflow-hidden bg-gray-100 h-32 w-full border border-gray-200 group">
                        <template x-if="selectedMediaType === 'video'">
                            <div class="w-full h-full relative">
                                <video src="{{ $mainMediaUrl }}" class="w-full h-full object-cover" controls muted playsinline preload="metadata"></video>
                            </div>
                        </template>
                        <template x-if="selectedMediaType !== 'video'">
                            <img src="{{ $mainMediaUrl }}" class="w-full h-full object-cover" alt="Media Utama Saat Ini">
                        </template>
                    </div>
                </div>
            @endif

            <!-- Replace File Input -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" x-text="selectedMediaType === 'video' ? 'Ganti File Video Utama' : 'Ganti Gambar Utama'"></label>
                <div class="relative group w-full h-36">
                    <input type="file" name="thumbnail" id="thumbnail_edit" :accept="selectedMediaType === 'video' ? 'video/*' : 'image/*'"
                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                 const file = $event.target.files[0];
                                 if (file) {
                                     const reader = new FileReader();
                                     reader.onload = (e) => { editMediaPreview = e.target.result; };
                                     reader.readAsDataURL(file);
                                 } else {
                                     editMediaPreview = '';
                                 }">
                    <label for="thumbnail_edit"
                        class="relative flex flex-col items-center justify-center w-full h-full border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-primary/50 transition-all bg-gray-50/30 overflow-hidden">
                        <template x-if="editMediaPreview">
                            <div class="absolute inset-0 w-full h-full bg-gray-100">
                                <template x-if="selectedMediaType === 'video'">
                                    <video :src="editMediaPreview" class="w-full h-full object-cover" muted autoplay loop></video>
                                </template>
                                <template x-if="selectedMediaType !== 'video'">
                                    <img :src="editMediaPreview" class="w-full h-full object-cover">
                                </template>
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <p class="text-white text-xs font-bold" x-text="selectedMediaType === 'video' ? 'Ganti Video' : 'Ganti Gambar'"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!editMediaPreview">
                            <div class="flex flex-col items-center justify-center text-center px-4">
                                <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-gray-700" x-text="fileName || 'Klik atau seret file baru ke sini'"></p>
                                <p class="text-[10px] text-gray-400 mt-1" x-text="selectedMediaType === 'video' ? 'MP4, MOV, WEBM (Maks. 50MB) - Biarkan kosong jika tidak diubah' : 'PNG, JPG, WEBP (Maks. 10MB) - Biarkan kosong jika tidak diubah'"></p>
                            </div>
                        </template>
                    </label>
                </div>
            </div>
            @error('thumbnail')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div x-data="{
            deletedImages: [],
            existingImages: {{ json_encode($galleryImages) }},
            removeImage(img) {
                this.deletedImages.push(img);
                this.existingImages = this.existingImages.filter(i => i !== img);
            }
        }">
            <label class="block text-sm font-medium text-gray-700">Gallery Media</label>
            <div class="mt-2 mb-3 flex flex-wrap gap-2" x-show="existingImages.length > 0">
                <template x-for="img in existingImages" :key="img">
                    <div class="relative group cursor-pointer border rounded-lg overflow-hidden">
                        <template x-if="!img.match(/\.(mp4|mov|avi|webm|ogg)(?:$|\?)/i)">
                            <img :src="img.startsWith('http') ? img : '/storage/' + img" alt="Gallery Image" class="w-20 h-16 object-cover">
                        </template>
                        <template x-if="img.match(/\.(mp4|mov|avi|webm|ogg)(?:$|\?)/i)">
                            <video :src="img.startsWith('http') ? img : '/storage/' + img" class="w-20 h-16 object-cover" muted playsinline preload="metadata"></video>
                        </template>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button type="button" @click="removeImage(img)" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full transform hover:scale-110 transition-all shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            <template x-for="img in deletedImages" :key="img">
                <input type="hidden" name="delete_images[]" :value="img">
            </template>
            <input type="file" id="video_file_input" name="images[]" accept="image/*,video/*" multiple class="mt-1 w-full border rounded-lg px-4 py-2" @change="handleVideoSelect">
            
            <!-- Video Player Interaktif -->
            <div x-show="selectedVideo" class="mt-6 p-5 border-2 border-blue-300 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Atur Waktu Mulai Video</h3>
                
                <!-- Video Preview -->
                <div class="mb-4 bg-black rounded-lg overflow-hidden">
                    <video id="video_preview" class="w-full h-64 object-contain bg-black" controls>
                        <source id="video_source" src="" type="video/mp4">
                        Browser Anda tidak mendukung video HTML5.
                    </video>
                </div>
                
                <!-- Durasi Info -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Durasi Total</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="formatTime(videoDuration)">00:00</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Waktu Mulai</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="formatTime(startTime)">00:00</p>
                    </div>
                </div>
                
                <!-- Slider untuk Memilih Waktu Mulai -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Waktu Mulai (Drag Slider)</label>
                    <input type="range" id="start_time_slider" min="0" max="0" value="0" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600" @input="startTime = parseInt($event.target.value)">
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>00:00</span>
                        <span x-text="'Maks: ' + formatTime(videoDuration)">Maks: 00:00</span>
                    </div>
                </div>
                
                <!-- Input Waktu Manual -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Atau Masukkan Waktu (dalam detik)</label>
                    <input type="number" id="start_time_input" name="start_time" min="0" x-model.number="startTime" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Masukkan detik, misal: 10">
                </div>
                
                <!-- Tombol Kontrol -->
                <div class="flex gap-2 mb-4">
                    <button type="button" @click="playVideo" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        ▶ Putar Preview
                    </button>
                    <button type="button" @click="pauseVideo" class="flex-1 px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
                        ⏸ Pause
                    </button>
                    <button type="button" @click="jumpToStartTime" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        ⏭ Ke Waktu Mulai
                    </button>
                </div>
                
                <p class="text-xs text-gray-600 bg-blue-100 p-3 rounded-lg">
                    💡 Tip: Gunakan slider atau masukkan angka untuk menentukan pada detik ke berapa video akan dimulai. Klik tombol "⏭ Ke Waktu Mulai" untuk preview.
                </p>
            </div>
            
            @error('images')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            @error('start_time')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.destinations.index') }}" class="px-4 py-2 border rounded-lg">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Save Changes</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Video Player Interaktif dengan Rentang Waktu
document.addEventListener('alpine:init', () => {
    Alpine.data('destinationForm', () => ({
        selectedMediaType: '{{ $mainMediaType }}',
        fileName: '',
        editMediaPreview: '',
        selectedVideo: false,
        videoDuration: 0,
        startTime: 0,
        endTime: 0,
        
        handleVideoSelect(event) {
            const files = event.target.files;
            if (files && files.length > 0) {
                for (let file of files) {
                    if (file.type.startsWith('video/')) {
                        this.selectedVideo = true;
                        const url = URL.createObjectURL(file);
                        const videoSource = document.getElementById('video_source');
                        const videoPreview = document.getElementById('video_preview');
                        
                        videoSource.src = url;
                        videoPreview.load();
                        
                        // Dapatkan durasi saat metadata dimuat
                        videoPreview.onloadedmetadata = () => {
                            this.videoDuration = Math.floor(videoPreview.duration);
                            this.endTime = this.videoDuration; // Set end time ke durasi penuh
                            document.getElementById('start_time_slider').max = this.videoDuration;
                            document.getElementById('end_time_slider').max = this.videoDuration;
                            document.getElementById('end_time_slider').value = this.videoDuration;
                            document.getElementById('end_time_input').value = this.videoDuration;
                        };
                        
                        break; // Hanya proses video pertama
                    }
                }
            }
        },
        
        formatTime(seconds) {
            if (!seconds || isNaN(seconds)) return '00:00';
            const mins = Math.floor(Math.abs(seconds) / 60);
            const secs = Math.floor(Math.abs(seconds) % 60);
            return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        },
        
        updateStartTime(event) {
            this.startTime = parseInt(event.target.value);
            // Pastikan start_time tidak lebih besar dari end_time
            if (this.startTime > this.endTime) {
                this.endTime = this.startTime;
                document.getElementById('end_time_slider').value = this.endTime;
                document.getElementById('end_time_input').value = this.endTime;
            }
        },
        
        updateStartTimeFromInput() {
            const val = parseInt(document.getElementById('start_time_input').value) || 0;
            this.startTime = Math.max(0, Math.min(val, this.endTime));
            document.getElementById('start_time_slider').value = this.startTime;
            document.getElementById('start_time_input').value = this.startTime;
        },
        
        updateEndTime(event) {
            this.endTime = parseInt(event.target.value);
            // Pastikan end_time tidak lebih kecil dari start_time
            if (this.endTime < this.startTime) {
                this.startTime = this.endTime;
                document.getElementById('start_time_slider').value = this.startTime;
                document.getElementById('start_time_input').value = this.startTime;
            }
        },
        
        updateEndTimeFromInput() {
            const val = parseInt(document.getElementById('end_time_input').value) || 0;
            this.endTime = Math.max(this.startTime, Math.min(val, this.videoDuration));
            document.getElementById('end_time_slider').value = this.endTime;
            document.getElementById('end_time_input').value = this.endTime;
        },
        
        playVideo() {
            const video = document.getElementById('video_preview');
            if (video) {
                video.currentTime = this.startTime;
                video.play();
            }
        },
        
        pauseVideo() {
            const video = document.getElementById('video_preview');
            if (video) video.pause();
        },
        
        jumpToStartTime() {
            const video = document.getElementById('video_preview');
            if (video) {
                video.currentTime = this.startTime;
                video.play();
            }
        }
    }));
});
</script>
    let autocompleteService;
    let placesService;
    let map;
    let marker;
    let geocoder;

    function initMap() {
        const initialLat = parseFloat(document.getElementById("latitude").value) || 2.3361;
        const initialLng = parseFloat(document.getElementById("longitude").value) || 99.0631;
        const initialPos = { lat: initialLat, lng: initialLng };
        
        map = new google.maps.Map(document.getElementById("map_picker"), {
            zoom: 14,
            center: initialPos,
            mapTypeControl: true,
            streetViewControl: false,
        });

        geocoder = new google.maps.Geocoder();
        marker = new google.maps.Marker({
            position: initialPos,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
        });

        // Initialize Places Service
        placesService = new google.maps.places.PlacesService(map);

        map.addListener("click", (mapsMouseEvent) => {
            const pos = mapsMouseEvent.latLng;
            marker.setPosition(pos);
            updateInputs(pos.lat(), pos.lng());
            reverseGeocodeAndUpdateSearch(pos.lat(), pos.lng());
        });

        marker.addListener("dragend", () => {
            const pos = marker.getPosition();
            updateInputs(pos.lat(), pos.lng());
            reverseGeocodeAndUpdateSearch(pos.lat(), pos.lng());
        });

        function updateInputs(lat, lng) {
            document.getElementById("latitude").value = lat.toFixed(8);
            document.getElementById("longitude").value = lng.toFixed(8);
        }

        function reverseGeocodeAndUpdateSearch(lat, lng) {
            const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
            geocoder.geocode({ location: latlng }, function(results, status) {
                if (status === "OK" && results[0]) {
                    document.getElementById("location_search").value = results[0].formatted_address;
                }
            });
        }

        // Load initial address
        reverseGeocodeAndUpdateSearch(initialLat, initialLng);

        // Setup location search
        setupLocationSearch();
    }

    function setupLocationSearch() {
        const searchInput = document.getElementById("location_search");
        const suggestionsDiv = document.getElementById("search_suggestions");
        let currentSearchRequest = null;

        // Debounce search
        let searchTimeout;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 3) {
                suggestionsDiv.classList.add("hidden");
                return;
            }

            searchTimeout = setTimeout(() => {
                searchLocations(query);
            }, 300);
        });

        // Hide suggestions when clicking outside
        document.addEventListener("click", function(e) {
            if (e.target !== searchInput && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.classList.add("hidden");
            }
        });

        // Clear button
        document.getElementById("clear_location_btn").addEventListener("click", function() {
            searchInput.value = "";
            suggestionsDiv.classList.add("hidden");
            suggestionsDiv.innerHTML = "";
        });
    }

    function searchLocations(query) {
        const suggestionsDiv = document.getElementById("search_suggestions");
        
        // Use Google Places API for predictions
        const service = new google.maps.places.AutocompleteService();
        
        service.getPlacePredictions({
            input: query,
            componentRestrictions: { country: 'id' }, // Prioritize Indonesia
            types: ['geocode', 'establishment'],
        }, function(predictions, status) {
            suggestionsDiv.innerHTML = "";

            if (status !== google.maps.places.PlacesServiceStatus.OK || !predictions) {
                suggestionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">Lokasi tidak ditemukan</div>';
                suggestionsDiv.classList.remove("hidden");
                return;
            }

            predictions.forEach((prediction, index) => {
                const div = document.createElement("div");
                div.className = "px-4 py-2 cursor-pointer hover:bg-blue-100 transition border-b last:border-b-0";
                div.innerHTML = `
                    <div class="font-medium text-gray-800">${prediction.main_text}</div>
                    <div class="text-xs text-gray-500">${prediction.secondary_text || ''}</div>
                `;

                div.addEventListener("click", function() {
                    selectPlacePrediction(prediction.place_id, prediction.description);
                });

                suggestionsDiv.appendChild(div);
            });

            suggestionsDiv.classList.remove("hidden");
        });
    }

    function selectPlacePrediction(placeId, description) {
        const suggestionsDiv = document.getElementById("search_suggestions");
        const searchInput = document.getElementById("location_search");
        
        // Get place details
        const service = new google.maps.places.PlacesService(map);
        
        service.getDetails({
            placeId: placeId,
            fields: ['formatted_address', 'geometry', 'name']
        }, function(place, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                const location = place.geometry.location;
                const lat = location.lat();
                const lng = location.lng();
                
                // Update map
                marker.setPosition(location);
                map.setCenter(location);
                map.setZoom(15);
                
                // Update inputs
                document.getElementById("latitude").value = lat.toFixed(8);
                document.getElementById("longitude").value = lng.toFixed(8);
                searchInput.value = place.formatted_address;
                
                // Hide suggestions
                suggestionsDiv.classList.add("hidden");
            }
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap" async defer></script>
@endpush
