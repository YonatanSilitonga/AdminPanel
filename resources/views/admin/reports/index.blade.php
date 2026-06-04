@extends('admin.layouts.app')

@section('title', 'Laporan Pengguna')
@section('navbar_title', 'Laporan')
@section('page_title', 'Laporan Pengguna')
@section('page_description', 'Tangani dan monitor laporan dari pengguna')

@section('page_actions')
<div class="flex items-center gap-3">
    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-export-modal'))" class="flex items-center gap-2 px-8 py-3 bg-[#066466] text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-[#066466]/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Ekspor Laporan
    </button>
    <div class="relative group cursor-pointer inline-flex items-center">
        <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
            <div class="space-y-2">
                <div>
                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Ekspor Laporan</span>
                    <p class="text-slate-200 font-sans leading-relaxed">Membuka opsi penyaringan laporan masuk untuk diekspor ke dalam format dokumen PDF resmi (dengan kop surat instansi) atau format data spreadsheet CSV/Excel.</p>
                </div>
            </div>
            <div class="absolute bottom-full right-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
        </div>
    </div>
</div>
@endsection

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Ulasan & Laporan</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    @php
        $statusLabels = [
            'pending' => 'Laporan Menunggu',
            'reviewed' => 'Laporan Ditinjau',
            'resolved' => 'Laporan Diselesaikan'
        ];
        $currentLabel = $statusLabels[request('status')] ?? 'Laporan Masuk';
    @endphp
    <span class="text-gray-900 font-bold" id="breadcrumb-active-state">{{ $currentLabel }}</span>
</nav>
@endsection

@section('content')
<div x-on:open-export-modal.window="showExportModal = true" x-data="{
    showViewModal: false,
    showExportModal: false,
    showSuccessModal: false,
    successMessage: '',
    exportFormat: 'pdf',
    instansi: 'PEMERINTAH KABUPATEN TOBA/DINAS KEBUDAYAAN DAN PARIWISATA',
    alamat: 'Jl. Bukit Pagar Batu No. 1, Balige, Kabupaten Toba, Sumatera Utara',
    email: 'disbudpar@tobakab.go.id',
    telp: '(0632) 123456',
    website: 'https://disbudpar.tobakab.go.id',
    nomor_surat: '050/321/Disbudpar/2026',
    hal: 'Laporan Pengaduan dan Penanganan Keluhan Wisatawan',
    nama_penandatangan: 'Sandro M. S. Simanjuntak, S.T., M.Si.',
    nip_penandatangan: '19780512 200501 1 003',
    jabatan: 'Kepala Dinas Kebudayaan dan Pariwisata',

    showConfirmModal: false,
    confirmMessage: '',
    confirmAction: null,

    confirm(message, action) {
        this.confirmMessage = message;
        this.confirmAction = action;
        this.showConfirmModal = true;
    },
    executeConfirm() {
        if (this.confirmAction) this.confirmAction();
        this.showConfirmModal = false;
    },

    submitExport() {
        const form = this.$refs.exportForm;
        if (this.exportFormat === 'pdf') {
            form.action = '{{ route("admin.reports.print") }}';
            form.target = '_blank';
            form.method = 'POST';
        } else {
            form.action = '{{ route("admin.reports.export") }}';
            form.target = '_self';
            form.method = 'GET';
        }
        form.submit();
        setTimeout(() => this.showExportModal = false, 100);
    },
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
            console.log('Report Data:', this.viewingReport);

        } catch(e) {
            window.showAlert('Gagal mengambil data laporan', 'Error', 'error');
            this.showViewModal = false;
        } finally {
            this.loading = false;
        }
    },

    async updateStatus(newStatus) {
        const labels = {
            'pending': 'Menunggu',
            'reviewed': 'Ditinjau',
            'resolved': 'Selesai'
        };
        const statusLabel = labels[newStatus] || newStatus;

        if (this.viewingReport && this.viewingReport.status === newStatus) {
            window.showAlert(`Status laporan sudah bernilai '${statusLabel}'`, 'Info', 'info');
            return;
        }

        this.confirm(`Apakah Anda yakin ingin mengubah status laporan ini menjadi '${statusLabel}'?`, async () => {
            this.savingStatus = true;
            try {
                const reportId = this.viewingReport._id || this.viewingReport.id;
                const res = await fetch(`/admin/reports/${reportId}/status`, {
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
                    localStorage.setItem('pending_success_toast', 'Status laporan berhasil diperbarui');
                    window.location.reload();
                } else { 
                    window.showAlert(result.message || 'Gagal memperbarui status', 'Gagal', 'error'); 
                }
            } catch(e) {
                window.showAlert('Terjadi kesalahan saat memperbarui status', 'Error', 'error');
            } finally { this.savingStatus = false; }
        });
    },

    statusClass(status) {
        if (status === 'pending') return 'bg-amber-50 text-amber-600';
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

    {{-- Filter Search --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex-1">
                {{-- Hidden inputs for sorting persistence --}}
                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Cari Deskripsi -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Cari Laporan
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Mencari laporan masuk berdasarkan kata kunci deskripsi keluhan.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-sans">Proses pencarian dan penyaringan data tabel laporan pengguna.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-300">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi laporan..."
                                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-[#066466] outline-none text-[14px] font-medium placeholder-gray-400 transition-all shadow-sm">
                        </div>
                    </div>

                    <!-- Status Laporan -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Status Laporan
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Menyaring laporan berdasarkan tahapan tindak lanjut yang telah dilakukan.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-sans">Pengawasan proses penyelesaian keluhan pengguna oleh tim moderator.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(request('status') === 'pending')>Menunggu</option>
                            <option value="reviewed" @selected(request('status') === 'reviewed')>Ditinjau</option>
                            <option value="resolved" @selected(request('status') === 'resolved')>Diselesaikan</option>
                        </select>
                    </div>

                    <!-- Alasan Pelanggaran -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Alasan Laporan
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Menyaring laporan berdasarkan kategori pelanggaran atau masalah yang dilaporkan.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-sans">Klasifikasi masalah untuk analisis jenis keluhan terbanyak.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <select name="reason" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                            <option value="">Semua Alasan</option>
                            @foreach(($reasons ?? []) as $reason)
                                <option value="{{ $reason }}" @selected(request('reason') === $reason)>{{ ucfirst(str_replace('_', ' ', $reason)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Destinasi Wisata -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Destinasi Wisata
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Menyaring laporan berdasarkan destinasi wisata tertentu.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-sans">Penyaringan data laporan agar mempermudah monitoring destinasi spesifik.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <select name="destination_id" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                            <option value="">Semua Destinasi</option>
                            @foreach(($destinations ?? []) as $dest)
                                <option value="{{ $dest->_id }}" @selected(request('destination_id') === (string)$dest->_id)>{{ $dest->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tampilkan -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Tampilkan
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Mengatur jumlah baris data laporan yang ditampilkan dalam satu halaman tabel.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-sans">Navigasi halaman (pagination) tabel laporan pengguna.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <select name="per_page" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-[14px] font-bold text-gray-700 shadow-sm hover:border-[#066466] transition-all cursor-pointer">
                            @foreach([10, 15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected(request('per_page', 15) == $size)>{{ $size }} Baris</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            @if(request('search') || request('status') || request('reason') || request('destination_id') || request('per_page') != 15)
                <div class="flex items-center gap-3 lg:mb-[2px] mt-4 lg:mt-0 flex-shrink-0">
                    <a href="{{ route('admin.reports.index') }}" class="px-5 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                        Reset
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    @php
                        $currentSort = request('sort_by', 'created_at');
                        $sortOrder = request('sort_order', 'desc') === 'asc' ? 'desc' : 'asc';
                    @endphp
                    <tr>
                        <th class="px-10 py-6 text-left">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'user_id', 'sort_order' => ($currentSort === 'user_id' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Pelapor
                                    <svg class="w-4 h-4 {{ $currentSort === 'user_id' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'user_id' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Identitas pengguna (pelapor) yang mengirimkan laporan.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                <p class="text-slate-200 font-sans">Daftar baris laporan masuk di halaman pemantauan.</p>
                                            </div>
                                        </div>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'reason', 'sort_order' => ($currentSort === 'reason' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Target / Alasan
                                    <svg class="w-4 h-4 {{ $currentSort === 'reason' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'reason' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Destinasi wisata yang dilaporkan beserta kategori pelanggaran konten.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                <p class="text-slate-200 font-sans">Daftar laporan untuk mempermudah identifikasi masalah konten.</p>
                                            </div>
                                        </div>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[13px] font-bold text-gray-400 uppercase tracking-wider">Deskripsi</span>
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Rincian atau penjelasan keluhan yang ditulis oleh pelapor.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                <p class="text-slate-200 font-sans">Detail baris laporan untuk memahami masalah secara kronologis.</p>
                                            </div>
                                        </div>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => ($currentSort === 'status' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Status
                                    <svg class="w-4 h-4 {{ $currentSort === 'status' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'status' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Menunjukkan status penyelesaian laporan saat ini.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                <p class="text-slate-200 font-sans">Status tindak lanjut yang dapat diubah di modal detail laporan.</p>
                                            </div>
                                        </div>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($currentSort === 'created_at' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Waktu
                                    <svg class="w-4 h-4 {{ $currentSort === 'created_at' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'created_at' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-sky-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-sans">Durasi waktu berlalu sejak laporan ini pertama kali dibuat.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-sky-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                <p class="text-slate-200 font-sans">Kolom waktu untuk melacak laporan terbaru.</p>
                                            </div>
                                        </div>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse(($reports ?? []) as $report)
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0">
                                        @if($report->image_url)
                                            <img src="{{ $report->image_url }}" class="w-full h-full object-cover" alt="Report">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex flex-col gap-0.5">
                                        <span class="text-sm font-bold text-gray-700 block truncate" title="{{ $report->reporter_name }}">{{ $report->reporter_name }}</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            @php
                                                $isRegistered = $report->user && !empty($report->user->password) && (!empty($report->user->email) || !empty($report->user->name));
                                            @endphp
                                            @if($isRegistered)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#E6F6F2] text-[#00A884] uppercase tracking-wide border border-[#00A884]/10">👤 User</span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-50 text-gray-500 uppercase tracking-wide border border-gray-100">👥 Guest</span>
                                            @endif
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">ID: {{ substr((string)$report->user_id, -6) ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <p class="text-sm font-bold text-gray-800 max-w-[150px] truncate" title="{{ optional($report->destination)->name ?? '' }}">{{ optional($report->destination)->name ?? 'Umum' }}</p>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">{{ str_replace('_', ' ', $report->reason ?? '-') }}</span>
                            </td>
                            <td class="px-10 py-6 max-w-xs">
                                <p class="text-sm text-gray-500 truncate">{{ $report->description ?? '-' }}</p>
                            </td>
                            <td class="px-10 py-6">
                                @php
                                    $s = $report->status ?? 'pending';
                                    $cls = ['pending'=>'bg-amber-50 text-amber-600','reviewed'=>'bg-blue-50 text-blue-600','resolved'=>'bg-[#E6F6F2] text-[#00A884]'];
                                    $lbl = ['pending'=>'Menunggu','reviewed'=>'Ditinjau','resolved'=>'Diselesaikan'];
                                @endphp
                                <span class="{{ $cls[$s] ?? 'bg-gray-100 text-gray-400' }} px-4 py-1.5 rounded-xl font-bold text-xs inline-block">{{ $lbl[$s] ?? ucfirst($s) }}</span>
                            </td>
                            <td class="px-10 py-6">
                                @php
                                    $rawTs = $report->created_at;
                                    if ($rawTs instanceof \MongoDB\BSON\UTCDateTime) {
                                        $ts = \Carbon\Carbon::createFromTimestampMs((int)$rawTs->toDateTime()->format('Uv'));
                                    } elseif (is_numeric($rawTs)) {
                                        // Go stores as milliseconds
                                        $ts = \Carbon\Carbon::createFromTimestampMs((int)$rawTs);
                                    } elseif ($rawTs instanceof \Carbon\Carbon) {
                                        $ts = $rawTs;
                                    } else {
                                        $ts = null;
                                    }
                                @endphp
                                <span class="text-xs text-gray-400 font-medium">{{ $ts ? $ts->diffForHumans() : '-' }}</span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openViewModal('{{ $report->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.reports.destroy', $report->_id) }}', title: 'Hapus Laporan', type: 'laporan', name: {{ json_encode('dari ' . ($report->reporter_name ?? 'Anonim')) }} })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all">
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
        <div class="text-gray-400 text-sm font-medium">Menampilkan {{ $reports->firstItem() }} - {{ $reports->lastItem() }} dari {{ $reports->total() }} data</div>
        <div>{{ $reports->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}</div>
    </div>
    @endif

    {{-- VIEW REPORT MODAL --}}
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showViewModal = false"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-xl bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar flex flex-col">

                <div class="flex items-center justify-between px-10 pt-8 pb-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-900">Detail Laporan</h3>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Detail Laporan</span>
                                            <p class="text-slate-200 font-normal leading-relaxed text-[11px]">Menampilkan rincian laporan keluhan wisatawan mengenai fasilitas or isu tertentu di destinasi. Admin dapat mengunggah bukti foto dan mengubah status tindak lanjut.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 mt-0.5">Rincian pengaduan dan bukti dari pelapor</p>
                    </div>
                    <button @click="showViewModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !viewingReport" class="py-16 flex justify-center px-10">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>

                <div x-show="viewingReport" class="space-y-6 px-10 py-8 flex-1">
                    {{-- Reporter & Status --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-400 font-bold">
                                <span x-text="viewingReport?.reporter_name?.charAt(0)?.toUpperCase() || viewingReport?.user_id?.charAt(0)?.toUpperCase() || 'A'"></span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-gray-800 text-sm" x-text="viewingReport?.reporter_name || viewingReport?.user_id || 'Anonim'"></p>
                                    <template x-if="viewingReport?.user_is_registered">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#E6F6F2] text-[#00A884] uppercase tracking-wide border border-[#00A884]/10">👤 User</span>
                                    </template>
                                    <template x-if="!viewingReport?.user_is_registered">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-50 text-gray-500 uppercase tracking-wide border border-gray-100">👥 Guest</span>
                                    </template>
                                </div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mt-0.5" x-text="viewingReport?.reason?.replace('_', ' ') || '-'"></p>
                            </div>
                        </div>
                        <span :class="statusClass(viewingReport?.status)" class="px-4 py-1.5 rounded-xl font-bold text-xs" x-text="statusLabel(viewingReport?.status)"></span>
                    </div>

                    {{-- Report Image --}}
                    <template x-if="viewingReport?.image_url">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Foto Laporan</label>
                            <div class="rounded-2xl overflow-hidden bg-gray-50 border border-gray-100">
                                <img :src="viewingReport?.image_url" class="w-full h-auto max-h-[300px] object-contain mx-auto" alt="Report Image">
                            </div>
                        </div>
                    </template>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Deskripsi Laporan</label>
                        <p class="text-sm text-gray-700 font-medium leading-relaxed p-4 bg-gray-50 rounded-2xl whitespace-pre-line" x-text="viewingReport?.description || '-'"></p>
                    </div>

                    {{-- Report Images --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Gambar Bukti</label>
                            <span class="text-[10px] text-gray-300 font-medium" x-text="'(' + (viewingReport?.all_image_urls?.length || 0) + ' gambar)'"></span>
                        </div>
                        <template x-if="viewingReport?.all_image_urls && viewingReport.all_image_urls.length > 0">
                            <div class="grid grid-cols-2 gap-3">
                                <template x-for="(img, index) in viewingReport.all_image_urls" :key="index">
                                    <div class="relative group aspect-video rounded-2xl overflow-hidden bg-gray-100 border border-gray-100">
                                        <img :src="img" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                             @click="window.open(img, '_blank')" alt="Laporan">
                                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer"
                                             @click="window.open(img, '_blank')">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!viewingReport?.all_image_urls || viewingReport.all_image_urls.length === 0">
                            <div class="p-8 border-2 border-dashed border-gray-100 rounded-2xl flex flex-col items-center justify-center text-gray-300">
                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-xs font-bold uppercase tracking-widest">Tidak ada gambar</p>
                            </div>
                        </template>
                    </div>

                    {{-- Update Status --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Update Status</label>
                        <div class="flex gap-3">
                            <button @click="updateStatus('pending')" :disabled="savingStatus || viewingReport?.status === 'pending'"
                                :class="viewingReport?.status === 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-500/10' : 'border-amber-200 text-amber-600 hover:bg-amber-50'"
                                class="flex-1 py-3.5 rounded-2xl text-sm font-bold transition-all border disabled:opacity-50 disabled:cursor-not-allowed">
                                Menunggu
                            </button>
                            <button @click="updateStatus('reviewed')" :disabled="savingStatus || viewingReport?.status === 'reviewed'"
                                :class="viewingReport?.status === 'reviewed' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-600/10' : 'border-blue-200 text-blue-600 hover:bg-blue-50'"
                                class="flex-1 py-3.5 rounded-2xl text-sm font-bold transition-all border disabled:opacity-50 disabled:cursor-not-allowed">
                                Ditinjau
                            </button>
                            <button @click="updateStatus('resolved')" :disabled="savingStatus || viewingReport?.status === 'resolved'"
                                :class="viewingReport?.status === 'resolved' ? 'bg-[#00A884] text-white border-[#00A884] shadow-lg shadow-[#00A884]/10' : 'border-[#00A884]/20 text-[#00A884] hover:bg-[#E6F6F2]'"
                                class="flex-1 py-3.5 rounded-2xl text-sm font-bold transition-all border disabled:opacity-50 disabled:cursor-not-allowed">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>

                <div x-show="viewingReport" class="px-10 py-6 bg-gray-50 flex items-center justify-end border-t border-gray-100">
                    <button @click="showViewModal = false" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- EXPORT REPORT MODAL --}}
    <div x-show="showExportModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showExportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showExportModal = false"></div>

            <div x-show="showExportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar flex flex-col">

                <div class="flex items-center justify-between px-10 pt-8 pb-6 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="flex flex-col">
                            <h3 class="text-xl font-bold text-gray-900">Ekspor Laporan</h3>
                            <p class="text-xs text-gray-400 font-medium mt-0.5">Saring data laporan masuk dan pilih format dokumen ekspor</p>
                        </div>
                        <div class="relative group cursor-pointer inline-flex items-center mt-1">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Ekspor Berkas</span>
                                        <p class="text-slate-200 font-normal leading-relaxed text-[11px]">Menyaring laporan pengaduan berdasarkan tanggal, status, dan kategori untuk diekspor ke berkas Excel/CSV atau berkas PDF dinas resmi.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <button @click="showExportModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form x-ref="exportForm" enctype="multipart/form-data" @submit.prevent="submitExport()" class="px-10 pb-8 space-y-6 flex-1">
                    @csrf
                    <input type="hidden" name="format" :value="exportFormat">

                    {{-- Format Selection --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Pilih Format Ekspor</label>
                        <div class="grid grid-cols-3 gap-4">
                            <!-- PDF Kedinasan -->
                            <div @click="exportFormat = 'pdf'" 
                                 :class="exportFormat === 'pdf' ? 'border-[#066466] bg-[#066466]/5 ring-2 ring-[#066466]/20' : 'border-gray-100 hover:border-gray-300 bg-white'"
                                 class="border-2 rounded-2xl p-4 flex flex-col items-center justify-center text-center cursor-pointer transition-all">
                                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500 mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 block">PDF Dinas</span>
                                <span class="text-[10px] text-gray-400 font-medium">Kop Dinas Resmi</span>
                            </div>

                            <!-- Excel -->
                            <div @click="exportFormat = 'excel'" 
                                 :class="exportFormat === 'excel' ? 'border-emerald-600 bg-emerald-50 ring-2 ring-emerald-600/20' : 'border-gray-100 hover:border-gray-300 bg-white'"
                                 class="border-2 rounded-2xl p-4 flex flex-col items-center justify-center text-center cursor-pointer transition-all">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 block">Excel (.xls)</span>
                                <span class="text-[10px] text-gray-400 font-medium">Spreadsheet Data</span>
                            </div>

                            <!-- CSV -->
                            <div @click="exportFormat = 'csv'" 
                                 :class="exportFormat === 'csv' ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-600/20' : 'border-gray-100 hover:border-gray-300 bg-white'"
                                 class="border-2 rounded-2xl p-4 flex flex-col items-center justify-center text-center cursor-pointer transition-all">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 block">CSV Data</span>
                                <span class="text-[10px] text-gray-400 font-medium">Delimiter Titik Koma</span>
                            </div>
                        </div>
                    </div>

                    {{-- Filters Section --}}
                    <div class="space-y-4 pt-4 border-t border-gray-50">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Filter Data Laporan</label>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Tanggal Mulai -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                            <!-- Tanggal Selesai -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Status Laporan -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Status Laporan</label>
                                <select name="status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all cursor-pointer">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Menunggu</option>
                                    <option value="reviewed">Ditinjau</option>
                                    <option value="resolved">Selesai / Diselesaikan</option>
                                </select>
                            </div>
                            <!-- Kategori Laporan -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Kategori / Alasan</label>
                                <select name="reason" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all cursor-pointer">
                                    <option value="">Semua Kategori</option>
                                    @foreach(($reasons ?? []) as $reason)
                                        <option value="{{ $reason }}">{{ ucfirst(str_replace('_', ' ', $reason)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Destinasi Wisata -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Destinasi Wisata</label>
                                <select name="destination_id" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all cursor-pointer">
                                    <option value="">Semua Destinasi</option>
                                    @foreach(($destinations ?? []) as $dest)
                                        <option value="{{ $dest->_id }}">{{ $dest->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Kop Dinas Custom Parameters (Only for PDF) --}}
                    <div x-show="exportFormat === 'pdf'" x-transition class="space-y-4 pt-4 border-t border-gray-50">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Pengaturan Kop & Tanda Tangan Dinas</label>
                            <span class="text-[10px] bg-red-50 text-red-500 font-bold px-2 py-0.5 rounded-lg">Konsep Kedinasan</span>
                        </div>

                        <!-- Custom Logo -->
                        <div class="space-y-1.5 p-4 bg-gray-50 border border-gray-100 rounded-xl">
                            <label class="text-xs font-bold text-gray-700 block">Logo Instansi Kustom (Opsional)</label>
                            <p class="text-xs text-gray-400 mb-2">Upload logo baru jika Anda ingin mengganti logo dari Pengaturan hanya untuk dokumen ini.</p>
                            <input type="file" name="custom_logo" accept="image/png, image/jpeg, image/jpg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#066466]/10 file:text-[#066466] hover:file:bg-[#066466]/20 transition-all cursor-pointer">
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Instansi -->
                            <div class="space-y-1.5 col-span-2">
                                <label class="text-xs font-bold text-gray-500 flex items-center gap-1.5 block">
                                    Nama Instansi / Lembaga
                                    <span class="text-[10px] text-gray-400 font-normal">(pisahkan dengan / untuk baris baru)</span>
                                </label>
                                <input type="text" name="instansi" x-model="instansi" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nomor Surat -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Nomor Surat Dinas</label>
                                <input type="text" name="nomor_surat" x-model="nomor_surat" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                            <!-- Perihal / Hal -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Perihal / Judul Surat</label>
                                <input type="text" name="hal" x-model="hal" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Nama Penandatangan -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Nama Pejabat</label>
                                <input type="text" name="nama_penandatangan" x-model="nama_penandatangan" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                            <!-- NIP -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">NIP Pejabat</label>
                                <input type="text" name="nip_penandatangan" x-model="nip_penandatangan" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                            <!-- Jabatan -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Jabatan Pejabat</label>
                                <input type="text" name="jabatan" x-model="jabatan" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-500 block">Alamat Lengkap Dinas</label>
                            <input type="text" name="alamat" x-model="alamat" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Email -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Email Dinas</label>
                                <input type="text" name="email" x-model="email" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                            <!-- Telp -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">No. Telpon Dinas</label>
                                <input type="text" name="telp" x-model="telp" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                            <!-- Website -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-500 block">Website Dinas</label>
                                <input type="text" name="website" x-model="website" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-[#066466] focus:ring-1 focus:ring-[#066466]/20 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 bg-gray-50 px-10 py-6 -mx-10 -mb-8">
                        <button type="button" @click="showExportModal = false" class="px-8 py-3 text-sm font-bold text-gray-600 border border-gray-200 rounded-2xl hover:bg-gray-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-8 py-3 text-sm font-bold text-white bg-[#066466] rounded-2xl hover:opacity-90 shadow-md shadow-[#066466]/10 transition-all">
                            Proses Ekspor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Custom Confirm Modal -->
    <div x-show="showConfirmModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[210] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center transform transition-all">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed" x-text="confirmMessage"></p>
            <div class="flex gap-3">
                <button @click="showConfirmModal = false" class="flex-1 py-4 bg-gray-50 text-gray-500 rounded-2xl font-bold hover:bg-gray-100 transition-all text-sm">
                    Batal
                </button>
                <button @click="executeConfirm()" class="flex-1 py-4 bg-[#066466] text-white rounded-2xl font-bold hover:opacity-95 transition-all text-sm shadow-lg shadow-[#066466]/20">
                    Ya, Ubah
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <template x-if="showSuccessModal">
        <div class="fixed inset-0 z-[200] flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-[420px] bg-white rounded-[2rem] p-12 text-center animate-in zoom-in duration-300 shadow-2xl">
                <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner ring-4 ring-emerald-50/50">
                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Berhasil!</h3>
                <p class="text-sm text-gray-500 font-medium leading-relaxed" x-text="successMessage"></p>
            </div>
        </div>
    </template>
</div>
@endsection
