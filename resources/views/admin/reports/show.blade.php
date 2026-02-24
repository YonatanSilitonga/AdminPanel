@extends('admin.layouts.app')

@section('title', 'Report Detail')
@section('page_title', 'Report Detail')
@section('page_description', 'Review and take action')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-dark">Report Information</h3>
        <p class="text-sm text-gray-600">{{ $report->description ?? '-' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-500">Reporter</p>
            <p class="text-dark font-medium">{{ optional($report->user)->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Type</p>
            <p class="text-dark font-medium">{{ $report->type ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="text-dark font-medium">{{ $report->status ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Assigned To</p>
            <p class="text-dark font-medium">{{ optional($report->assignedAdmin)->name ?? '-' }}</p>
        </div>
    </div>

    <form action="{{ route('admin.reports.action', $report ?? 0) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Action Notes</label>
            <textarea name="action_notes" rows="4" class="mt-1 w-full border rounded-lg px-4 py-2">{{ old('action_notes') }}</textarea>
            @error('action_notes')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Submit Action</button>
    </form>

    <div class="flex items-center gap-3">
        <form action="{{ route('admin.reports.resolve', $report ?? 0) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Resolve</button>
        </form>
        <form action="{{ route('admin.reports.flag', $report ?? 0) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg">Flag</button>
        </form>
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border rounded-lg">Back</a>
    </div>
</div>
@endsection
