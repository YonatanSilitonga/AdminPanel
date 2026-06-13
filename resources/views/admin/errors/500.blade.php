@extends('admin.layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 px-4 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <!-- Top Bar dengan Gradient -->
            <div class="h-2 bg-gradient-to-r from-danger to-red-400"></div>
            
            <div class="p-8">
                <!-- Error Code Section -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-danger/10 mb-4 animate-pulse">
                        <svg class="w-12 h-12 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900">500</h1>
                </div>

                <!-- Error Message -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Kesalahan Server</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $message ?? 'Terjadi kesalahan pada server kami. Silakan coba lagi nanti atau hubungi tim support.' }}</p>
                </div>

                <!-- Error Details (jika debug enabled) -->
                @if(config('app.debug'))
                    <div class="mb-8 p-4 bg-slate-900 rounded-xl text-left text-xs text-slate-300 overflow-auto max-h-48 border border-slate-700 font-mono space-y-2">
                        @if(isset($exception))
                            <div>
                                <div class="font-bold text-red-400 uppercase text-[10px] tracking-widest mb-1">Exception:</div>
                                <span class="text-yellow-300 block break-all">{{ get_class($exception) }}</span>
                            </div>
                            <div class="pt-2 border-t border-slate-600">
                                <div class="font-bold text-red-400 uppercase text-[10px] tracking-widest mb-1">Message:</div>
                                <span class="text-slate-200 block break-all">{{ $exception->getMessage() }}</span>
                            </div>
                            <div class="pt-2 border-t border-slate-600">
                                <div class="font-bold text-red-400 uppercase text-[10px] tracking-widest mb-1">File:</div>
                                <span class="text-slate-200 block break-all">{{ $exception->getFile() }}:{{ $exception->getLine() }}</span>
                            </div>
                        @else
                            <div class="text-red-400">{{ $details ?? 'Internal Server Error' }}</div>
                        @endif
                    </div>
                @endif

                <!-- Info Box -->
                <div class="mb-8 p-4 bg-danger/5 rounded-xl border border-danger/20">
                    <p class="text-sm text-gray-700"><span class="font-bold text-danger">⚠️ Info:</span> Tim teknis kami telah diberitahu tentang masalah ini.</p>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center bg-gradient-to-r from-danger to-red-500 hover:from-red-600 hover:to-red-600 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Coba
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