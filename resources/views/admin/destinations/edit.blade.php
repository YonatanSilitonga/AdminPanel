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
                    <img src="{{ asset('storage/' . $destination->images[0]) }}" alt="Current Thumbnail" class="w-32 h-24 object-cover rounded-lg border">
                    <p class="text-xs text-gray-500 mt-1">Current thumbnail</p>
                </div>
            @endif
            <input type="file" name="thumbnail" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('thumbnail')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Gallery Images</label>
            @if(isset($destination->images) && count($destination->images) > 1)
                <div class="mt-2 mb-3 flex flex-wrap gap-2">
                    @foreach(array_slice($destination->images, 1) as $img)
                        <img src="{{ asset('storage/' . $img) }}" alt="Gallery Image" class="w-20 h-16 object-cover rounded-lg border">
                    @endforeach
                </div>
            @endif
            <input type="file" name="images[]" multiple class="mt-1 w-full border rounded-lg px-4 py-2">
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
