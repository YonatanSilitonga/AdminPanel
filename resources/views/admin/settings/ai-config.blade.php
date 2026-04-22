@extends('admin.layouts.app')

@section('title', 'Konfigurasi AI')

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

    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: #066466;
        cursor: pointer;
        box-shadow: 0 0 10px rgba(6, 100, 102, 0.3);
        margin-top: -6px;
    }
</style>

<!-- Breadcrumb Area -->
<div class="flex items-center gap-2 text-[14px] text-gray-500 mb-6">
    <span>Pengaturan</span>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    <span class="font-bold text-gray-900">Konfigurasi AI</span>
</div>

<!-- Unified Navigation Tabs -->
<div class="flex items-center gap-8 border-b border-gray-100 mb-8 px-2 overflow-x-auto whitespace-nowrap custom-scrollbar">
    <a href="{{ route('admin.settings.general') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.general') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Pengaturan Umum</a>
    <a href="{{ route('admin.settings.api-keys') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.api-keys') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">API & Integrasi</a>
    <a href="{{ route('admin.settings.ai-config') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.ai-config') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Konfigurasi AI</a>
    <a href="{{ route('admin.settings.audit-logs') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.audit-logs') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Log Audit</a>
</div>

<div class="max-w-4xl">
    <form action="{{ route('admin.settings.ai-config.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
            <div class="px-8 py-8 border-b border-gray-50 flex items-center gap-4 bg-gray-50/30">
                <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Parameter Kecerdasan AI</h3>
                    <p class="text-sm text-gray-500 font-medium">Kontrol akurasi dan kreativitas model AI Anda</p>
                </div>
            </div>

            <div class="p-8 space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-3">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Model Lingua Utama</label>
                        <select name="model_name" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-sidebar/5 focus:border-sidebar outline-none text-[15px] font-semibold text-gray-700 transition-all cursor-pointer appearance-none">
                            @foreach(['gpt-4o', 'gpt-4-turbo', 'gpt-3.5-turbo', 'claude-3-opus', 'claude-3-sonnet'] as $model)
                                <option value="{{ $model }}" @selected(old('model_name', $settings['model_name'] ?? '') === $model)>{{ strtoupper($model) }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 font-medium pl-1 italic">Pilih model yang paling efisien untuk kebutuhan Anda.</p>
                        @error('model_name')<p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pl-1">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest">Temperature</label>
                            <span class="px-2.5 py-1 bg-[#e1f0f1] text-[#066466] font-bold text-[13px] rounded-lg" id="temp-display">{{ old('temperature', $settings['temperature'] ?? '0.7') }}</span>
                        </div>
                        <div class="relative pt-2">
                            <input type="range" name="temperature" min="0" max="2" step="0.1" value="{{ old('temperature', $settings['temperature'] ?? '0.7') }}" 
                                class="w-full h-2 bg-gray-100 rounded-lg appearance-none cursor-pointer accent-[#066466]"
                                oninput="document.getElementById('temp-display').innerText = this.value">
                            <div class="flex justify-between mt-3 text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                                <span>Presisi / Fokus</span>
                                <span>Kreatif / Variatif</span>
                            </div>
                        </div>
                        @error('temperature')<p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50 flex items-center justify-between gap-6">
                    <div class="flex items-center gap-3 px-5 py-3 bg-indigo-50 text-indigo-700 rounded-2xl border border-indigo-100 flex-1">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-[12px] font-bold italic lowercase first-letter:uppercase leading-snug">Parameter ini berdampak langsung pada jumlah penggunaan token dan biaya operasional API.</span>
                    </div>
                    
                    <button type="submit" class="px-10 py-4 bg-sidebar text-white rounded-2xl font-bold hover:bg-sidebar-hover transition-all shadow-lg shadow-sidebar/20 flex items-center gap-2 flex-shrink-0">
                        <span>Terapkan Tuning AI</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Additional Info -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-gray-50 rounded-[1.5rem] border border-gray-100 flex flex-col gap-3">
            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h4 class="text-[14px] font-bold text-gray-900">Keamanan Data</h4>
            <p class="text-[12px] text-gray-500 font-medium">Semua data yang dikirim ke model AI dienkripsi dan mematuhi kebijakan privasi.</p>
        </div>
        <div class="p-6 bg-gray-50 rounded-[1.5rem] border border-gray-100 flex flex-col gap-3">
            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h4 class="text-[14px] font-bold text-gray-900">Latensi Rendah</h4>
            <p class="text-[12px] text-gray-500 font-medium">Model GPT-3.5 Turbo menawarkan respons yang lebih cepat namun sedikit kurang akurat.</p>
        </div>
        <div class="p-6 bg-gray-50 rounded-[1.5rem] border border-gray-100 flex flex-col gap-3">
            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a2 2 0 00-1.96 1.414l-.718 2.153a2 2 0 01-3.04 1.159l-1.358-.905a2 2 0 00-1.745-.333l-2.43.518a2 2 0 01-2.222-2.147l.215-2.584a2 2 0 00-.518-1.579l-1.957-2.31a2 2 0 01.332-3.111l2.13 1.42a2 2 0 00-.916 1.558l.19-2.584a2 2 0 012.35-1.815l2.38.477a2 2 0 001.62-.214l2.15-.718a2 2 0 012.808 2.052l-.19 2.584a2 2 0 00.518 1.579l1.957 2.31a2 2 0 01-.332 3.111l-2.13 1.42a2 2 0 00-.916 1.558l-.19 2.584a2 2 0 01-2.35 1.815l-2.38-.477a2 2 0 00-1.62.214l-2.15.718z"></path></svg>
            </div>
            <h4 class="text-[14px] font-bold text-gray-900">Pemantauan Token</h4>
            <p class="text-[12px] text-gray-500 font-medium">Batas maksimum token dapat disesuaikan untuk mengontrol biaya operasional bulanan.</p>
        </div>
    </div>
</div>
@endsection
