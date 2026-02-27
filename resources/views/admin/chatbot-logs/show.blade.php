@extends('admin.layouts.app')

@section('title', 'Detail Sesi Chatbot')
@section('page_title', 'Detail Sesi Chatbot')
@section('page_description', 'Riwayat percakapan lengkap')

@section('content')

@php
    $sessionId = (string) ($session->_id ?? '');
    $userId    = $session->user_id ?? null;
    $messages  = $session->messages ?? [];
@endphp

{{-- Session Info --}}
<div class="bg-white rounded-lg shadow p-4 mb-6 flex items-center gap-6">
    <div>
        <p class="text-xs text-gray-400">Session ID</p>
        <p class="font-mono text-sm">{{ $sessionId }}</p>
    </div>
    <div>
        <p class="text-xs text-gray-400">User ID</p>
        <p class="font-mono text-sm">{{ $userId ?: 'Tamu (tidak login)' }}</p>
    </div>
    <div>
        <p class="text-xs text-gray-400">Terakhir Update</p>
        <p class="text-sm">
            {{ $session->updated_at ? \Carbon\Carbon::parse($session->updated_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
        </p>
    </div>
    <div>
        <p class="text-xs text-gray-400">Jumlah Pesan</p>
        <p class="text-sm font-semibold">{{ count($messages) }}</p>
    </div>
    <a href="{{ route('admin.chatbot-logs.index') }}" class="ml-auto text-sm text-gray-500 hover:underline">← Kembali</a>
</div>

{{-- Chat Bubbles --}}
<div class="space-y-3 max-w-3xl mx-auto">
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
            <div class="max-w-lg">
                <div class="rounded-2xl px-4 py-2 text-sm
                    {{ $isUser
                        ? 'bg-blue-600 text-white rounded-br-none'
                        : 'bg-gray-200 text-gray-800 rounded-bl-none' }}">
                    {{ $content }}
                </div>
                <p class="text-xs text-gray-400 mt-1 {{ $isUser ? 'text-right' : 'text-left' }}">
                    {{ $isUser ? 'User' : 'AI' }}
                    @if($time) · {{ $time }} @endif
                </p>
            </div>
        </div>
    @empty
        <p class="text-center text-gray-400 py-8">Tidak ada pesan dalam sesi ini.</p>
    @endforelse
</div>

@endsection
