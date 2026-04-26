@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Settings</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Log Audit</span>
</nav>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Admin</th>
                <th class="text-left px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Action</th>
                <th class="text-left px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Entity</th>
                <th class="text-left px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Time</th>
                <th class="text-right px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($logs ?? []) as $log)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ optional($log->admin)->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $log->action ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $log->entity_type ?? '-' }}</td>
                    <td class="px-4 py-3">{{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}</td>
                    <td class="px-10 py-6 text-right">
                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.settings.audit-logs.show', $log) }}" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="View Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </div>
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
