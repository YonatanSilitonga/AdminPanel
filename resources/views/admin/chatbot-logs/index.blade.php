@extends('admin.layouts.app')

@section('title', 'Chatbot Log')
@section('page_title', 'Chatbot Log')
@section('page_description', 'Monitor percakapan chatbot dengan pengunjung')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Fitur AI dan Cerdas</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Chatbot Log</span>
</nav>
@endsection

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium">TOTAL SESI</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalSessions) }}</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium">SESI PENGGUNA</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($userSessions) }}</p>
            </div>
            <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium">SESI TAMU</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">{{ number_format($guestSessions) }}</p>
            </div>
            <div class="p-3 bg-amber-100 rounded-lg">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 4h6m0 0h-6m6 0v6"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium">TINGKAT AKTIVITAS</p>
                <p class="text-3xl font-bold text-teal-600 mt-1">87%</p>
            </div>
            <div class="p-3 bg-teal-100 rounded-lg">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow p-5 mb-6">
    <form method="GET" class="flex gap-3 items-end flex-wrap">
        <div>
            <label class="block text-xs text-gray-600 font-bold mb-2 uppercase">Tipe Pengguna</label>
            <select name="type" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-teal-500">
                <option value="">Semua Tipe</option>
                <option value="user" {{ request('type') === 'user' ? 'selected' : '' }}>Login Pengguna</option>
                <option value="guest" {{ request('type') === 'guest' ? 'selected' : '' }}>Tamu</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-600 font-bold mb-2 uppercase">User ID</label>
            <input name="user_id" value="{{ request('user_id') }}" placeholder="Cari user ID..."
                   class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-56 focus:outline-none focus:border-teal-500"/>
        </div>
        <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-teal-700 transition-colors">
            🔍 Cari
        </button>
        <a href="{{ route('admin.chatbot-logs.index') }}" class="text-gray-600 text-sm px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            Reset
        </a>
    </form>
</div>

{{-- Sessions Table --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-800">Daftar Sesi Chatbot</h3>
        <p class="text-xs text-gray-500 mt-1">Menampilkan {{ $sessions->count() }} dari {{ $sessions->total() }} sesi</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">SESSION ID</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">USER</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">PREVIEW PESAN</th>
                    <th class="text-center px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">PESAN</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">WAKTU TERAKHIR</th>
                    <th class="text-center px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sessions as $session)
                    @php
                        $sessionId = (string) ($session->_id ?? '');
                        $userId    = $session->user_id ?? null;
                        $messages  = $session->messages ?? [];
                        $msgCount  = count($messages);
                        $preview   = '';
                        foreach ($messages as $m) {
                            if (($m['role'] ?? '') === 'user') {
                                $preview = \Illuminate\Support\Str::limit($m['content'] ?? '', 50);
                                break;
                            }
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-semibold text-teal-600">{{ substr($sessionId, -8) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($userId)
                                <span class="inline-block bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-semibold">
                                    👤 User
                                </span>
                            @else
                                <span class="inline-block bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full">👥 Tamu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700 max-w-xs truncate">
                            {{ $preview ?: '(tidak ada pesan)' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                                {{ $msgCount }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">
                            {{ $session->updated_at ? \Carbon\Carbon::parse($session->updated_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.chatbot-logs.show', $sessionId) }}"
                               class="inline-flex items-center justify-center p-2.5 bg-teal-100 text-teal-600 rounded-lg hover:bg-teal-200 transition-colors" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="font-semibold">Tidak ada sesi chatbot ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($sessions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $sessions->links() }}
        </div>
    @endif
</div>

@endsection
