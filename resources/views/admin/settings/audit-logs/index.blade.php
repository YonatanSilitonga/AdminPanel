@extends('admin.layouts.app')

@section('title', 'Log Audit')

@section('content')
<style>
    /* Hide the default empty page title container */
    .mb-5:has(h1:empty) { display: none !important; }
    
    .settings-tab-active {
        color: #6349A5;
        border-bottom: 3px solid #6349A5;
        font-weight: 700;
    }
</style>

<!-- Breadcrumb Area -->
<div class="flex items-center gap-2 text-[14px] text-gray-500 mb-6">
    <span>Pengaturan</span>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    <span class="font-bold text-gray-900">Log Audit</span>
</div>

<!-- Unified Navigation Tabs -->
<div class="flex items-center gap-8 border-b border-gray-100 mb-8 px-2 overflow-x-auto whitespace-nowrap custom-scrollbar">
    <a href="{{ route('admin.profile') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.profile') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Profil Saya</a>
    <a href="{{ route('admin.settings.general') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.general') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Pengaturan Umum</a>
    <a href="{{ route('admin.settings.api-keys') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.api-keys') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">API & Integrasi</a>
    <a href="{{ route('admin.settings.ai-config') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.ai-config') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Konfigurasi AI</a>
    <a href="{{ route('admin.settings.audit-logs') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.audit-logs') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Log Audit</a>
</div>
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
