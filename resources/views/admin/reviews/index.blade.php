@extends('admin.layouts.app')

@section('title', 'Reviews')
@section('page_title', 'Reviews')
@section('page_description', 'Moderate user reviews')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">User</th>
                <th class="text-left px-4 py-3">Destination</th>
                <th class="text-left px-4 py-3">Rating</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($reviews ?? []) as $review)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ optional($review->user)->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ optional($review->destination)->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $review->rating ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ ($review->status ?? '') === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ $review->status ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('admin.reviews.show', $review) }}" class="text-blue-600">View</a>
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-600">Approve</button>
                        </form>
                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-amber-600">Reject</button>
                        </form>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">No reviews found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (isset($reviews) && method_exists($reviews, 'links'))
    <div class="mt-4">{{ $reviews->links() }}</div>
@endif
@endsection
