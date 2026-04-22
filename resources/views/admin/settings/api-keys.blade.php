@extends('admin.layouts.app')

@section('title', 'API & Integrasi')

@section('page_title')
@endsection

@section('page_description')
@endsection

@section('content')
<style>
    /* Hide the default empty page title container */
    .mb-5:has(h1:empty) { display: none !important; }
    
    .settings-tab-active {
        color: #066466;
        border-bottom: 3px solid #066466;
        font-weight: 700;
    }
</style>

<!-- Breadcrumb Area -->
<div class="flex items-center gap-2 text-[14px] text-gray-500 mb-6">
    <span>Pengaturan</span>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    <span class="font-bold text-gray-900">API & Integrasi</span>
</div>

<!-- Unified Navigation Tabs -->
<div class="flex items-center gap-8 border-b border-gray-100 mb-8 px-2 overflow-x-auto whitespace-nowrap custom-scrollbar">
    <a href="{{ route('admin.settings.general') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.general') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Pengaturan Umum</a>
    <a href="{{ route('admin.settings.api-keys') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.api-keys') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">API & Integrasi</a>
    <a href="{{ route('admin.settings.ai-config') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.ai-config') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Konfigurasi AI</a>
    <a href="{{ route('admin.settings.audit-logs') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.audit-logs') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Log Audit</a>
</div>

<div class="max-w-4xl">
    <form action="{{ route('admin.settings.api-keys.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
            <div class="px-8 py-8 border-b border-gray-50 flex items-center gap-4 bg-gray-50/30">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Kredensial Layanan Eksternal</h3>
                    <p class="text-sm text-gray-500 font-medium">Kelola kunci akses untuk integrasi peta dan AI</p>
                </div>
            </div>

            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Google Maps API Key -->
                    <div class="space-y-3" x-data="{ show: false }">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Maps API Key (Google Cloud)</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="maps_api_key" value="{{ old('maps_api_key', $settings['maps_api_key'] ?? '') }}" 
                                class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-sidebar/5 focus:border-sidebar outline-none text-[15px] font-semibold text-gray-700 transition-all font-mono">
                            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                        @error('maps_api_key')<p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- AI API Key -->
                    <div class="space-y-3" x-data="{ show: false }">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">AI API Key (OpenAI/Claude)</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="ai_api_key" value="{{ old('ai_api_key', $settings['ai_api_key'] ?? '') }}" 
                                class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-sidebar/5 focus:border-sidebar outline-none text-[15px] font-semibold text-gray-700 transition-all font-mono">
                            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                        @error('ai_api_key')<p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3 px-5 py-3 bg-red-50 text-red-700 rounded-2xl border border-red-100 flex-1 w-full md:w-auto">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="text-[12px] font-bold">Peringatan: Jangan pernah membagikan kunci rahasia ini kepada siapapun!</span>
                    </div>
                    
                    <button type="submit" class="w-full md:w-auto px-10 py-4 bg-sidebar text-white rounded-2xl font-bold hover:bg-sidebar-hover transition-all shadow-lg shadow-sidebar/20 flex items-center justify-center gap-2">
                        <span>Perbarui Kredensial</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Info Card -->
    <div class="mt-8 p-6 bg-blue-50/50 rounded-3xl border border-blue-100/50 flex gap-4">
        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-500 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <h4 class="text-[14px] font-bold text-blue-900 mb-1">Informasi Integrasi</h4>
            <p class="text-[13px] text-blue-700/70 font-medium leading-relaxed">
                Kunci API digunakan untuk menghubungkan platform dengan layanan pihak ketiga. Pastikan Anda telah mengaktifkan penagihan (billing) pada konsol penyedia layanan masing-masing jika diperlukan.
            </p>
        </div>
    </div>
</div>
@endsection
