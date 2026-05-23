@extends('admin.layouts.app')

@section('title', 'Edit Destination')
@section('page_title', 'Edit Destination')
@section('page_description', 'Update destination information')

@section('content')
<form action="{{ route('admin.destinations.update', $destination ?? 0) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-6">
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">Thumbnail</label>
            @if(isset($destination->images) && count($destination->images) > 0)
                <div class="mt-2 mb-3">
                    <img src="{{ image_url($destination->images[0]) }}" alt="Current Thumbnail" class="w-32 h-24 object-cover rounded-lg border">
                    <p class="text-xs text-gray-500 mt-1">Current thumbnail</p>
                </div>
            @endif
            <input type="file" name="thumbnail" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('thumbnail')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div x-data="{
            deletedImages: [],
            existingImages: {{ json_encode(array_slice($destination->images ?? [], 1)) }},
            removeImage(img) {
                this.deletedImages.push(img);
                this.existingImages = this.existingImages.filter(i => i !== img);
            }
        }">
            <label class="block text-sm font-medium text-gray-700">Gallery Images</label>
            <div class="mt-2 mb-3 flex flex-wrap gap-2" x-show="existingImages.length > 0">
                <template x-for="img in existingImages" :key="img">
                    <div class="relative group cursor-pointer border rounded-lg overflow-hidden">
                        <img :src="img.startsWith('http') ? img : '/storage/' + img" alt="Gallery Image" class="w-20 h-16 object-cover">
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
            <input type="file" name="images[]" multiple class="mt-1 w-full border rounded-lg px-4 py-2">
            <p class="text-[10px] text-gray-400 italic mt-1">* Mengunggah foto baru akan menambahkannya ke galeri saat ini</p>
            @error('images')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
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
