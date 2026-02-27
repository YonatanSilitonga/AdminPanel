@extends('admin.layouts.app')

@section('title', 'Chatbot Logs')
@section('page_title', 'Chatbot Logs')
@section('page_description', 'Monitor percakapan chatbot dari MongoDB')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ number_format($totalSessions) }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Sesi</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ number_format($userSessions) }}</p>
        <p class="text-sm text-gray-500 mt-1">Sesi Pengguna</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-amber-500">{{ number_format($guestSessions) }}</p>
        <p class="text-sm text-gray-500 mt-1">Sesi Tamu</p>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 flex gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Tipe Pengguna</label>
        <select name="type" class="border rounded px-3 py-2 text-sm">
            <option value="">Semua</option>
            <option value="user"  {{ request('type') === 'user'  ? 'selected' : '' }}>Login</option>
            <option value="guest" {{ request('type') === 'guest' ? 'selected' : '' }}>Tamu</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">User ID</label>
        <input name="user_id" value="{{ request('user_id') }}" placeholder="ObjectID..."
               class="border rounded px-3 py-2 text-sm w-56"/>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Filter</button>
    <a href="{{ route('admin.chatbot-logs.index') }}" class="text-gray-500 text-sm px-2 py-2">Reset</a>
</form>

{{-- Table --}}
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Session ID</th>
                <th class="text-left px-4 py-3">User ID</th>
                <th class="text-left px-4 py-3">Pesan</th>
                <th class="text-left px-4 py-3">Terakhir Update</th>
                <th class="text-left px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
                @php
                    $sessionId = (string) ($session->_id ?? '');
                    $userId    = $session->user_id ?? null;
                    $messages  = $session->messages ?? [];
                    $msgCount  = count($messages);
                    $preview   = '';
                    foreach ($messages as $m) {
                        if (($m['role'] ?? '') === 'user') {
                            $preview = \Illuminate\Support\Str::limit($m['content'] ?? '', 60);
                            break;
                        }
                    }
                @endphp
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ substr($sessionId, -8) }}</td>
                    <td class="px-4 py-3">
                        @if($userId)
                            <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-mono">
                                {{ substr($userId, -8) }}
                            </span>
                        @else
                            <span class="inline-block bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">Tamu</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ $preview ?: '(tidak ada pesan)' }}
                        <span class="text-xs text-gray-400 ml-1">({{ $msgCount }} pesan)</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">
                        {{ $session->updated_at ? \Carbon\Carbon::parse($session->updated_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.chatbot-logs.show', $sessionId) }}"
                           class="text-blue-600 hover:underline text-sm">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada sesi chatbot ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($sessions->hasPages())
    <div class="mt-4">{{ $sessions->links() }}</div>
@endif

@endsection
