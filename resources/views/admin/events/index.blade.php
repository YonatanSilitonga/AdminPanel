@extends('admin.layouts.app')

@section('title', 'Events')
@section('page_title', 'Events')
@section('page_description', 'Manage events')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.events.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg">Add Event</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Name</th>
                <th class="text-left px-4 py-3">Date</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($events ?? []) as $event)
                <tr class="border-t">
                    <td class="px-4 py-3">
                        <p class="font-medium text-dark">{{ $event->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $event->location ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        {{ optional($event->start_date)->format('d M Y') ?? '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ ($event->is_active ?? false) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ($event->is_active ?? false) ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('admin.events.edit', $event) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.events.toggle-status', $event) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-amber-600">Toggle Status</button>
                        </form>
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">No events found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (isset($events) && method_exists($events, 'links'))
    <div class="mt-4">{{ $events->links() }}</div>
@endif
@endsection
