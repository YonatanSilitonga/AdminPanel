@extends('admin.layouts.app')

@section('title', 'Destination Analytics')
@section('page_title', 'Destination Analytics')
@section('page_description', 'Performance per destination')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Destination</th>
                <th class="text-left px-4 py-3">Views</th>
                <th class="text-left px-4 py-3">Bookings</th>
                <th class="text-left px-4 py-3">Rating</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($destinationStats ?? []) as $stat)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $stat->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $stat->views ?? 0 }}</td>
                    <td class="px-4 py-3">{{ $stat->bookings ?? 0 }}</td>
                    <td class="px-4 py-3">{{ $stat->rating ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">No analytics data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
