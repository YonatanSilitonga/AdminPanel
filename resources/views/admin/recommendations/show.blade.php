@extends('admin.layouts.app')

@section('title', 'Recommendation Detail')
@section('page_title', 'Recommendation Detail')
@section('page_description', 'Log details')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div>
        <p class="text-sm text-gray-500">User</p>
        <p class="text-dark font-medium">{{ optional($log->user)->name ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Query</p>
        <p class="text-dark font-medium">{{ $log->query ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Response</p>
        <p class="text-dark">{{ $log->response ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Created</p>
        <p class="text-dark">{{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}</p>
    </div>

    <a href="{{ route('admin.recommendations.index') }}" class="px-4 py-2 border rounded-lg">Back</a>
</div>
@endsection
