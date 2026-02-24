@extends('admin.layouts.app')

@section('title', 'Report Analytics')
@section('page_title', 'Report Analytics')
@section('page_description', 'Report trends and resolution')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Type</th>
                <th class="text-left px-4 py-3">Total</th>
                <th class="text-left px-4 py-3">Resolved</th>
                <th class="text-left px-4 py-3">Pending</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($reportStats ?? []) as $stat)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $stat->type ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $stat->total ?? 0 }}</td>
                    <td class="px-4 py-3">{{ $stat->resolved ?? 0 }}</td>
                    <td class="px-4 py-3">{{ $stat->pending ?? 0 }}</td>
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
