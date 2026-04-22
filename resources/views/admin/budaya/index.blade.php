@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Content Management</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Budaya dan Warisan</span>
</nav>
@endsection

@section('content')

<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    editingBudaya: null,
    loading: false,
    createFileName: '',
    editFileName: '',

    async openEditModal(id) {
        this.loading = true;
        this.showEditModal = true;
        this.editingBudaya = null;
        try {
            const res = await fetch(`/admin/budaya/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            this.editingBudaya = await res.json();
            this.editFileName = this.editingBudaya.image_url ? 'Foto saat ini' : '';
        } catch(e) {
            alert('Gagal mengambil data budaya');
            this.showEditModal = false;
        } finally {
            this.loading = false;
        }
    },

    async submitEdit() {
        this.loading = true;
        const form = document.getElementById('editBudayaForm');
        const formData = new FormData(form);
        try {
            const res = await fetch(`/admin/budaya/${this.editingBudaya._id}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: formData
            });
            const result = await res.json();
            if (result.success) { window.location.reload(); }
            else { alert(result.message || 'Gagal menyimpan'); }
        } catch(e) {
            alert('Terjadi kesalahan saat menyimpan');
        } finally { this.loading = false; }
    }
}">

    {{-- /////////////////////////////////// --}}
    {{-- DESKTOP VIEW (ADMIN TABLE LAYOUT)   --}}
    {{-- /////////////////////////////////// --}}
    <div class="hidden md:block">
        {{-- Tabs/Pills --}}
        <div class="flex items-center bg-white border border-gray-200 rounded-2xl p-1 mb-6 inline-flex shadow-sm">
            <a href="{{ route('admin.budaya.index') }}" class="px-6 py-2 {{ request('category') == '' ? 'bg-[#066466] text-white' : 'text-gray-500 hover:text-gray-800' }} rounded-xl text-sm font-bold transition-colors">Semua</a>
            @foreach(['Sejarah', 'Tradisi', 'Kuliner', 'Cerita Rakyat', 'Rumah Adat'] as $cat)
                <a href="{{ route('admin.budaya.index', ['category' => $cat]) }}" class="px-6 py-2 {{ request('category') == $cat ? 'bg-[#066466] text-white' : 'text-gray-500 hover:text-gray-800' }} rounded-xl text-sm font-medium transition-colors">{{ $cat }}</a>
            @endforeach
        </div>

        {{-- Search & Filters --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <form method="GET" action="{{ route('admin.budaya.index') }}" class="flex flex-wrap items-center gap-4">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <div class="relative w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul/kategori budaya"
                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 placeholder-gray-400">
                </div>
                
                <select name="status" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm font-medium text-gray-600">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <!-- Add dates if required -->
            </form>

            <button @click="showCreateModal = true" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Konten Budaya
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16">#</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Thumbnail</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Topik</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-8 py-5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-8 py-5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($budayas as $index => $item)
                            <tr class="hover:bg-gray-50/30 transition-colors group">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-semibold text-gray-400">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    @if(isset($item->image_url))
                                        <img src="{{ Str::startsWith($item->image_url, 'http') ? $item->image_url : asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="w-20 h-14 object-cover rounded-xl shadow-sm border border-gray-100 group-hover:scale-105 transition-transform">
                                    @else
                                        <div class="w-20 h-14 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-[14px] font-bold text-gray-800">{{ $item->name }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 text-xs font-bold text-sidebar bg-sidebar/10 rounded-lg whitespace-nowrap">
                                        {{ $item->category }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <form action="{{ route('admin.budaya.toggle-status', $item->_id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold {{ ($item->is_active ?? false) ? 'bg-[#E6F6F2] text-[#00A884]' : 'bg-gray-100 text-gray-400' }}">
                                            @if($item->is_active)
                                                <div class="w-1.5 h-1.5 bg-[#00A884] rounded-full"></div>
                                                Aktif
                                            @else
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full"></div>
                                                Nonaktif
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openEditModal('{{ $item->_id }}')" class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-600 rounded-lg font-bold text-xs hover:bg-green-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.budaya.destroy', $item->_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus budaya ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-14 text-center text-gray-400">
                                    Tidak ada data budaya ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(isset($budayas) && method_exists($budayas, 'links'))
        <div class="px-2 py-4 flex items-center justify-between">
            <div class="text-sm font-medium text-gray-400">
                Menampilkan {{ $budayas->firstItem() ?? 0 }}-{{ $budayas->lastItem() ?? 0 }} dari {{ $budayas->total() }} konten budaya
            </div>
            <div>
                {{ $budayas->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}
            </div>
        </div>
        @endif
    </div>


    {{-- /////////////////////////////////// --}}
    {{-- MOBILE VIEW (FRONTEND APP LAYOUT)   --}}
    {{-- /////////////////////////////////// --}}
    <div class="md:hidden block pb-24 bg-[#F2F3F8] min-h-screen -mx-5 -mt-6 sm:-mx-6 sm:-mt-8 font-sans relative z-0">
        <!-- Top Nav Mobile -->
        <div class="bg-gradient-to-b from-[#8C75B5] to-[#7B61A5] rounded-b-[2rem] px-6 py-6 pt-12 text-white shadow-lg relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <div class="flex-1">
                    <h1 class="text-[22px] font-bold leading-tight">Sejarah & Budaya</h1>
                    <p class="text-sm text-white/80 font-medium">Cari sejarah & budaya</p>
                </div>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pt-0.5">
                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Cari sejarah & budaya Batak..." class="w-full bg-white/20 placeholder-white/80 text-white rounded-[1.25rem] py-3.5 pl-12 pr-4 outline-none font-medium shadow-inner focus:bg-white/30 transition border border-white/10">
            </div>
        </div>
        
        <!-- Chips -->
        <div class="overflow-x-auto flex space-x-3 px-6 py-6 no-scrollbar">
            <span class="bg-[#7861A5] text-white px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-md">Semua</span>
            <span class="bg-white text-gray-500 px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-sm border border-gray-100">Desa</span>
            <span class="bg-white text-gray-500 px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-sm border border-gray-100">Legenda</span>
            <span class="bg-white text-gray-500 px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-sm border border-gray-100">Kuliner</span>
        </div>

        <!-- Cards Layout -->
        <div class="px-6 space-y-6">
           @forelse($budayas->where('is_active', true) as $item)
               <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100/50 relative z-0">
                   <div class="relative">
                       @if(isset($item->image_url))
                           <img src="{{ Str::startsWith($item->image_url, 'http') ? $item->image_url : asset('storage/' . $item->image_url) }}" class="h-48 w-full object-cover">
                       @else
                           <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-400">Tidak ada gambar</div>
                       @endif
                   </div>
                   
                   <div class="p-5">
                       <span class="inline-block text-[10px] font-extrabold tracking-wider text-[#7861A5] bg-purple-50 px-2.5 py-1.5 rounded-lg uppercase mb-2.5">
                           {{ $item->category_mobile ?? $item->category }}
                       </span>
                       <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $item->name }}</h3>
                       <p class="text-[13px] text-gray-500 mt-2 font-medium leading-relaxed line-clamp-2">{{ $item->description }}</p>
                       
                       <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-50">
                           <span class="text-[13px] font-semibold text-gray-400 flex items-center gap-1.5">
                               <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> 
                               {{ $item->location }}
                           </span>
                           <span class="text-sm font-bold text-[#7861A5]">Lihat Detail</span>
                       </div>
                   </div>
               </div>
           @empty
               <div class="text-center text-gray-400 py-10">Belum ada budaya yg aktif.</div>
           @endforelse
           
           <!-- Did You Know Banner -->
           <div class="bg-white rounded-3xl p-5 border border-[#7861A5]/20 shadow-sm flex items-start gap-4 mt-8 relative overflow-hidden">
               <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 rounded-full blur-2xl -mr-10 -mt-10 opacity-60 pointer-events-none"></div>
               <div class="w-12 h-12 rounded-2xl bg-[#7861A5] text-white flex items-center justify-center flex-shrink-0 shadow-md">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               </div>
               <div>
                   <h4 class="font-bold text-[#7861A5] text-md">Tahukah Kamu?</h4>
                   <p class="text-xs font-medium text-gray-500 mt-1 leading-relaxed">Danau Toba adalah danau vulkanik terbesar di dunia yang terbentuk dari letusan supervolcano sekitar 74.000 tahun yang lalu.</p>
               </div>
           </div>
        </div>
        
        <!-- Floating Action Button -->
        <button class="fixed bottom-24 right-6 w-14 h-14 bg-[#3EACA8] text-white rounded-full flex items-center justify-center shadow-lg shadow-[#3EACA8]/30 z-20 hover:scale-105 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
        </button>
    </div>

    {{-- Mobile Bottom Nav (Mockup as seen in image) --}}
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 flex items-center justify-between px-6 py-2.5 pb-safe z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.03)]">
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-[#066466] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-bold text-[#066466]">Beranda</span>
        </div>
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#066466] group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#066466] transition-colors">Jelajahi</span>
        </div>
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#066466] group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#066466] transition-colors">Peta</span>
        </div>
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#066466] group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#066466] transition-colors">Acara</span>
        </div>
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#066466] group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#066466] transition-colors">Profil</span>
        </div>
    </div>


    {{-- ========================================= --}}
    {{-- MODAL TAMBAH BUDAYA                       --}}
    {{-- ========================================= --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500/50 backdrop-blur-sm" @click="showCreateModal = false"></div>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-[101] max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Konten Budaya</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('admin.budaya.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama / Judul Budaya</label>
                            <input type="text" name="name" required placeholder="Cth: Makam Raja Sidabutar" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="col-span-2  space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori Utama</label>
                            <select name="category" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                @foreach($categories ?? ['Sejarah', 'Tradisi', 'Rumah Adat', 'Cerita Rakyat', 'Kuliner'] as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori Mobile <span class="text-[10px] lowercase font-normal">(Opsional)</span></label>
                            <input type="text" name="category_mobile" placeholder="Cth: SEJARAH BATAK" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                        </div> -->
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Singkat</label>
                            <input type="text" name="location" required placeholder="Cth: Pulau Samosir" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                            <textarea name="description" rows="3" required placeholder="Penjelasan mengenai budaya ini..." class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 placeholder-gray-300"></textarea>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Foto Utama (Thumbnail)</label>
                            <div class="relative group">
                                <input type="file" name="thumbnail" id="create_thumbnail" required class="hidden" @change="createFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                <label for="create_thumbnail" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/10">
                                    <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Klik untuk upload foto'"></p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tight">PNG, JPG, WEBP (Maks. 5MB)</p>
                                </label>
                            </div>
                        </div>
                        <div class="col-span-2 mt-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 rounded-md border-gray-300 text-sidebar focus:ring-sidebar/30">
                                <span class="text-sm font-semibold text-gray-700">Tampilkan ke Publik (Aktif)</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" @click="showCreateModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-200 rounded-xl hover:text-gray-600 transition-colors">Batal</button>
                        <button type="submit" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all">Simpan Budaya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT BUDAYA                         --}}
    {{-- ========================================= --}}
    <div x-show="showEditModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500/50 backdrop-blur-sm" @click="showEditModal = false"></div>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-[101] max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Edit Konten Budaya</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !editingBudaya" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <div x-show="editingBudaya">
                    <form id="editBudayaForm" @submit.prevent="submitEdit()" class="space-y-5">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama / Judul Budaya</label>
                                <input type="text" name="name" x-model="editingBudaya.name" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori Utama</label>
                                <select name="category" x-model="editingBudaya.category" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    @foreach($categories ?? ['Sejarah', 'Tradisi', 'Rumah Adat', 'Cerita Rakyat', 'Kuliner'] as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori Mobile</label>
                                <input type="text" name="category_mobile" x-model="editingBudaya.category_mobile" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                            </div> -->
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Singkat</label>
                                <input type="text" name="location" x-model="editingBudaya.location" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                                <textarea name="description" rows="3" x-model="editingBudaya.description" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700"></textarea>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Ganti Foto Utama (Opsional)</label>
                                <div x-show="editingBudaya && editingBudaya.image_url" class="mb-3">
                                    <img :src="editingBudaya.image_url.startsWith('http') ? editingBudaya.image_url : `/storage/${editingBudaya.image_url}`" class="w-full h-40 object-cover rounded-2xl shadow-sm border border-gray-100">
                                </div>
                                <div class="relative group">
                                    <input type="file" name="thumbnail" id="edit_thumbnail" class="hidden" @change="editFileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                    <label for="edit_thumbnail" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/10">
                                        <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700" x-text="editFileName || 'Klik untuk ganti foto'"></p>
                                    </label>
                                </div>
                            </div>
                            <div class="col-span-2 mt-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" x-model="editingBudaya.is_active" class="w-5 h-5 rounded-md border-gray-300 text-sidebar focus:ring-sidebar/30">
                                    <span class="text-sm font-semibold text-gray-700">Tampilkan ke Publik (Aktif)</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                            <button type="button" @click="showEditModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 border border-gray-200 rounded-xl bg-gray-50 hover:text-gray-600 transition-colors">Batal</button>
                            <button type="submit" :disabled="loading" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom);
    }
</style>
@endsection
