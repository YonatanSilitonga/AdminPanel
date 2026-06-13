@extends('admin.layouts.app')

@section('title', 'Unauthorized')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 px-4 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <!-- Top Bar dengan Gradient -->
            <div class="h-2 bg-gradient-to-r from-info to-cyan-400"></div>
            
            <div class="p-8">
                <!-- Error Code Section -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-info/10 mb-4">
                        <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900">401</h1>
                </div>

                <!-- Error Message -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Autentikasi Diperlukan</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $message ?? 'Sesi Anda telah berakhir. Silakan login kembali untuk melanjutkan.' }}</p>
                </div>

                <!-- Info Box -->
                <div class="mb-8 p-4 bg-info/5 rounded-xl border border-info/20">
                    <p class="text-sm text-gray-700"><span class="font-bold text-info">💡 Tip:</span> Pastikan Anda sudah login dengan akun admin yang valid.</p>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.login') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-info to-cyan-500 hover:from-cyan-600 hover:to-cyan-600 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Login
                    </a>
                    <a href="{{ route('admin.dashboard') ?? route('welcome') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                        Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection