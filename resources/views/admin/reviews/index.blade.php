@extends('admin.layouts.app')

@section('title', 'Ulasan Pengguna')
@section('page_title', 'Ulasan')
@section('page_description', 'Moderasi ulasan pengguna')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="#" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Ulasan</span>
</nav>
@endsection

@section('content')
<div x-data="{
    showViewModal: false,
    viewingReview: null,
    loading: false,

    async openViewModal(id) {
        this.loading = true;
        this.showViewModal = true;
        this.viewingReview = null;
        try {
            const res = await fetch(`/admin/reviews/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            this.viewingReview = await res.json();
        } catch(e) {
            alert('Gagal mengambil data ulasan');
            this.showViewModal = false;
        } finally {
            this.loading = false;
        }
    },

    stars(n) {
        return '★'.repeat(n) + '☆'.repeat(5 - n);
    }
}">
    {{-- Filter & Search --}}
    <div class="flex flex-wrap items-center gap-4 mb-8">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="relative w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari teks ulasan..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm shadow-sm placeholder-gray-300">
            </div>
            <select name="rating" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm shadow-sm text-gray-600 font-medium">
                <option value="">Semua Rating</option>
                @foreach([5,4,3,2,1] as $r)
                    <option value="{{ $r }}" @selected(request('rating') == $r)>{{ $r }} Bintang</option>
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
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Destinasi</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Ulasan</th>
                        <th class="px-10 py-6 text-left text-[13px] font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-10 py-6 text-right text-[13px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse(($reviews ?? []) as $review)
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-sidebar/10 rounded-full flex items-center justify-center text-sidebar text-xs font-bold">
                                        {{ strtoupper(substr($review->user_id ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ $review->user_id ?? 'Anonim' }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="text-sm text-gray-500 font-medium">{{ optional($review->destination)->name ?? 'Umum' }}</span>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-1">
                                    @for($i = 0; $i < ($review->rating ?? 0); $i++)
                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @endfor
                                    @for($i = ($review->rating ?? 0); $i < 5; $i++)
                                        <svg class="w-4 h-4 text-gray-200 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-10 py-6 max-w-xs">
                                <p class="text-sm text-gray-500 truncate">{{ $review->review ?? '-' }}</p>
                            </td>
                            <td class="px-10 py-6">
                                <span class="text-xs text-gray-400 font-medium">{{ $review->created_at?->diffForHumans() ?? '-' }}</span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openViewModal('{{ $review->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.reviews.destroy', $review->_id) }}', title: 'Hapus Ulasan', type: 'ulasan', name: {{ json_encode('dari ' . ($review->user_id ?? 'Anonim')) }} })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-14 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                    <p class="text-sm font-medium">Tidak ada ulasan ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($reviews) && method_exists($reviews, 'links'))
    <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between">
        <div class="text-gray-400 text-sm font-medium">Menampilkan {{ $reviews->count() }} dari {{ $reviews->total() }} Ulasan</div>
        <div>{{ $reviews->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}</div>
    </div>
    @endif

    {{-- VIEW REVIEW MODAL --}}
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500/20 backdrop-blur-sm" @click="showViewModal = false"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10">

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Detail Ulasan</h3>
                    <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !viewingReview" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>

                <div x-show="viewingReview" class="space-y-5">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                        <div class="w-12 h-12 bg-sidebar/10 rounded-full flex items-center justify-center text-sidebar font-bold text-lg">
                            <span x-text="viewingReview ? viewingReview.user_id?.charAt(0)?.toUpperCase() || 'A' : 'A'"></span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800" x-text="viewingReview?.user_id || 'Anonim'"></p>
                            <p class="text-xs text-gray-400" x-text="viewingReview?.created_at ? new Date(viewingReview.created_at).toLocaleDateString('id-ID', {year:'numeric', month:'long', day:'numeric'}) : ''"></p>
                        </div>
                        <div class="ml-auto text-2xl text-yellow-400" x-text="stars(viewingReview?.rating || 0)"></div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Ulasan</label>
                        <p class="text-sm text-gray-700 font-medium leading-relaxed p-4 bg-gray-50 rounded-2xl" x-text="viewingReview?.review || '-'"></p>
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
