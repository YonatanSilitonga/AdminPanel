@extends('admin.layouts.app')

@section('title', 'Chatbot Log')
@section('navbar_title', 'Chatbot Log')
@section('page_title', 'Chatbot Log')
@section('page_description', 'Monitor percakapan chatbot dengan pengunjung')

@section('page_actions')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.chatbot-logs.export', request()->query()) }}" class="flex items-center gap-2 px-8 py-3 bg-emerald-700 text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-emerald-700/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Export CSV
    </a>
    <div class="relative group cursor-pointer inline-flex items-center">
        <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
            <div class="space-y-2">
                <div>
                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Ekspor Log Chatbot</span>
                    <p class="text-slate-200 font-sans leading-relaxed">Mengekspor data rekam jejak riwayat sesi percakapan chatbot dengan para pengunjung (baik terdaftar maupun tamu) ke format file CSV berdasarkan pencarian/filter aktif saat ini.</p>
                </div>
            </div>
            <div class="absolute bottom-full right-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
        </div>
    </div>
</div>
@endsection

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring AI</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Chatbot Log</span>
</nav>
@endsection

@section('content')

<!-- Stats Overview -->
<div class="bg-white rounded-[2rem] border border-gray-100 p-8 mb-8 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="flex items-center gap-4 px-6 first:pl-0">
            <div class="w-1 h-10 bg-emerald-700 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($totalSessions) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400">Total Sesi</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menunjukkan total keseluruhan sesi percakapan chatbot yang pernah terinisiasi.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Halaman log chatbot dan laporan monitoring AI.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8">
            <div class="w-1 h-10 bg-emerald-500 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($userSessions) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400">Sesi Pengguna</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menghitung jumlah sesi percakapan chatbot yang dilakukan oleh pengguna yang telah login.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Statistik keaktifan pengguna terdaftar di modul AI.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8">
            <div class="w-1 h-10 bg-orange-400 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($guestSessions) }}</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400">Sesi Tamu</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menghitung jumlah sesi percakapan chatbot yang diajukan oleh pengguna tanpa login (tamu).</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Statistik keterlibatan pengunjung publik pada asisten AI.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8 last:pr-0">
            <div class="w-1 h-10 bg-blue-400 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">87%</p>
                <div class="flex items-center gap-1.5">
                    <p class="text-[13px] font-bold text-gray-400">Rasio Respon</p>
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3 h-3 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-normal">Menampilkan tingkat efektivitas AI dalam membalas pesan secara instan dan tepat.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                    <p class="text-slate-200 font-normal">Laporan performa asisten cerdas AI.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
    <form method="GET" action="{{ route('admin.chatbot-logs.index') }}" id="filter-form">
        <!-- Persist current sorting -->
        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'updated_at') }}">
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Cari Pengguna -->
            <div class="space-y-2 sm:col-span-2">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                    Cari Pengguna
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-sans">Mencari sesi chatbot berdasarkan nama, email, atau User ID pengguna.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                    <p class="text-slate-200 font-sans">Pemfilteran log percakapan untuk audit sesi pengguna tertentu.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau ID..." 
                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-[#066466] outline-none text-sm transition-all shadow-sm placeholder-gray-400">
                </div>
            </div>

            <!-- Tipe User -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                    Tipe User
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-sans">Memisahkan tipe penanya antara Pengguna Terdaftar (User) atau Pengunjung Anonim (Tamu).</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                    <p class="text-slate-200 font-sans">Analisis demografi pengunjung yang aktif menggunakan asisten AI.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </label>
                <select name="type" onchange="this.form.submit()" 
                    class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-[#066466] transition-all cursor-pointer">
                    <option value="">Semua Tipe</option>
                    <option value="user" @selected(request('type') === 'user')>👤 User</option>
                    <option value="guest" @selected(request('type') === 'guest')>👥 Tamu</option>
                </select>
            </div>

            <!-- Tampilkan & Reset -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                    Tampilkan
                    <div class="relative group cursor-pointer inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                            <div class="space-y-2">
                                <div>
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                    <p class="text-slate-200 font-sans">Menentukan jumlah baris log percakapan yang ditampilkan per halaman.</p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                    <p class="text-slate-200 font-sans">Pagination tabel daftar log chatbot Panel Admin.</p>
                                </div>
                            </div>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                        </div>
                    </div>
                </label>
                <div class="flex items-center gap-2">
                    <select name="per_page" onchange="this.form.submit()" 
                        class="flex-1 px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                        <option value="10" @selected(request('per_page', 10) == 10)>10 Baris</option>
                        <option value="20" @selected(request('per_page', 10) == 20)>20 Baris</option>
                        <option value="50" @selected(request('per_page', 10) == 50)>50 Baris</option>
                        <option value="100" @selected(request('per_page', 10) == 100)>100 Baris</option>
                    </select>
                    @if(request('search') || request('type') || request('per_page') != 10)
                        <a href="{{ route('admin.chatbot-logs.index') }}" class="px-4 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Sessions Table -->
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-white border-b border-gray-50">
                    @php
                        $sortOrder = request('sort_order') === 'asc' ? 'desc' : 'asc';
                        $currentSort = request('sort_by', 'updated_at');
                    @endphp
                    <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Session ID</th>
                    <th class="px-10 py-6 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'user_id', 'sort_order' => ($currentSort === 'user_id' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                            Tipe User
                            <svg class="w-4 h-4 {{ $currentSort === 'user_id' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'user_id' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                            </svg>
                        </a>
                    </th>
                    <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Preview Pesan</th>
                    <th class="px-10 py-6 text-center text-[13px] font-bold text-gray-500 uppercase tracking-wider">Jumlah Pesan</th>
                    <th class="px-10 py-6 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'updated_at', 'sort_order' => ($currentSort === 'updated_at' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                            Waktu Terakhir
                            <svg class="w-4 h-4 {{ $currentSort === 'updated_at' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'updated_at' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                            </svg>
                        </a>
                    </th>
                    <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse($sessions as $session)
                    @php
                        $sessionId = (string) ($session->_id ?? '');
                        $userId    = $session->user_id ?? null;
                        $messages  = $session->messages ?? [];
                        $msgCount  = count($messages);
                        $preview   = '';
                        foreach ($messages as $m) {
                            if (($m['role'] ?? '') === 'user') {
                                $preview = \Illuminate\Support\Str::limit($m['content'] ?? '', 45);
                                break;
                            }
                        }
                    @endphp
                    <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                        <td class="px-10 py-6">
                            <span class="font-mono text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">#{{ substr($sessionId, -8) }}</span>
                        </td>
                        <td class="px-10 py-6">
                            @if($userId)
                                <div class="flex flex-col">
                                    <span class="text-[14px] font-bold text-gray-800">{{ $session->user->name ?? 'User Terdaftar' }}</span>
                                    <span class="text-xs text-gray-400 mt-0.5">{{ $session->user->email ?? 'ID: ' . substr($userId, -8) }}</span>
                                </div>
                            @else
                                <span class="px-4 py-1.5 bg-gray-50 text-gray-500 text-[11px] font-bold rounded-xl uppercase tracking-wider">👥 Tamu</span>
                            @endif
                        </td>
                        <td class="px-10 py-6">
                            <div class="text-[14px] text-gray-700 font-medium italic">{{ $preview ?: '(tidak ada pesan)' }}</div>
                        </td>
                        <td class="px-10 py-6 text-center">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">
                                {{ $msgCount }} Pesan
                            </span>
                        </td>
                        <td class="px-10 py-6">
                            <div class="text-[13px] text-gray-600 font-medium">
                                {{ $session->updated_at ? \Carbon\Carbon::parse($session->updated_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }}
                            </div>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.chatbot-logs.show', $sessionId) }}"
                                   class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-10 py-20 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-sm font-bold">Tidak ada sesi chatbot ditemukan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($sessions->hasPages())
    <div class="px-8 py-6 border-t border-gray-50 flex items-center justify-between bg-white">
        <p class="text-[13px] text-gray-400 font-medium">Menampilkan {{ $sessions->firstItem() }}-{{ $sessions->lastItem() }} dari {{ $sessions->total() }} sesi</p>
        <div class="flex items-center gap-2">
            @if($sessions->onFirstPage())
                <span class="px-4 py-2 text-[13px] font-bold text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">Prev</span>
            @else
                <a href="{{ $sessions->previousPageUrl() }}" class="px-4 py-2 text-[13px] font-bold text-gray-600 bg-gray-100 hover:bg-emerald-600 hover:text-white rounded-lg transition-all">Prev</a>
            @endif
            
            <div class="flex items-center gap-1">
                @foreach($sessions->getUrlRange(max(1, $sessions->currentPage()-1), min($sessions->lastPage(), $sessions->currentPage()+1)) as $page => $url)
                    <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center text-[13px] font-bold {{ $page == $sessions->currentPage() ? 'bg-emerald-700 text-white shadow-lg shadow-emerald-700/30' : 'text-gray-500 hover:bg-gray-100' }} rounded-lg transition-all">{{ $page }}</a>
                @endforeach
            </div>

            @if($sessions->hasMorePages())
                <a href="{{ $sessions->nextPageUrl() }}" class="px-4 py-2 text-[13px] font-bold text-gray-600 bg-gray-100 hover:bg-emerald-600 hover:text-white rounded-lg transition-all">Next</a>
            @else
                <span class="px-4 py-2 text-[13px] font-bold text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection
