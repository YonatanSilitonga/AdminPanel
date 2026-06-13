@extends('admin.layouts.app')

@section('title', 'Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 px-4 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <!-- Top Bar dengan Gradient -->
            <div class="h-2 bg-gradient-to-r from-primary to-blue-400"></div>
            
            <div class="p-8">
                <!-- Error Code Section -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-primary/10 mb-4">
                        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900">404</h1>
                </div>

                <!-- Error Message -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Halaman Tidak Ditemukan</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $message ?? 'Halaman yang Anda cari tidak ada atau telah dipindahkan.' }}</p>
                </div>

                <!-- Path Info (debug) -->
                @if(config('app.debug') && isset($path))
                    <div class="mb-8 p-4 bg-slate-100 rounded-xl text-left text-xs text-gray-700 font-mono border border-gray-200 break-all">
                        <div class="font-bold mb-2 text-gray-900 uppercase text-[10px] tracking-widest">Rute yang Diminta:</div>
                        <code class="text-primary">{{ $path }}</code>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center bg-gradient-to-r from-primary to-blue-500 hover:from-blue-600 hover:to-blue-600 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Kembali
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-sidebar to-sidebar-active hover:from-sidebar-active hover:to-sidebar text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                        Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection