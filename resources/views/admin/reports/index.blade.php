@extends('admin.layouts.app')

@section('title', 'Reports')
@section('page_title', 'Reports')
@section('page_description', 'Handle user reports')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Reporter</th>
                <th class="text-left px-4 py-3">Target / Description</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-right px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($reports ?? []) as $report)
                <tr class="border-t">
                    <td class="px-4 py-3">
                        <p class="font-medium text-dark">{{ $report->user_id ?? 'Anonymous' }}</p>
                        <p class="text-xs text-gray-500">{{ $report->created_at?->diffForHumans() }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-dark">{{ optional($report->destination)->name ?? 'General' }}</p>
                        <p class="text-xs text-gray-500 line-clamp-1">{{ $report->description }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($report->status === 'pending') bg-yellow-100 text-yellow-700
                            @elseif($report->status === 'reviewed') bg-blue-100 text-blue-700
                            @elseif($report->status === 'resolved') bg-green-100 text-green-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($report->status ?? 'pending') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 space-x-2 text-right">
                        <a href="{{ route('admin.reports.show', $report->_id) }}" class="text-blue-600">View</a>
                        <form action="{{ route('admin.reports.destroy', $report->_id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this report?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">No reports found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (isset($reports) && method_exists($reports, 'links'))
    <div class="mt-4">{{ $reports->links() }}</div>
@endif
@endsection
