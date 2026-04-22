@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Fitur AI dan Cerdas</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Log Rekomendasi</span>
</nav>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.recommendations.export') }}" class="px-4 py-2 bg-primary text-white rounded-lg">Export</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">User</th>
                <th class="text-left px-4 py-3">Query</th>
                <th class="text-left px-4 py-3">Created</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($logs ?? []) as $log)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ optional($log->user)->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $log->query ?? '-' }}</td>
                    <td class="px-4 py-3">{{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.recommendations.show', $log) }}" class="text-blue-600">View</a>
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
