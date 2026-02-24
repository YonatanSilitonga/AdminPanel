@extends('admin.layouts.app')

@section('title', 'Chatbot Logs')
@section('page_title', 'Chatbot Logs')
@section('page_description', 'Inspect chatbot conversations')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">User</th>
                <th class="text-left px-4 py-3">Message</th>
                <th class="text-left px-4 py-3">Created</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($logs ?? []) as $log)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ optional($log->user)->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $log->message ?? '-' }}</td>
                    <td class="px-4 py-3">{{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}</td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('admin.chatbot-logs.show', $log) }}" class="text-blue-600">View</a>
                        <form action="{{ route('admin.chatbot-logs.flag', $log) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-amber-600">Flag</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">No logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (isset($logs) && method_exists($logs, 'links'))
    <div class="mt-4">{{ $logs->links() }}</div>
@endif
@endsection
