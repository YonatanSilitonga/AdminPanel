@extends('admin.layouts.app')

@section('title', 'Chatbot Log')
@section('navbar_title', 'Chatbot Log')
@section('page_title', 'Chatbot Log')
@section('page_description', 'Monitor percakapan chatbot dengan pengunjung')

@section('page_actions')
<a href="{{ route('admin.chatbot-logs.export', request()->query()) }}" class="flex items-center gap-2 px-8 py-3 bg-emerald-700 text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-emerald-700/20">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
    Export CSV
</a>
@endsection

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Home</a>
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
                <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Total Sesi</p>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8">
            <div class="w-1 h-10 bg-emerald-500 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($userSessions) }}</p>
                <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Sesi Pengguna</p>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8">
            <div class="w-1 h-10 bg-orange-400 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">{{ number_format($guestSessions) }}</p>
                <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Sesi Tamu</p>
            </div>
        </div>
        <div class="flex items-center gap-4 px-8 last:pr-0">
            <div class="w-1 h-10 bg-blue-400 rounded-full"></div>
            <div>
                <p class="text-[28px] font-bold text-gray-900 leading-none mb-1">87%</p>
                <p class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Aktivitas</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
    <form method="GET" action="{{ route('admin.chatbot-logs.index') }}" class="flex flex-wrap w-full gap-4 items-center" id="filter-form">
        <!-- Persist current sorting -->
        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'updated_at') }}">
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

        <div class="flex-1 min-w-[300px] relative">
            <input type="text" name="user_id" value="{{ request('user_id') }}" placeholder="Cari User ID..." class="w-full pl-12 pr-6 py-3 bg-gray-50 border-none rounded-2xl outline-none text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <select name="per_page" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:bg-gray-50 transition-colors">
            <option value="10" @selected(request('per_page', 10) == 10)>10 Baris</option>
            <option value="20" @selected(request('per_page', 10) == 20)>20 Baris</option>
            <option value="50" @selected(request('per_page', 10) == 50)>50 Baris</option>
            <option value="100" @selected(request('per_page', 10) == 100)>100 Baris</option>
        </select>
        
        <select name="type" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-100 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium cursor-pointer hover:bg-gray-50 transition-colors">
            <option value="">Semua Tipe</option>
            <option value="user" @selected(request('type') === 'user')>👤 User</option>
            <option value="guest" @selected(request('type') === 'guest')>👥 Tamu</option>
        </select>

        <a href="{{ route('admin.chatbot-logs.index') }}" class="px-6 py-3 bg-gray-50 border border-gray-100 text-gray-500 rounded-2xl text-sm font-medium hover:bg-gray-100 transition-all shadow-sm">
            Reset
        </a>
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
                                <span class="px-4 py-1.5 bg-[#E6F6F2] text-[#00A884] text-[11px] font-bold rounded-xl uppercase tracking-wider">👤 User</span>
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
