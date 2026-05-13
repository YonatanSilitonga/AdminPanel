@extends('admin.layouts.app')

@section('title', 'Detail Sesi Chatbot')
@section('page_title', 'Detail Sesi Chatbot')
@section('page_description', 'Riwayat percakapan lengkap')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Fitur AI dan Cerdas</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <a href="{{ route('admin.chatbot-logs.index') }}" class="text-gray-400 hover:text-sidebar transition-colors">Chatbot Log</a>
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

{{-- Header Info Card --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex items-center justify-between gap-6">
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Session ID</p>
            <p class="font-mono text-lg font-bold text-gray-800 mt-1">{{ substr($sessionId, 0, 20) }}...</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">User Type</p>
            <p class="text-lg font-bold mt-1">
                @if($userId)
                    <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">👤 Login User</span>
                @else
                    <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">👥 Guest</span>
                @endif
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Total Messages</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($messages) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Last Updated</p>
            <p class="text-sm font-semibold text-gray-800 mt-1">
                {{ $session->updated_at ? \Carbon\Carbon::parse($session->updated_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
            </p>
        </div>
        <a href="{{ route('admin.chatbot-logs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
            ← Kembali
        </a>
    </div>
</div>

{{-- Chat Container --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-6 py-4">
        <h3 class="font-bold text-white text-lg">Riwayat Percakapan</h3>
        <p class="text-teal-100 text-sm mt-1">{{ count($messages) }} pesan dalam sesi ini</p>
    </div>

    {{-- Chat Messages --}}
    <div class="p-6 space-y-4 max-h-96 overflow-y-auto bg-gray-50" style="max-height: 600px;">
        @forelse($messages as $msg)
            @php
                $role      = $msg['role'] ?? 'unknown';
                $content   = $msg['content'] ?? '';
                $time      = isset($msg['timestamp'])
                    ? \Carbon\Carbon::parse($msg['timestamp'])->setTimezone('Asia/Jakarta')->format('H:i:s')
                    : '';
                $isUser    = $role === 'user';
            @endphp
            <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs">
                    <div class="rounded-2xl px-4 py-3 text-sm {{ $isUser ? 'bg-teal-600 text-white rounded-br-none' : 'bg-gray-200 text-gray-800 rounded-bl-none' }}">
                        {{ $content }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1 {{ $isUser ? 'text-right' : 'text-left' }}">
                        <strong>{{ $isUser ? '👤 User' : '🤖 AI' }}</strong>
                        @if($time) · {{ $time }} @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p>Tidak ada pesan dalam sesi ini</p>
            </div>
        @endforelse
    </div>

    {{-- Stats Footer --}}
    <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 grid grid-cols-3 gap-4">
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Pesan Pengguna</p>
            <p class="text-lg font-bold text-gray-800 mt-1">
                {{ count(collect($messages)->filter(fn($m) => ($m['role'] ?? '') === 'user')) }}
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Pesan AI</p>
            <p class="text-lg font-bold text-gray-800 mt-1">
                {{ count(collect($messages)->filter(fn($m) => ($m['role'] ?? '') !== 'user')) }}
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">User ID</p>
            <p class="text-sm font-mono text-gray-700 mt-1">{{ substr($userId ?? 'guest', 0, 16) }}...</p>
        </div>
    </div>
</div>

@endsection
