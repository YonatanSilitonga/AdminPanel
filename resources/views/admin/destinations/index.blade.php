@extends('admin.layouts.app')

@section('title', 'Destinations')
@section('page_title', 'Destinations')
@section('page_description', 'Manage destination content')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <form method="GET" action="{{ route('admin.destinations.index') }}" class="flex flex-col md:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or description"
            class="w-full md:w-64 px-4 py-2 border rounded-lg">
        <select name="category" class="px-4 py-2 border rounded-lg">
            <option value="">All Categories</option>
            @foreach(($categories ?? []) as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>
                    {{ ucfirst($category) }}
                </option>
            @endforeach
        </select>
        <select name="status" class="px-4 py-2 border rounded-lg">
            <option value="">All Status</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Filter</button>
    </form>

    <a href="{{ route('admin.destinations.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg">Add Destination</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Thumbnail</th>
                <th class="text-left px-4 py-3">Name</th>
                <th class="text-left px-4 py-3">Category</th>
                <th class="text-left px-4 py-3">Rating</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-left px-4 py-3">Featured</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($destinations ?? []) as $destination)
                <tr class="border-t">
                    <td class="px-4 py-3">
                        @if(isset($destination->images) && count($destination->images) > 0)
                            <img src="{{ asset('storage/' . $destination->images[0]) }}" alt="{{ $destination->name }}" class="w-16 h-12 object-cover rounded-lg border">
                        @else
                            <div class="w-16 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs text-center p-1">No Image</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-dark">{{ $destination->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $destination->category ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3">{{ $destination->category ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $destination->rating ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ ($destination->is_active ?? false) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ($destination->is_active ?? false) ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ ($destination->is_featured ?? false) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ($destination->is_featured ?? false) ? 'Featured' : 'No' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('admin.destinations.edit', $destination->_id) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.destinations.toggle-featured', $destination->_id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-indigo-600">Toggle Featured</button>
                        </form>
                        <form action="{{ route('admin.destinations.toggle-status', $destination->_id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-amber-600">Toggle Status</button>
                        </form>
                        <form action="{{ route('admin.destinations.destroy', $destination->_id) }}" method="POST" class="inline" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No destinations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (isset($destinations) && method_exists($destinations, 'links'))
    <div class="mt-4">{{ $destinations->links() }}</div>
@endif
@endsection
