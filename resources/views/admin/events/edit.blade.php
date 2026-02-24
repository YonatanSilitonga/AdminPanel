@extends('admin.layouts.app')

@section('title', 'Edit Event')
@section('page_title', 'Edit Event')
@section('page_description', 'Update event details')

@section('content')
<form action="{{ route('admin.events.update', $event ?? 0) }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">Event Name</label>
            <input type="text" name="name" value="{{ old('name', $event->name ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Location</label>
            <input type="text" name="location" value="{{ old('location', $event->location ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('location')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date', $event->start_date ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('start_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">End Date</label>
            <input type="date" name="end_date" value="{{ old('end_date', $event->end_date ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('end_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4" class="mt-1 w-full border rounded-lg px-4 py-2">{{ old('description', $event->description ?? '') }}</textarea>
        @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.events.index') }}" class="px-4 py-2 border rounded-lg">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Save Changes</button>
    </div>
</form>
@endsection
