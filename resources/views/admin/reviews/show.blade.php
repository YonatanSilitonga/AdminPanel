@extends('admin.layouts.app')

@section('title', 'Review Detail')
@section('page_title', 'Review Detail')
@section('page_description', 'Review information')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-dark">Review</h3>
        <p class="text-sm text-gray-600">{{ $review->comment ?? '-' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-500">User</p>
            <p class="text-dark font-medium">{{ optional($review->user)->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Destination</p>
            <p class="text-dark font-medium">{{ optional($review->destination)->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Rating</p>
            <p class="text-dark font-medium">{{ $review->rating ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="text-dark font-medium">{{ $review->status ?? '-' }}</p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <form action="{{ route('admin.reviews.approve', $review ?? 0) }}" method="POST">
            @csrf
            @method('PATCH')
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg" type="submit">Approve</button>
        </form>
        <form action="{{ route('admin.reviews.reject', $review ?? 0) }}" method="POST">
            @csrf
            @method('PATCH')
            <button class="px-4 py-2 bg-amber-600 text-white rounded-lg" type="submit">Reject</button>
        </form>
        <form action="{{ route('admin.reviews.destroy', $review ?? 0) }}" method="POST" onsubmit="return confirmDelete()">
            @csrf
            @method('DELETE')
            <button class="px-4 py-2 bg-red-600 text-white rounded-lg" type="submit">Delete</button>
        </form>
        <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 border rounded-lg">Back</a>
    </div>
</div>
@endsection
