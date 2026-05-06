@extends('admin.layouts.app')

@section('title', 'Pengaturan Umum')

@section('content')
<style>
    /* Hide the default empty page title container */
    .mb-5:has(h1:empty) { display: none !important; }
    
    .settings-tab-active {
        color: #6349A5;
        border-bottom: 3px solid #6349A5;
        font-weight: 700;
    }
</style>

<!-- Breadcrumb Area -->
<div class="flex items-center gap-2 text-[14px] text-gray-500 mb-6">
    <span>Pengaturan</span>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    <span class="font-bold text-gray-900">Pengaturan Umum</span>
</div>

<!-- Unified Navigation Tabs -->
<div class="flex items-center gap-8 border-b border-gray-100 mb-8 px-2 overflow-x-auto whitespace-nowrap custom-scrollbar">
    <a href="{{ route('admin.profile') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.profile') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Profil Saya</a>
    <a href="{{ route('admin.settings.general') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.general') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Pengaturan Umum</a>
    <a href="{{ route('admin.settings.api-keys') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.api-keys') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">API & Integrasi</a>
    <a href="{{ route('admin.settings.ai-config') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.ai-config') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Konfigurasi AI</a>
    <a href="{{ route('admin.settings.audit-logs') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.audit-logs') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Log Audit</a>
</div>

<div class="max-w-4xl">
    <form action="{{ route('admin.settings.general.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
            <div class="px-8 py-8 border-b border-gray-50 flex items-center gap-4 bg-gray-50/30">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Identitas Aplikasi</h3>
                    <p class="text-sm text-gray-500 font-medium">Konfigurasi nama situs dan informasi kontak dukungan</p>
                </div>
            </div>

            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Site Name -->
                    <div class="space-y-3">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Nama Situs / Aplikasi</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" 
                            class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-sidebar/5 focus:border-sidebar outline-none text-[15px] font-semibold text-gray-700 transition-all">
                        @error('site_name')<p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Support Email -->
                    <div class="space-y-3">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Email Dukungan (Support)</label>
                        <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? '') }}" 
                            class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-sidebar/5 focus:border-sidebar outline-none text-[15px] font-semibold text-gray-700 transition-all">
                        @error('support_email')<p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50 flex items-center justify-end">
                    <button type="submit" class="w-full md:w-auto px-10 py-4 bg-sidebar text-white rounded-2xl font-bold hover:bg-sidebar-hover transition-all shadow-lg shadow-sidebar/20 flex items-center justify-center gap-2">
                        <span>Simpan Perubahan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Maintenance Mode Card -->
    <div class="mt-8 bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        <div class="p-8 flex items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 {{ ($settings['maintenance_mode'] ?? false) ? 'bg-orange-50 text-orange-600' : 'bg-green-50 text-green-600' }} rounded-2xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Mode Pemeliharaan</h3>
                    <p class="text-sm text-gray-500 font-medium">Batasi akses publik saat melakukan pembaruan sistem</p>
                </div>
            </div>
            
            <form action="{{ route('admin.settings.maintenance') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="relative inline-flex items-center cursor-pointer group">
                    <div class="w-14 h-7 {{ ($settings['maintenance_mode'] ?? false) ? 'bg-orange-500' : 'bg-gray-200' }} rounded-full transition-colors duration-200"></div>
                    <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full transition-transform duration-200 {{ ($settings['maintenance_mode'] ?? false) ? 'translate-x-7' : 'translate-x-0' }}"></div>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
