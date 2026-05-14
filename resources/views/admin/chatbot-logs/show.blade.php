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
                <span class="px-4 py-1.5 bg-[#E6F6F2] text-[#00A884] text-[12px] font-bold rounded-xl uppercase tracking-wider">👤 Login User</span>
            @else
                <span class="px-4 py-1.5 bg-gray-50 text-gray-500 text-[12px] font-bold rounded-xl uppercase tracking-wider">👥 Guest / Tamu</span>
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
