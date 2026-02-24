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
                <th class="text-left px-4 py-3">Type</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($reports ?? []) as $report)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ optional($report->user)->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $report->type ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $report->status ?? '-' }}</td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('admin.reports.show', $report) }}" class="text-blue-600">View</a>
                        <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-600">Resolve</button>
                        </form>
                        <form action="{{ route('admin.reports.flag', $report) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-amber-600">Flag</button>
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
