@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Fitur AI dan Cerdas</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Log Chatbot</span>
</nav>
@endsection

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
                <th class="text-left px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Session ID</th>
                <th class="text-left px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">User ID</th>
                <th class="text-left px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Pesan</th>
                <th class="text-left px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Terakhir Update</th>
                <th class="text-right px-10 py-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
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
                    <td class="px-10 py-6 text-right">
                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.chatbot-logs.show', $sessionId) }}"
                               class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </div>
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
