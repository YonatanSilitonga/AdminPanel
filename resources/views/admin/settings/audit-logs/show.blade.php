@extends('admin.layouts.app')

@section('title', 'Detail Log Audit')
@section('navbar_title', 'Log Audit')
@section('page_title', 'Detail Log Audit')
@section('page_description', 'Informasi lengkap tentang perubahan yang dilakukan admin')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Pengaturan</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <a href="{{ route('admin.settings.audit-logs') }}" class="text-gray-400 hover:text-sidebar transition-colors">Log Audit</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Detail</span>
</nav>
@endsection

@section('content')

<!-- Settings Navigation Tabs -->
@include('admin.settings.partials.tabs')


<div class="max-w-4xl space-y-6">

    <!-- Main Info Card -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-4">
            <div class="w-11 h-11 bg-sidebar/10 rounded-2xl flex items-center justify-center text-sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Informasi Aktivitas</h3>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Data lengkap log audit ini</p>
            </div>
        </div>

        <div class="p-8 space-y-8">
            <!-- Admin, Waktu, IP -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-1.5 mb-3">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Admin</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Nama dan email administrator yang melakukan tindakan ini.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Halaman detail Log Audit.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-sidebar/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-sidebar">{{ strtoupper(substr($log->admin?->name ?? '?', 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $log->admin?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-400">{{ $log->admin?->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-1.5 mb-3">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Waktu</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Waktu dan tanggal pencatatan saat tindakan dilakukan admin.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Halaman detail Log Audit.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <p class="font-bold text-gray-900 text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                </div>

                <div>
                    <div class="flex items-center gap-1.5 mb-3">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">IP Address</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Alamat IP perangkat jaringan yang digunakan admin saat mengakses sistem.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Halaman detail Log Audit.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <p class="font-mono text-sm font-bold text-gray-700">{{ $log->ip_address ?? '-' }}</p>
                </div>
            </div>

            <!-- Action & Entity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-gray-50">
                <div>
                    <div class="flex items-center gap-1.5 mb-3">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Action</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Jenis tindakan yang dilakukan admin (contoh: update_settings, create, delete).</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Halaman detail Log Audit.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    @php
                        $actionColors = [
                            'create'             => 'bg-green-50 text-green-700',
                            'update'             => 'bg-blue-50 text-blue-700',
                            'delete'             => 'bg-red-50 text-red-700',
                            'soft_delete'        => 'bg-orange-50 text-orange-700',
                            'restore'            => 'bg-purple-50 text-purple-700',
                            'approve'            => 'bg-emerald-50 text-emerald-700',
                            'reject'             => 'bg-pink-50 text-pink-700',
                            'toggle_maintenance' => 'bg-yellow-50 text-yellow-700',
                            'update_settings'    => 'bg-indigo-50 text-indigo-700',
                            'update_api_keys'    => 'bg-cyan-50 text-cyan-700',
                            'update_ai_config'   => 'bg-violet-50 text-violet-700',
                        ];
                        $color = $actionColors[$log->action] ?? 'bg-gray-50 text-gray-700';
                    @endphp
                    <span class="inline-block px-4 py-2 {{ $color }} rounded-xl text-sm font-bold uppercase tracking-wider">
                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                    </span>
                </div>

                <div>
                    <div class="flex items-center gap-1.5 mb-3">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Entity</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3 h-3 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Nama model database dan ID objek data yang dikenai tindakan admin.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                        <p class="text-slate-200 font-normal">Halaman detail Log Audit.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-4 py-2 bg-gray-50 text-gray-700 rounded-xl text-sm font-bold">
                            {{ ucfirst(str_replace('_', ' ', $log->entity_type)) }}
                        </span>
                        @if($log->entity_id)
                            <span class="text-xs text-gray-400 font-mono bg-gray-50 px-3 py-2 rounded-xl">
                                #{{ substr($log->entity_id, -8) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Old Values -->
    @if($log->old_values)
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-5 border-b border-gray-50 bg-red-50/40 flex items-center gap-3">
            <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center text-red-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
            </div>
            <div class="flex items-center gap-1.5">
                <h4 class="font-bold text-gray-900 text-sm">Nilai Lama</h4>
                <div class="relative group cursor-pointer inline-flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                        <div class="space-y-2">
                            <div>
                                <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                <p class="text-slate-200 font-normal">Melihat isi data lama (sebelum diubah) dalam format JSON terstruktur.</p>
                            </div>
                            <div class="pt-1.5 border-t border-slate-800">
                                <span class="block font-bold text-red-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                <p class="text-slate-200 font-normal">Halaman detail Log Audit (opsional, jika ada perubahan data).</p>
                            </div>
                        </div>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-8">
            <pre class="bg-gray-50 p-6 rounded-2xl text-xs text-gray-700 overflow-auto max-h-96 border border-gray-100 font-mono leading-relaxed"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
        </div>
    </div>
    @endif

    <!-- New Values -->
    @if($log->new_values)
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-5 border-b border-gray-50 bg-green-50/40 flex items-center gap-3">
            <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <div class="flex items-center gap-1.5">
                <h4 class="font-bold text-gray-900 text-sm">Nilai Baru</h4>
                <div class="relative group cursor-pointer inline-flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                        <div class="space-y-2">
                            <div>
                                <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                <p class="text-slate-200 font-normal">Melihat isi data baru (setelah diubah) dalam format JSON terstruktur.</p>
                            </div>
                            <div class="pt-1.5 border-t border-slate-800">
                                <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Ditampilkan Di</span>
                                <p class="text-slate-200 font-normal">Halaman detail Log Audit (opsional, jika ada perubahan data).</p>
                            </div>
                        </div>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-8">
            <pre class="bg-gray-50 p-6 rounded-2xl text-xs text-gray-700 overflow-auto max-h-96 border border-gray-100 font-mono leading-relaxed"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
        </div>
    </div>
    @endif

    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.settings.audit-logs') }}"
           class="inline-flex items-center gap-2 px-8 py-3.5 bg-gray-50 border border-gray-100 text-gray-700 rounded-2xl font-bold hover:bg-gray-100 transition-all shadow-sm text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Log Audit
        </a>
    </div>

</div>

@endsection
