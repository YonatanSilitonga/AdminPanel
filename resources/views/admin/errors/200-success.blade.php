@extends('admin.layouts.app')

@section('title', 'Success')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 px-4 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <!-- Top Bar dengan Gradient -->
            <div class="h-2 bg-gradient-to-r from-secondary to-green-400"></div>
            
            <div class="p-8">
                <!-- Success Icon -->
                <div class="flex justify-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-secondary/10 animate-bounce">
                        <svg class="w-12 h-12 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Success Message -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $title ?? 'Berhasil!' }}</h1>
                    <p class="text-lg text-gray-600 leading-relaxed">{{ $message ?? 'Operasi berhasil diselesaikan!' }}</p>
                </div>

                <!-- Additional Info -->
                @if(isset($details))
                    <div class="mb-8 p-4 bg-secondary/10 rounded-xl border border-secondary/20 text-left">
                        <p class="text-sm text-gray-700">{{ $details }}</p>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="grid {{ isset($redirect_url) ? 'grid-cols-2' : '' }} gap-3">
                    @if(isset($redirect_url))
                        <a href="{{ $redirect_url }}" class="inline-flex items-center justify-center bg-gradient-to-r from-secondary to-green-500 hover:from-green-600 hover:to-green-600 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-105 active:scale-95">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            Lanjutkan
                        </a>
                    @endif
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