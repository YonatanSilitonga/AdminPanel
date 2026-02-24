@extends('admin.layouts.app')

@section('title', 'Audit Log Detail')
@section('page_title', 'Audit Log Detail')
@section('page_description', 'Audit log information')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div>
        <p class="text-sm text-gray-500">Admin</p>
        <p class="text-dark font-medium">{{ optional($log->admin)->name ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Action</p>
        <p class="text-dark">{{ $log->action ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Entity</p>
        <p class="text-dark">{{ $log->entity_type ?? '-' }} #{{ $log->entity_id ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Old Values</p>
        <pre class="bg-gray-50 p-3 rounded text-xs">{{ json_encode($log->old_values ?? [], JSON_PRETTY_PRINT) }}</pre>
    </div>
    <div>
        <p class="text-sm text-gray-500">New Values</p>
        <pre class="bg-gray-50 p-3 rounded text-xs">{{ json_encode($log->new_values ?? [], JSON_PRETTY_PRINT) }}</pre>
    </div>

    <a href="{{ route('admin.settings.audit-logs') }}" class="px-4 py-2 border rounded-lg">Back</a>
</div>
@endsection
