@extends('admin.layouts.app')

@section('title', 'Bad Request')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 px-4 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <!-- Top Bar dengan Gradient -->
            <div class="h-2 bg-gradient-to-r from-warning to-orange-400"></div>
            
            <div class="p-8">
                <!-- Error Code Section -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-warning/10 mb-4">
                        <div class="text-5xl font-black text-warning">!</div>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900">400</h1>
                </div>

                <!-- Error Message -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Request Tidak Valid</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $message ?? 'Permintaan Anda tidak dapat diproses. Silakan periksa input dan coba lagi.' }}</p>
                </div>

                <!-- Error Details (jika ada) -->
                @if(isset($details) && config('app.debug'))
                    <div class="mb-8 p-4 bg-warning/5 rounded-xl text-left text-xs text-warning overflow-auto max-h-40 border border-warning/20 font-mono">
                        <div class="font-bold mb-2 text-warning uppercase text-[10px] tracking-widest">Detail Error:</div>
                        <pre class="whitespace-pre-wrap text-gray-700">{{ is_array($details) ? json_encode($details, JSON_PRETTY_PRINT) : $details }}</pre>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center bg-gradient-to-r from-warning to-yellow-500 hover:from-yellow-600 hover:to-yellow-600 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-105 active:scale-95">
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
