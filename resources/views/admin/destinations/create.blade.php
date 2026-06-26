@extends('admin.layouts.app')

@section('title', 'Create Destination')
@section('page_title', 'Create Destination')
@section('page_description', 'Add a new destination')

@section('content')
<form x-data="destinationForm()" action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category" class="mt-1 w-full border rounded-lg px-4 py-2">
                @foreach(($categories ?? ['Alam', 'Budaya & Sejarah', 'Alam dan Budaya', 'Religi', 'Alam dan Religi', 'Budaya']) as $category)
                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ ucfirst($category) }}</option>
                @endforeach
            </select>
            @error('category')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Latitude</label>
            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" class="mt-1 w-full border rounded-lg px-4 py-2" readonly>
            @error('latitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Longitude</label>
            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" class="mt-1 w-full border rounded-lg px-4 py-2" readonly>
            @error('longitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- Map Picker with Location Search -->
        <div class="col-span-1 md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi di Peta</label>
            
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
        <input type="text" name="facilities" value="{{ old('facilities') }}" class="mt-1 w-full border rounded-lg px-4 py-2" placeholder="contoh: Toko Suvenir, Toilet Umum">
        @error('facilities')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- Jam Operasional, Tiket, Best Time -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ open_time: '08:00', close_time: '17:00' }">
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
            <input type="text" name="ticket_price" value="{{ old('ticket_price', 'Gratis') }}" class="mt-1 w-full border rounded-lg px-4 py-2" placeholder="Gratis / Rp 10.000">
            @error('ticket_price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Waktu Terbaik</label>
            <input type="text" name="best_time" value="{{ old('best_time', 'Kapan saja') }}" class="mt-1 w-full border rounded-lg px-4 py-2" placeholder="Pagi Hari / Malam Hari">
            @error('best_time')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3" class="mt-1 w-full border rounded-lg px-4 py-2">{{ old('description') }}</textarea>
        @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Location</label>
        <input type="text" name="location" value="{{ old('location') }}" class="mt-1 w-full border rounded-lg px-4 py-2" placeholder="e.g. Samosir, North Sumatra">
        @error('location')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- Video settings -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Fitur Video (Khusus Media Utama berupa Video)</label>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Durasi Video -->
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5 pl-0.5">Durasi Video Tampil (Detik)</label>
                <input type="number" name="video_duration" min="1" max="120" value="{{ old('video_duration', 10) }}" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
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
                    <input type="checkbox" name="video_autoplay" value="1" @checked(old('video_autoplay', true)) class="sr-only peer">
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
                    <input type="checkbox" name="video_loop" value="1" @checked(old('video_loop', true)) class="sr-only peer">
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
                    <input type="checkbox" name="video_wait_until_ready" value="1" @checked(old('video_wait_until_ready', true)) class="sr-only peer">
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
                        <input type="radio" name="media_type" value="image" x-model="selectedMediaType" class="text-primary focus:ring-primary" checked>
                        <span class="text-sm font-semibold text-gray-700">Gambar</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="media_type" value="video" x-model="selectedMediaType" class="text-primary focus:ring-primary">
                        <span class="text-sm font-semibold text-gray-700">Video</span>
                    </label>
                </div>
            </div>

            <!-- Media File Upload -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" x-text="selectedMediaType === 'video' ? 'File Video Utama' : 'Gambar Utama'"></label>
                <div class="relative group w-full h-36">
                    <input type="file" name="thumbnail" id="thumbnail_create" :accept="selectedMediaType === 'video' ? 'video/*' : 'image/*'" required
                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                        @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                 const file = $event.target.files[0];
                                 if (file) {
                                     const reader = new FileReader();
                                     reader.onload = (e) => { createMediaPreview = e.target.result; };
                                     reader.readAsDataURL(file);
                                 } else {
                                     createMediaPreview = '';
                                 }">
                    <label for="thumbnail_create"
                        class="relative flex flex-col items-center justify-center w-full h-full border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-primary/50 transition-all bg-gray-50/30 overflow-hidden">
                        <template x-if="createMediaPreview">
                            <div class="absolute inset-0 w-full h-full bg-gray-100">
                                <template x-if="selectedMediaType === 'video'">
                                    <video :src="createMediaPreview" class="w-full h-full object-cover" muted autoplay loop></video>
                                </template>
                                <template x-if="selectedMediaType !== 'video'">
                                    <img :src="createMediaPreview" class="w-full h-full object-cover">
                                </template>
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <p class="text-white text-xs font-bold" x-text="selectedMediaType === 'video' ? 'Ganti Video' : 'Ganti Gambar'"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!createMediaPreview">
                            <div class="flex flex-col items-center justify-center text-center px-4">
                                <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Klik atau seret file ke sini'"></p>
                                <p class="text-[10px] text-gray-400 mt-1" x-text="selectedMediaType === 'video' ? 'MP4, MOV, WEBM (Maks. 50MB)' : 'PNG, JPG, WEBP (Maks. 10MB)'"></p>
                            </div>
                        </template>
                    </label>
                </div>
            </div>
            @error('thumbnail')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Gallery Media (Multiple)</label>
            <input type="file" id="video_file_input" name="images[]" accept="image/*,video/*" multiple class="mt-1 w-full border rounded-lg px-4 py-2" @change="handleVideoSelect">
            
            <!-- Video Player Interaktif -->
            <div x-show="selectedVideo" class="mt-6 p-5 border-2 border-blue-300 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Atur Rentang Waktu Video</h3>
                
                <!-- Video Preview -->
                <div class="mb-4 bg-black rounded-lg overflow-hidden">
                    <video id="video_preview" class="w-full h-64 object-contain bg-black" controls>
                        <source id="video_source" src="" type="video/mp4">
                        Browser Anda tidak mendukung video HTML5.
                    </video>
                </div>
                
                <!-- Durasi Info -->
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Durasi Total</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="formatTime(videoDuration)">00:00</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg border border-green-200">
                        <p class="text-xs text-green-600 uppercase tracking-widest font-bold">Mulai Dari</p>
                        <p class="text-2xl font-bold text-green-600" x-text="formatTime(startTime)">00:00</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg border border-red-200">
                        <p class="text-xs text-red-600 uppercase tracking-widest font-bold">Selesai Pada</p>
                        <p class="text-2xl font-bold text-red-600" x-text="formatTime(endTime)">00:00</p>
                    </div>
                </div>
                
                <!-- Slider untuk Waktu Mulai -->
                <div class="mb-6 p-4 bg-white rounded-lg border border-green-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">📍 Waktu Mulai (Detik)</label>
                    <input type="range" id="start_time_slider" min="0" max="0" value="0" class="w-full h-2 bg-green-200 rounded-lg appearance-none cursor-pointer accent-green-600" @input="updateStartTime($event)">
                    <div class="flex justify-between text-xs text-gray-600 mt-2">
                        <span>00:00</span>
                        <input type="number" id="start_time_input" name="start_time" min="0" x-model.number="startTime" class="w-20 border border-green-300 rounded px-2 py-1 text-center" @change="updateStartTimeFromInput">
                        <span x-text="'Maks: ' + formatTime(videoDuration)">Maks: 00:00</span>
                    </div>
                </div>
                
                <!-- Slider untuk Waktu Selesai -->
                <div class="mb-6 p-4 bg-white rounded-lg border border-red-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">⏹️ Waktu Selesai (Detik)</label>
                    <input type="range" id="end_time_slider" min="0" max="0" value="0" class="w-full h-2 bg-red-200 rounded-lg appearance-none cursor-pointer accent-red-600" @input="updateEndTime($event)">
                    <div class="flex justify-between text-xs text-gray-600 mt-2">
                        <span>00:00</span>
                        <input type="number" id="end_time_input" name="end_time" min="0" x-model.number="endTime" class="w-20 border border-red-300 rounded px-2 py-1 text-center" @change="updateEndTimeFromInput">
                        <span x-text="'Maks: ' + formatTime(videoDuration)">Maks: 00:00</span>
                    </div>
                </div>
                
                <!-- Info Durasi Tayang -->
                <div class="mb-4 p-3 bg-blue-100 border border-blue-300 rounded-lg">
                    <p class="text-sm text-blue-800"><strong>⏱️ Durasi Tayang:</strong> <span x-text="formatTime(endTime - startTime)">00:00</span></p>
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
                        ⏩ Ke Mulai
                    </button>
                </div>
                
                <p class="text-xs text-gray-600 bg-blue-100 p-3 rounded-lg">
                    💡 Tip: Gunakan slider atau input untuk menentukan rentang video yang akan ditampilkan. Video akan diputar dari "Mulai Dari" sampai "Selesai Pada".
                </p>
            </div>
            
            @error('images')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            @error('start_time')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            @error('end_time')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.destinations.index') }}" class="px-4 py-2 border rounded-lg">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Create</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Video Player Interaktif dengan Rentang Waktu
document.addEventListener('alpine:init', () => {
    Alpine.data('destinationForm', () => ({
        selectedMediaType: 'image',
        createFileName: '',
        createMediaPreview: '',
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
@endpush

@push('scripts')
<script>
    let map;
    let marker;
    let geocoder;
    let autocompleteService;
    let placesService;

    function initMap() {
        const defaultLocation = { lat: 2.3361, lng: 99.0631 }; // Balige, Toba
        
        map = new google.maps.Map(document.getElementById("map_picker"), {
            zoom: 11,
            center: defaultLocation,
            mapTypeControl: true,
            streetViewControl: false,
        });

        geocoder = new google.maps.Geocoder();
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
        });

        // Initialize Places Service
        placesService = new google.maps.places.PlacesService(map);
        autocompleteService = new google.maps.places.AutocompleteService();

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
        
        // Handle old values
        const oldLat = "{{ old('latitude') }}";
        const oldLng = "{{ old('longitude') }}";
        if (oldLat && oldLng) {
            const oldPos = { lat: parseFloat(oldLat), lng: parseFloat(oldLng) };
            marker.setPosition(oldPos);
            map.setCenter(oldPos);
            reverseGeocodeAndUpdateSearch(oldLat, oldLng);
        }

        // Setup location search
        setupLocationSearch();
    }

    function setupLocationSearch() {
        const searchInput = document.getElementById("location_search");
        const suggestionsDiv = document.getElementById("search_suggestions");
        
        if (!searchInput || !suggestionsDiv) {
            console.error("Search input or suggestions div not found");
            return;
        }

        let searchTimeout;
        
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
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
        const clearBtn = document.getElementById("clear_location_btn");
        if (clearBtn) {
            clearBtn.addEventListener("click", function(e) {
                e.preventDefault();
                searchInput.value = "";
                suggestionsDiv.classList.add("hidden");
                suggestionsDiv.innerHTML = "";
            });
        }
    }

    async function searchLocations(query) {
        const suggestionsDiv = document.getElementById("search_suggestions");
        
        try {
            const request = {
                input: query,
                componentRestrictions: { country: 'id' },
                types: ['geocode'],
                language: 'id'
            };

            suggestionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">Mencari...</div>';
            suggestionsDiv.classList.remove("hidden");

            const predictions = await autocompleteService.getPlacePredictions(request);
            
            suggestionsDiv.innerHTML = "";

            if (!predictions || predictions.length === 0) {
                suggestionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">Lokasi tidak ditemukan</div>';
                suggestionsDiv.classList.remove("hidden");
                return;
            }

            predictions.forEach((prediction) => {
                const div = document.createElement("div");
                div.className = "px-4 py-3 cursor-pointer hover:bg-blue-100 transition border-b last:border-b-0 text-sm";
                
                const mainText = prediction.main_text || '';
                const secondaryText = prediction.secondary_text || '';
                
                div.innerHTML = `
                    <div class="font-medium text-gray-900">${escapeHtml(mainText)}</div>
                    <div class="text-xs text-gray-600">${escapeHtml(secondaryText)}</div>
                `;

                div.addEventListener("click", function() {
                    selectPlacePrediction(prediction.place_id, mainText);
                });

                suggestionsDiv.appendChild(div);
            });

            suggestionsDiv.classList.remove("hidden");
        } catch (error) {
            console.error("Search error:", error);
            suggestionsDiv.innerHTML = '<div class="px-4 py-2 text-red-500">Terjadi kesalahan saat mencari</div>';
            suggestionsDiv.classList.remove("hidden");
        }
    }

    function selectPlacePrediction(placeId, placeName) {
        const suggestionsDiv = document.getElementById("search_suggestions");
        const searchInput = document.getElementById("location_search");
        
        const request = {
            placeId: placeId,
            fields: ['formatted_address', 'geometry', 'name']
        };

        placesService.getDetails(request, (place, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK && place && place.geometry) {
                const location = place.geometry.location;
                const lat = location.lat();
                const lng = location.lng();
                
                // Update map
                if (marker && map) {
                    marker.setPosition(location);
                    map.setCenter(location);
                    map.setZoom(16);
                }
                
                // Update inputs
                document.getElementById("latitude").value = lat.toFixed(8);
                document.getElementById("longitude").value = lng.toFixed(8);
                searchInput.value = place.formatted_address;
                
                // Hide suggestions
                suggestionsDiv.classList.add("hidden");
            } else {
                console.error("Error getting place details:", status);
            }
        });
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Initialize map when page loads
    window.addEventListener('load', initMap);
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places" async defer></script>
@endpush
