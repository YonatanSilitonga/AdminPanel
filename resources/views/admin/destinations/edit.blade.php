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
                @foreach(($categories ?? ['park','beach','museum','historical','nature','cultural']) as $category)
                    <option value="{{ $category }}" @selected(old('category', $destination->category ?? '') === $category)>{{ ucfirst($category) }}</option>
                @endforeach
            </select>
            @error('category')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Latitude</label>
            <input type="text" name="latitude" value="{{ old('latitude', $destination->latitude ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('latitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Longitude</label>
            <input type="text" name="longitude" value="{{ old('longitude', $destination->longitude ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('longitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3" class="mt-1 w-full border rounded-lg px-4 py-2">{{ old('description', $destination->description ?? '') }}</textarea>
        @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Long Description</label>
        <textarea name="long_description" rows="5" class="mt-1 w-full border rounded-lg px-4 py-2">{{ old('long_description', $destination->long_description ?? '') }}</textarea>
        @error('long_description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">Thumbnail</label>
            <input type="file" name="thumbnail" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('thumbnail')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Cover</label>
            <input type="file" name="cover" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('cover')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.destinations.index') }}" class="px-4 py-2 border rounded-lg">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Save Changes</button>
    </div>
</form>
@endsection
