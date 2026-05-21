@extends('admin.layouts.app')

@section('title', 'Analytics')
@section('page_title', 'Analytics Dashboard')
@section('page_description', 'High-level analytics overview')

@section('content')
<div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 mb-8">
    <div class="flex items-center gap-1.5 mb-6">
        <h2 class="text-lg font-bold text-gray-900">Ringkasan Statistik</h2>
        <div class="relative group cursor-pointer inline-flex items-center">
            <svg class="w-4 h-4 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                <div class="space-y-2">
                    <div>
                        <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                        <p class="text-slate-200 font-sans">Menampilkan gambaran umum interaksi pengguna di aplikasi mobile.</p>
                    </div>
                    <div class="pt-1.5 border-t border-slate-800">
                        <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                        <p class="text-slate-200 font-sans">Dashboard utama Analitik Sistem.</p>
                    </div>
                </div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Tayangan -->
        <div class="p-6 bg-gray-50/50 border border-gray-100 rounded-[1.5rem] transition-all hover:bg-white hover:shadow-md group">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-sidebar transition-colors">Total Tayangan</p>
                <div class="relative group/tooltip cursor-pointer inline-flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover/tooltip:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                        <div class="space-y-2">
                            <div>
                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                <p class="text-slate-200 font-sans">Mengakumulasikan total kunjungan halaman destinasi, event, atau budaya oleh pengguna.</p>
                            </div>
                            <div class="pt-1.5 border-t border-slate-800">
                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                <p class="text-slate-200 font-sans">Statistik ringkasan halaman analitik.</p>
                            </div>
                        </div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                    </div>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($summary['total_views'] ?? 0) }}</p>
        </div>

        <!-- Total Pencarian -->
        <div class="p-6 bg-gray-50/50 border border-gray-100 rounded-[1.5rem] transition-all hover:bg-white hover:shadow-md group">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-sidebar transition-colors">Total Pencarian</p>
                <div class="relative group/tooltip cursor-pointer inline-flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover/tooltip:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                        <div class="space-y-2">
                            <div>
                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                <p class="text-slate-200 font-sans">Mengakumulasikan total pencarian kata kunci yang dilakukan oleh pengguna.</p>
                            </div>
                            <div class="pt-1.5 border-t border-slate-800">
                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                <p class="text-slate-200 font-sans">Statistik ringkasan halaman analitik.</p>
                            </div>
                        </div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                    </div>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($summary['total_searches'] ?? 0) }}</p>
        </div>

        <!-- Pengguna Aktif -->
        <div class="p-6 bg-gray-50/50 border border-gray-100 rounded-[1.5rem] transition-all hover:bg-white hover:shadow-md group">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-sidebar transition-colors">Pengguna Aktif</p>
                <div class="relative group/tooltip cursor-pointer inline-flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover/tooltip:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                        <div class="space-y-2">
                            <div>
                                <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                <p class="text-slate-200 font-sans">Total pengguna terdaftar yang secara aktif menggunakan aplikasi mobile.</p>
                            </div>
                            <div class="pt-1.5 border-t border-slate-800">
                                <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                <p class="text-slate-200 font-sans">Statistik ringkasan halaman analitik.</p>
                            </div>
                        </div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                    </div>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($summary['active_users'] ?? 0) }}</p>
        </div>
    </div>
</div>
@endsection
