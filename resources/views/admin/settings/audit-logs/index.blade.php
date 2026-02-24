@extends('admin.layouts.app')

@section('title', 'Audit Logs')
@section('page_title', 'Audit Logs')
@section('page_description', 'System audit logs')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Admin</th>
                <th class="text-left px-4 py-3">Action</th>
                <th class="text-left px-4 py-3">Entity</th>
                <th class="text-left px-4 py-3">Time</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($logs ?? []) as $log)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ optional($log->admin)->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $log->action ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $log->entity_type ?? '-' }}</td>
                    <td class="px-4 py-3">{{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.settings.audit-logs.show', $log) }}" class="text-blue-600">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">No audit logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (isset($logs) && method_exists($logs, 'links'))
    <div class="mt-4">{{ $logs->links() }}</div>
@endif
@endsection
