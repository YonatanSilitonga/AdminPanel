@extends('admin.layouts.app')

@section('title', 'Laporan Pengguna')
@section('page_title', 'Laporan')
@section('page_description', 'Tangani laporan dari pengguna')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="#" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Laporan</span>
</nav>
@endsection

@section('content')
<div x-data="{
    showViewModal: false,
    viewingReport: null,
    loading: false,
    savingStatus: false,

    async openViewModal(id) {
        this.loading = true;
        this.showViewModal = true;
        this.viewingReport = null;
        try {
            const res = await fetch(`/admin/reports/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            this.viewingReport = await res.json();
        } catch(e) {
            alert('Gagal mengambil data laporan');
            this.showViewModal = false;
        } finally {
            this.loading = false;
        }
    },

    async updateStatus(newStatus) {
        this.savingStatus = true;
        try {
            const res = await fetch(`/admin/reports/${this.viewingReport._id}/status`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            });
            const result = await res.json();
            if (result.success) {
                this.viewingReport.status = newStatus;
                setTimeout(() => window.location.reload(), 800);
            } else { alert(result.message); }
        } catch(e) {
            alert('Terjadi kesalahan');
        } finally { this.savingStatus = false; }
    },

    statusClass(status) {
        if (status === 'pending') return 'bg-yellow-50 text-yellow-600';
        if (status === 'reviewed') return 'bg-blue-50 text-blue-600';
        if (status === 'resolved') return 'bg-[#E6F6F2] text-[#00A884]';
        return 'bg-gray-100 text-gray-400';
    },

    statusLabel(status) {
        if (status === 'pending') return 'Menunggu';
        if (status === 'reviewed') return 'Ditinjau';
        if (status === 'resolved') return 'Diselesaikan';
        return status;
    }
}">

    {{-- Filter Tab --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 inline-flex mb-8">
        <a href="{{ route('admin.reports.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ !request('status') ? 'bg-sidebar text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">Semua</a>
        <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ request('status') === 'pending' ? 'bg-sidebar text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">Menunggu</a>
        <a href="{{ route('admin.reports.index', ['status' => 'reviewed']) }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ request('status') === 'reviewed' ? 'bg-sidebar text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">Ditinjau</a>
        <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ request('status') === 'resolved' ? 'bg-sidebar text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">Diselesaikan</a>
    </div>

    {{-- Filter Search --}}
    <div class="flex flex-wrap items-center gap-4 mb-8">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-center gap-4">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="relative w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi laporan..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm shadow-sm placeholder-gray-300">
            </div>
            <select name="reason" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium">
                <option value="">Semua Alasan</option>
                @foreach(($reasons ?? []) as $reason)
                    <option value="{{ $reason }}" @selected(request('reason') === $reason)>{{ ucfirst(str_replace('_', ' ', $reason)) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    <tr>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Pelapor</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Target / Alasan</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse(($reports ?? []) as $report)
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-red-50 rounded-full flex items-center justify-center text-red-400 text-xs font-bold">
                                        {{ strtoupper(substr($report->user_id ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ $report->user_id ?? 'Anonim' }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <p class="text-sm font-bold text-gray-800">{{ optional($report->destination)->name ?? 'Umum' }}</p>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">{{ str_replace('_', ' ', $report->reason ?? '-') }}</span>
                            </td>
                            <td class="px-10 py-6 max-w-xs">
                                <p class="text-sm text-gray-500 truncate">{{ $report->description ?? '-' }}</p>
                            </td>
                            <td class="px-10 py-6">
                                @php
                                    $s = $report->status ?? 'pending';
                                    $cls = ['pending'=>'bg-yellow-50 text-yellow-600','reviewed'=>'bg-blue-50 text-blue-600','resolved'=>'bg-[#E6F6F2] text-[#00A884]'];
                                    $lbl = ['pending'=>'Menunggu','reviewed'=>'Ditinjau','resolved'=>'Diselesaikan'];
                                @endphp
                                <span class="{{ $cls[$s] ?? 'bg-gray-100 text-gray-400' }} px-4 py-1.5 rounded-xl font-bold text-xs inline-block">{{ $lbl[$s] ?? ucfirst($s) }}</span>
                            </td>
                            <td class="px-10 py-6">
                                <span class="text-xs text-gray-400 font-medium">{{ $report->created_at?->diffForHumans() ?? '-' }}</span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openViewModal('{{ $report->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.reports.destroy', $report->_id) }}', title: 'Hapus Laporan', type: 'laporan', name: {{ json_encode('dari ' . ($report->user_id ?? 'Anonim')) }} })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-14 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <p class="text-sm font-medium">Tidak ada laporan ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($reports) && method_exists($reports, 'links'))
    <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between">
        <div class="text-gray-400 text-sm font-medium">Menampilkan {{ $reports->count() }} dari {{ $reports->total() }} Laporan</div>
        <div>{{ $reports->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}</div>
    </div>
    @endif

    {{-- VIEW REPORT MODAL --}}
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500/20 backdrop-blur-sm" @click="showViewModal = false"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10">

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Detail Laporan</h3>
                    <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !viewingReport" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>

                <div x-show="viewingReport" class="space-y-5">
                    {{-- Reporter & Status --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-400 font-bold">
                                <span x-text="viewingReport?.user_id?.charAt(0)?.toUpperCase() || 'A'"></span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm" x-text="viewingReport?.user_id || 'Anonim'"></p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold" x-text="viewingReport?.reason?.replace('_', ' ') || '-'"></p>
                            </div>
                        </div>
                        <span :class="statusClass(viewingReport?.status)" class="px-4 py-1.5 rounded-xl font-bold text-xs" x-text="statusLabel(viewingReport?.status)"></span>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi Laporan</label>
                        <p class="text-sm text-gray-700 font-medium leading-relaxed p-4 bg-gray-50 rounded-2xl" x-text="viewingReport?.description || '-'"></p>
                    </div>

                    {{-- Update Status --}}
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Update Status</label>
                        <div class="flex gap-3">
                            <button @click="updateStatus('pending')" :disabled="savingStatus || viewingReport?.status === 'pending'"
                                class="flex-1 py-3 rounded-xl text-sm font-bold transition-all border border-yellow-200 text-yellow-600 hover:bg-yellow-50 disabled:opacity-40 disabled:cursor-not-allowed">
                                Menunggu
                            </button>
                            <button @click="updateStatus('reviewed')" :disabled="savingStatus || viewingReport?.status === 'reviewed'"
                                class="flex-1 py-3 rounded-xl text-sm font-bold transition-all border border-blue-200 text-blue-600 hover:bg-blue-50 disabled:opacity-40 disabled:cursor-not-allowed">
                                Ditinjau
                            </button>
                            <button @click="updateStatus('resolved')" :disabled="savingStatus || viewingReport?.status === 'resolved'"
                                class="flex-1 py-3 rounded-xl text-sm font-bold transition-all border border-[#066466]/30 text-[#066466] hover:bg-[#E6F6F2] disabled:opacity-40 disabled:cursor-not-allowed">
                                Selesai
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button @click="showViewModal = false" class="px-8 py-3 text-sm font-bold text-gray-400 border border-gray-200 rounded-xl hover:text-gray-600 transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
