@extends('admin.layouts.app')

@section('title', 'Review Detail')
@section('page_title', 'Review Detail')
@section('page_description', 'Review information from MongoDB')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div class="flex justify-between items-start">
        <div>
            <h3 class="text-lg font-semibold text-dark">Tourist Review</h3>
            <p class="text-sm text-gray-600">{{ $review->created_at?->format('d M Y H:i') }}</p>
        </div>
        <div class="flex text-yellow-400">
            @for($i = 0; $i < ($review->rating ?? 0); $i++)
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
            @endfor
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-y py-6">
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wider">User ID</p>
            <p class="text-dark font-medium">{{ $review->user_id ?? 'Anonymous' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wider">Destination</p>
            <p class="text-dark font-medium">{{ optional($review->destination)->name ?? 'General' }}</p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500 uppercase tracking-wider">Review Text</p>
            <p class="text-dark mt-2 text-lg italic">"{{ $review->review ?? '-' }}"</p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.reviews.destroy', $review->_id) }}', title: 'Hapus Ulasan', type: 'ulasan', name: {{ json_encode('dari ' . ($review->user_id ?? 'Anonymous')) }} })" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Delete Review</button>
        <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">Back to List</a>
    </div>
</div>
@endsection
