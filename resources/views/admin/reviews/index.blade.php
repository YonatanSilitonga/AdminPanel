@extends('admin.layouts.app')

@section('title', 'Reviews')
@section('page_title', 'Reviews')
@section('page_description', 'Moderate user reviews')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <tr class="border-t">
                    <td class="px-4 py-3">
                        <p class="font-medium text-dark">{{ $review->user_id ?? 'Anonymous' }}</p>
                        <p class="text-xs text-gray-500">{{ $review->created_at?->diffForHumans() }}</p>
                    </td>
                    <td class="px-4 py-3">{{ optional($review->destination)->name ?? 'General' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex text-yellow-400">
                            @for($i = 0; $i < ($review->rating ?? 0); $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            @endfor
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-xs text-gray-600 line-clamp-2">{{ $review->review ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('admin.reviews.show', $review->_id) }}" class="text-blue-600">View</a>
                        <form action="{{ route('admin.reviews.destroy', $review->_id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this review?')">
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
