@extends('admin.layouts.app')

@section('title', 'Detail Sesi Chatbot')
@section('navbar_title', 'Detail Sesi Chatbot')
@section('page_title', 'Detail Sesi Chatbot')
@section('page_description', 'Riwayat percakapan lengkap antara AI dan pengunjung')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring AI</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <a href="{{ route('admin.chatbot-logs.index') }}" class="text-gray-400 hover:text-emerald-600 transition-colors">Chatbot Log</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Detail Sesi</span>
</nav>
@endsection

@section('content')

@php
    $sessionId = (string) ($session->_id ?? '');
    $userId    = $session->user_id ?? null;
    $messages  = $session->messages ?? [];
@endphp

<!-- Session Info Header -->
<div class="bg-white rounded-[20px] border border-gray-100 p-8 mb-8 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-8">
        <div class="flex items-center gap-6">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
            <div>
                <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-1">Session ID</p>
                <p class="font-mono text-[15px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg inline-block">#{{ substr($sessionId, 0, 12) }}...</p>
            </div>
        </div>

        <div class="h-12 w-px bg-gray-100 hidden md:block"></div>

        <div>
            <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipe Pengguna</p>
            @if($userId)
                <span class="inline-flex items-center px-4 py-1.5 bg-[#E6F6F2] text-[#00A884] text-[12px] font-bold rounded-xl uppercase tracking-wider border border-[#00A884]/10">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    Login User
                </span>
            @else
                <span class="inline-flex items-center px-4 py-1.5 bg-gray-50 text-gray-500 text-[12px] font-bold rounded-xl uppercase tracking-wider border border-gray-100">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 00-7 7v1h12v-1a7 7 0 00-7-7z"></path></svg>
                    Guest / Tamu
                </span>
            @endif
        </div>

        <div class="h-12 w-px bg-gray-100 hidden md:block"></div>

        <div>
            <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-1">Terakhir Update</p>
            <p class="text-[15px] font-bold text-gray-800">
                {{ $session->updated_at ? \Carbon\Carbon::parse($session->updated_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }}
            </p>
        </div>

        <div class="flex items-center gap-3 ml-auto">
            <a href="{{ route('admin.chatbot-logs.index') }}" class="px-6 py-3 bg-gray-50 text-gray-500 rounded-xl text-[14px] font-bold hover:bg-gray-100 transition-all border border-transparent hover:border-gray-200">
                Kembali
            </a>
            <button
                id="flag-btn"
                onclick="toggleFlagShow('{{ $sessionId }}')"
                data-flagged="{{ ($session->is_flagged ?? false) ? 'true' : 'false' }}"
                title="{{ ($session->is_flagged ?? false) ? 'Batalkan tanda' : 'Tandai sesi ini' }}"
                class="flex items-center gap-2 px-6 py-3 rounded-xl text-[14px] font-bold transition-all border {{ ($session->is_flagged ?? false) ? 'bg-red-50 text-red-500 border-red-100 hover:bg-red-100' : 'bg-gray-50 text-gray-400 border-transparent hover:bg-red-50 hover:text-red-400' }}">
                <svg class="w-4 h-4" fill="{{ ($session->is_flagged ?? false) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 2H21l-3 6 3 6H12.5l-1-2H5a2 2 0 00-2 2z"></path>
                </svg>
                <span id="flag-label">{{ ($session->is_flagged ?? false) ? 'Batalkan Tanda' : 'Tandai Sesi' }}</span>
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Chat Conversation (Main) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-white">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Percakapan</h3>
                <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-[12px] font-bold uppercase tracking-wider">
                    {{ count($messages) }} Pesan
                </span>
            </div>

            <div class="p-8 space-y-8 bg-[#FAFAFA] min-h-[500px] max-h-[700px] overflow-y-auto custom-scrollbar">
                @forelse($messages as $msg)
                    @php
                        $role      = $msg['role'] ?? 'unknown';
                        $content   = $msg['content'] ?? '';
                        $time      = isset($msg['timestamp'])
                            ? \Carbon\Carbon::parse($msg['timestamp'])->setTimezone('Asia/Jakarta')->format('H:i')
                            : '';
                        $isUser    = $role === 'user';
                    @endphp
                    
                    <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }}">
                        <div class="flex flex-col {{ $isUser ? 'items-end' : 'items-start' }} max-w-[80%]">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">{{ $isUser ? 'Pengguna' : 'Chatbot AI' }}</span>
                                @if($time) <span class="text-[11px] text-gray-300">• {{ $time }}</span> @endif
                            </div>
                            
                            <div class="px-6 py-4 rounded-2xl text-[14px] leading-relaxed shadow-sm
                                {{ $isUser 
                                    ? 'bg-emerald-700 text-white rounded-tr-none' 
                                    : 'bg-white text-gray-700 border border-gray-100 rounded-tl-none' }}">
                                {!! nl2br(e($content)) !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                        <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        <p class="text-sm font-bold">Tidak ada pesan dalam sesi ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Stats & Metadata (Sidebar) -->
    <div class="space-y-6">
        <div class="bg-white rounded-[20px] border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Analisis Sesi</h4>
            
            <div class="space-y-6">
                <div class="p-5 bg-gray-50/50 rounded-2xl border border-gray-50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Pesan Pengguna</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ count(collect($messages)->filter(fn($m) => ($m['role'] ?? '') === 'user')) }}
                    </p>
                </div>

                <div class="p-5 bg-gray-50/50 rounded-2xl border border-gray-50">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Respon AI</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ count(collect($messages)->filter(fn($m) => ($m['role'] ?? '') !== 'user')) }}
                    </p>
                </div>

                <div class="p-5 bg-gray-50/50 rounded-2xl border border-gray-50 overflow-hidden">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">User ID Context</p>
                    <p class="text-[13px] font-mono text-emerald-600 truncate bg-white px-3 py-2 rounded-lg border border-gray-100">
                        {{ $userId ?: 'GUEST_MODE' }}
                    </p>
                </div>

                <div id="flag-status-card" class="p-5 rounded-2xl border {{ ($session->is_flagged ?? false) ? 'bg-red-50 border-red-100' : 'bg-gray-50/50 border-gray-50' }}">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Status Tanda</p>
                    @if($session->is_flagged ?? false)
                        <p class="text-[13px] font-bold text-red-500 flex items-center gap-1.5">
                            🚩 Ditandai
                        </p>
                        @if($session->flagged_at)
                            <p class="text-[11px] text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($session->flagged_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                            </p>
                        @endif
                    @else
                        <p class="text-[13px] text-gray-400 font-medium">Belum ditandai</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-emerald-700 rounded-[20px] p-8 shadow-lg shadow-emerald-700/20 text-white relative overflow-hidden">
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            <h4 class="text-sm font-bold mb-2 relative z-10">Monitoring AI</h4>
            <p class="text-emerald-100 text-[13px] leading-relaxed relative z-10">Log ini digunakan untuk memantau kualitas respon AI dan mendeteksi jika ada anomali dalam percakapan.</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleFlagShow(sessionId) {
    const btn = document.getElementById('flag-btn');
    const label = document.getElementById('flag-label');
    const icon = btn.querySelector('svg');
    const statusCard = document.getElementById('flag-status-card');

    fetch(`/admin/chatbot-logs/${sessionId}/flag`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Gagal');

        const nowFlagged = data.is_flagged;
        btn.dataset.flagged = nowFlagged ? 'true' : 'false';
        btn.title = nowFlagged ? 'Batalkan tanda' : 'Tandai sesi ini';
        label.textContent = nowFlagged ? 'Batalkan Tanda' : 'Tandai Sesi';
        icon.setAttribute('fill', nowFlagged ? 'currentColor' : 'none');

        if (nowFlagged) {
            btn.className = 'flex items-center gap-2 px-6 py-3 rounded-xl text-[14px] font-bold transition-all border bg-red-50 text-red-500 border-red-100 hover:bg-red-100';
            statusCard.className = 'p-5 rounded-2xl border bg-red-50 border-red-100';
            statusCard.querySelector('p:last-child').innerHTML = '🚩 <span class="text-[13px] font-bold text-red-500">Ditandai</span>';
        } else {
            btn.className = 'flex items-center gap-2 px-6 py-3 rounded-xl text-[14px] font-bold transition-all border bg-gray-50 text-gray-400 border-transparent hover:bg-red-50 hover:text-red-400';
            statusCard.className = 'p-5 rounded-2xl border bg-gray-50/50 border-gray-50';
            statusCard.querySelector('p:last-child').innerHTML = '<span class="text-[13px] text-gray-400 font-medium">Belum ditandai</span>';
        }

        showToast(nowFlagged ? '🚩 Sesi berhasil ditandai' : '✅ Tanda berhasil dibatalkan', nowFlagged ? 'red' : 'green');
    })
    .catch(err => {
        showToast('Gagal: ' + err.message, 'red');
    });
}

function showToast(message, color) {
    const existing = document.getElementById('flag-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'flag-toast';
    toast.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl text-white text-sm font-bold shadow-lg transition-all duration-300 ${color === 'red' ? 'bg-red-500' : 'bg-emerald-600'}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}
</script>
@endpush
