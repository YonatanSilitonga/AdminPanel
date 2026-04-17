@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')
@section('page_description', 'Kelola semua pengguna yang terdaftar dalam sistem')

@section('content')

<div x-data="{
    showDetailModal: false,
    loadingDetail: false,
    detailUser: null,
    
    getInitials(name) {
        if (!name) return 'U';
        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    },

    async openUserDetail(userBase) {
        this.showDetailModal = true;
        this.loadingDetail = true;
        
        // Pre-populate with data we already have for instant response
        this.detailUser = {
            user: userBase,
            initials: this.getInitials(userBase.name),
            stats: { reviews: '...', trips: '...', wishlists: '...' },
            activities: []
        };
        
        try {
            const response = await fetch(`/admin/users/${userBase.id}/activity`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const fullData = await response.json();
            
            // Merge full data with base info
            this.detailUser = { ...this.detailUser, ...fullData };
        } catch (error) {
            console.error('Error fetching user details:', error);
            alert('Gagal memuat detail lengkap pengguna.');
        } finally {
            this.loadingDetail = false;
        }
    }
}">

    {{-- /////////////////////////////////// --}}
    {{-- DESKTOP VIEW (ADMIN TABLE LAYOUT)   --}}
    {{-- /////////////////////////////////// --}}
    <div class="hidden md:block">
        {{-- Status Bar / Statistics --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 mb-8">
            <div class="grid grid-cols-4 divide-x divide-gray-100">
                {{-- Total Users --}}
                <div class="flex items-center gap-5 px-6 first:pl-0">
                    <div class="w-1.5 h-12 rounded-full bg-[#006466]"></div>
                    <div>
                        <p class="text-3xl font-extrabold text-[#006466] leading-none mb-1">{{ number_format($stats['total'] ?? 0) }}</p>
                        <p class="text-sm font-bold text-gray-400">Total</p>
                    </div>
                </div>

                {{-- Active Users --}}
                <div class="flex items-center gap-5 px-8">
                    <div class="w-1.5 h-12 rounded-full bg-[#00A884]"></div>
                    <div>
                        <p class="text-3xl font-extrabold text-[#00A884] leading-none mb-1">{{ number_format($stats['active'] ?? 0) }}</p>
                        <p class="text-sm font-bold text-gray-400">Aktif</p>
                    </div>
                </div>

                {{-- Guest Sessions --}}
                <div class="flex items-center gap-5 px-8">
                    <div class="w-1.5 h-12 rounded-full bg-[#FF9F1C]"></div>
                    <div>
                        <p class="text-3xl font-extrabold text-[#FF9F1C] leading-none mb-1">{{ number_format($stats['guests'] ?? 0) }}</p>
                        <p class="text-sm font-bold text-gray-400">Guest Sessions</p>
                    </div>
                </div>

                {{-- Suspended Users --}}
                <div class="flex items-center gap-5 px-8 last:pr-0">
                    <div class="w-1.5 h-12 rounded-full bg-[#EF4444]"></div>
                    <div>
                        <p class="text-3xl font-extrabold text-[#EF4444] leading-none mb-1">{{ number_format($stats['suspended'] ?? 0) }}</p>
                        <p class="text-sm font-bold text-gray-400">Suspended</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filters --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-4">
                <div class="relative w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 placeholder-gray-400 shadow-sm transition-all">
                </div>
                
                <select name="role" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm font-medium text-gray-600 shadow-sm hover:border-sidebar/30 transition-all">
                    <option value="">Semua Role</option>
                    @foreach($roles ?? ['user', 'admin'] as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl outline-none text-sm font-medium text-gray-600 shadow-sm hover:border-sidebar/30 transition-all">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Avatar</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Nama</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Email</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Terdaftar</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Last Active</th>
                            <th class="px-8 py-5 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/30 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="w-10 h-10 bg-sidebar/10 rounded-full flex items-center justify-center border border-sidebar/5 group-hover:bg-sidebar/20 transition-all">
                                        <span class="text-sm font-bold text-sidebar" x-text="getInitials({{ json_encode($user->name) }})"></span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-[15px] font-bold text-gray-800 leading-none group-hover:text-sidebar transition-all">{{ $user->name }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-medium text-gray-500">{{ $user->email }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-medium text-gray-600">{{ $user->created_at->format('d M Y') }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-medium text-gray-400 italic">
                                        {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'N/A' }}
                                    </p>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ ($user->is_active ?? false) ? 'bg-[#E6F6F2] text-[#00A884]' : 'bg-gray-100 text-gray-400' }}">
                                        <div class="w-1.5 h-1.5 {{ ($user->is_active ?? false) ? 'bg-[#00A884]' : 'bg-gray-400' }} rounded-full"></div>
                                        {{ ($user->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end">
                                        <button @click="openUserDetail({ id: '{{ $user->_id }}', name: '{{ $user->name }}', email: '{{ $user->email }}', is_active: {{ $user->is_active ? 'true' : 'false' }} })" 
                                           class="flex items-center justify-center gap-2 px-6 py-2.5 bg-green-500 text-white rounded-full font-bold text-xs hover:bg-green-600 transition-all shadow-lg shadow-green-500/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Detail
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <p class="text-lg font-medium">Tidak ada data pengguna.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(isset($users) && method_exists($users, 'links'))
        <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between">
            <div class="text-sm font-medium text-gray-400 text-[13px]">
                Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} Pengguna
            </div>
            <div>
                {{ $users->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}
            </div>
        </div>
        @endif
    </div>


    {{-- /////////////////////////////////// --}}
    {{-- MOBILE VIEW (FRONTEND APP LAYOUT)   --}}
    {{-- /////////////////////////////////// --}}
    <div class="md:hidden block pb-24 bg-[#F2F3F8] min-h-screen -mx-5 -mt-6 sm:-mx-6 sm:-mt-8 font-sans">
        <!-- Top Header Mobile -->
        <div class="bg-gradient-to-b from-sidebar to-[#055355] rounded-b-[2.5rem] px-6 py-12 text-white shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-10 -mt-10 blur-2xl"></div>
            
            <div class="flex items-center gap-4 mb-6 relative">
                <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Pengguna</h1>
                    <p class="text-xs text-white/70 font-medium">Manajemen Akses & Member</p>
                </div>
            </div>

            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center">
                    <svg class="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Cari nama atau email..." class="w-full bg-white/10 backdrop-blur-md placeholder-white/50 text-white rounded-2xl py-4 pl-12 pr-4 outline-none border border-white/10 focus:bg-white/20 transition-all font-medium">
            </div>
        </div>

        <!-- Scrollable Cards -->
        <div class="px-6 py-8 space-y-4">
            @forelse($users as $user)
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 flex items-center justify-between group transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-sidebar/5 flex items-center justify-center border border-sidebar/5">
                            <span class="font-extrabold text-sidebar" x-text="getInitials({{ json_encode($user->name) }})"></span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 leading-tight mb-1">{{ $user->name }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-sidebar bg-sidebar/5 px-2 py-0.5 rounded-md">{{ $user->role }}</span>
                                <span class="text-[11px] text-gray-400 font-medium">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end gap-3">
                        <div class="w-8 h-4 rounded-full relative transition-colors {{ $user->is_active ? 'bg-[#00A884]' : 'bg-gray-200' }}">
                            <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full transition-all shadow-sm {{ $user->is_active ? 'translate-x-4' : '' }}"></div>
                        </div>
                        <button @click="openUserDetail({ id: '{{ $user->_id }}', name: '{{ $user->name }}', email: '{{ $user->email }}', is_active: {{ $user->is_active ? 'true' : 'false' }} })" class="text-xs font-extrabold text-red-500 uppercase tracking-widest">Detail</button>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 py-10">Belum ada pengguna terdaftar.</div>
            @endforelse
        </div>
    </div>


    {{-- Detail User Modal --}}
    <div x-show="showDetailModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         x-cloak
         role="dialog"
         aria-modal="true">
        
        {{-- Backdrop --}}
        <div x-show="showDetailModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/40 backdrop-blur-[2px] transition-opacity"
             @click="showDetailModal = false"></div>

        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showDetailModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col">
                
                {{-- Modal Header --}}
                <div class="px-8 py-6 flex items-center justify-between border-b border-gray-50 bg-white/50 backdrop-blur-md sticky top-0 z-10">
                    <h3 class="text-xl font-bold text-gray-800">Detail Pengguna</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-10 no-scrollbar overflow-y-auto max-h-[75vh]">
                    {{-- Loading Background State (Small Spinner instead of full screen) --}}
                    <div x-show="loadingDetail && !detailUser" class="py-20 flex flex-col items-center justify-center">
                        <div class="w-12 h-12 border-4 border-sidebar/10 border-t-sidebar rounded-full animate-spin mb-4"></div>
                        <p class="text-sm font-bold text-gray-400">Memuat data...</p>
                    </div>

                    {{-- Data Content --}}
                    <div x-show="detailUser" class="space-y-12">
                        {{-- Profile Info --}}
                        <div class="flex flex-col items-center text-center">
                            <div class="relative mb-6">
                                <div class="w-32 h-32 bg-sidebar/5 rounded-full flex items-center justify-center border-4 border-white shadow-xl ring-1 ring-gray-100 overflow-hidden">
                                    <span class="text-4xl font-black text-sidebar/80 tracking-tighter" x-text="detailUser ? detailUser.initials : ''"></span>
                                </div>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900 mb-1" x-text="detailUser ? detailUser.user.name : ''"></h3>
                            <p class="text-[15px] font-medium text-gray-400 mb-5" x-text="detailUser ? detailUser.user.email : ''"></p>
                            
                            <template x-if="detailUser && detailUser.user.is_active">
                                <span class="px-5 py-1.5 bg-[#E6F6F2] text-[#00A884] text-xs font-bold uppercase tracking-widest rounded-full">Aktif</span>
                            </template>
                            <template x-if="detailUser && !detailUser.user.is_active">
                                <span class="px-5 py-1.5 bg-gray-100 text-gray-400 text-xs font-bold uppercase tracking-widest rounded-full">Nonaktif</span>
                            </template>
                        </div>

                        {{-- Stats Cards --}}
                        <div class="grid grid-cols-3 gap-6">
                            <div class="bg-gray-50/70 rounded-[2rem] p-7 text-center border border-gray-100/50 hover:bg-white hover:shadow-lg hover:shadow-gray-100 transition-all duration-300">
                                <p class="text-3xl font-black text-gray-900 mb-1" x-text="detailUser ? detailUser.stats.reviews : 0"></p>
                                <p class="text-[13px] font-bold text-gray-400">Review</p>
                            </div>
                            <div class="bg-gray-50/70 rounded-[2rem] p-7 text-center border border-gray-100/50 hover:bg-white hover:shadow-lg hover:shadow-gray-100 transition-all duration-300">
                                <p class="text-3xl font-black text-gray-900 mb-1" x-text="detailUser ? detailUser.stats.trips : 0"></p>
                                <p class="text-[13px] font-bold text-gray-400">Trip</p>
                            </div>
                            <div class="bg-gray-50/70 rounded-[2rem] p-7 text-center border border-gray-100/50 hover:bg-white hover:shadow-lg hover:shadow-gray-100 transition-all duration-300">
                                <p class="text-3xl font-black text-gray-900 mb-1" x-text="detailUser ? detailUser.stats.wishlists : 0"></p>
                                <p class="text-[13px] font-bold text-gray-400">Wishlist</p>
                            </div>
                        </div>

                        {{-- Activity Log --}}
                        <div class="space-y-8">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-5 h-5 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <h4 class="text-lg font-bold text-gray-800">Riwayat Aktivitas</h4>
                            </div>

                            <div class="relative space-y-8 pl-4">
                                {{-- Timeline Line --}}
                                <div class="absolute left-7 top-4 bottom-4 w-0.5 bg-gray-100"></div>

                                <template x-for="activity in (detailUser ? detailUser.activities : [])" :key="activity.title">
                                    <div class="relative flex items-center gap-6 group">
                                        {{-- Icon Circle --}}
                                        <div class="relative z-10 w-11 h-11 rounded-2xl flex items-center justify-center bg-[#E6F6F2] text-[#00A884] border-4 border-white shadow-sm transition-transform group-hover:scale-110">
                                            {{-- Dynamic Icons based on type --}}
                                            <template x-if="activity.icon === 'map'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                            </template>
                                            <template x-if="activity.icon === 'chat'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                            </template>
                                            <template x-if="activity.icon === 'heart'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            </template>
                                            <template x-if="activity.icon === 'search'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            </template>
                                        </div>
                                        <div>
                                            <p class="text-[15px] font-bold text-gray-800 leading-tight mb-1" x-text="activity.title"></p>
                                            <p class="text-xs font-medium text-gray-400" x-text="activity.time"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Footer Actions --}}
                <div class="px-10 py-8 bg-gray-50/80 border-t border-gray-100 flex justify-end">
                    <form :action="detailUser ? `/admin/users/${detailUser.user._id}` : '#'" method="POST" @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus akun ini?')) $el.submit()">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex items-center gap-3 px-8 py-3.5 bg-red-600 text-white rounded-2xl font-bold text-[15px] hover:bg-red-700 transition-all shadow-xl shadow-red-600/20 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Hide scrollbar for Chrome, Safari and Opera */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
@endsection
