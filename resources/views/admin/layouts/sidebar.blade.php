<!-- resources/views/admin/layouts/sidebar.blade.php -->
@php
    $admin = auth('admin')->user();
    $name = $admin?->name ?? 'Admin User';
    $initials = collect(explode(' ', $name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('');
    $email = $admin?->email ?? 'admin@toba.id';
    $roleName = optional($admin?->role)->name ?? 'Super Admin';
@endphp

<div 
    x-data="{ 
        openMenus: {
            destinasi: {{ request()->routeIs('admin.destinations.*') ? 'true' : 'false' }},
            smartFeatures: {{ request()->routeIs('admin.chatbot-logs.*') || request()->routeIs('admin.recommendations.*') ? 'true' : 'false' }},
            ulasan: {{ request()->routeIs('admin.reviews.*') || request()->routeIs('admin.reports.*') ? 'true' : 'false' }}
        } 
    }" 
    class="fixed inset-y-0 left-0 lg:relative bg-sidebar text-white h-screen flex flex-col shadow-2xl lg:shadow-xl overflow-hidden flex-shrink-0 z-50 transition-all duration-300 ease-in-out"
    :class="sidebarOpen ? 'w-72 translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-16'"
>
    <!-- Logo Section -->
    <div class="py-5 flex items-center border-b border-white/10 transition-all duration-300"
         :class="sidebarOpen ? 'px-5 space-x-3' : 'px-3 justify-center'">
        <div class="w-10 h-10 bg-toba-gold rounded-lg flex items-center justify-center shadow-lg flex-shrink-0 transition-transform duration-200 hover:scale-110">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21l7-14 4 8 3-4 4 10H3z"></path>
            </svg>
        </div>
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200 delay-100" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="overflow-hidden whitespace-nowrap">
            <h2 class="text-lg font-bold tracking-wider leading-tight">TOBA TOURISM</h2>
            <p class="text-xs text-gray-300 opacity-80">Kawasan Danau Toba</p>
        </div>
    </div>

    <!-- User Profile Section -->
    <div class="py-4 flex items-center border-b border-white/10 mb-2 transition-all duration-300"
         :class="sidebarOpen ? 'px-5 space-x-3' : 'px-3 justify-center'">
        <div class="relative group/avatar flex-shrink-0">
            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center border border-white/20 text-sm font-bold transition-transform duration-200 group-hover/avatar:scale-110 group-hover/avatar:border-toba-gold cursor-default">
                {{ $initials }}
            </div>
            <!-- Tooltip for collapsed avatar -->
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 top-1/2 -translate-y-1/2 z-[60] pointer-events-none
                        opacity-0 group-hover/avatar:opacity-100 translate-x-1 group-hover/avatar:translate-x-0
                        transition-all duration-200 whitespace-nowrap">
                <div class="bg-gray-900 text-white text-xs rounded-lg px-3 py-2 shadow-xl">
                    <p class="font-semibold">{{ $name }}</p>
                    <p class="text-gray-400 text-[10px]">{{ $email }}</p>
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-gray-900"></div>
                </div>
            </div>
        </div>
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200 delay-100" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="overflow-hidden">
            <p class="text-sm font-semibold truncate">{{ $name }}</p>
            <p class="text-xs text-gray-400 truncate">{{ $email }}</p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-4 space-y-1 custom-scrollbar transition-all duration-300"
         :class="sidebarOpen ? 'px-4' : 'px-2'">

        {{-- ── Helper macro: nav-item with tooltip ── --}}
        {{-- Each item uses group/item for hover scope --}}

        <!-- Dashboard -->
        <div class="relative group/item">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-transform duration-200 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Dashboard</span>
            </a>
            <!-- Tooltip (collapsed only) -->
            <div x-show="!sidebarOpen" class="sidebar-tooltip">
                <span>Dashboard</span>
                <div class="sidebar-tooltip-arrow"></div>
            </div>
        </div>

        <!-- Content Management Section -->
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="px-4 py-3 mt-4 mb-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Content Management</p>
        </div>
        <div x-show="!sidebarOpen" class="my-2 border-t border-white/10"></div>

        <!-- Destinasi (Dropdown) -->
        <div x-data="{ open: openMenus.destinasi }" class="relative group/item">
            <!-- Collapsed: icon-only link to first sub-page -->
            <a x-show="!sidebarOpen" href="{{ route('admin.destinations.index') }}"
               class="flex justify-center items-center py-2 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.destinations.*') || request()->routeIs('admin.trending.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-6 h-6 transition-transform duration-200 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </a>
            <!-- Expanded: dropdown button -->
            <button x-show="sidebarOpen" @click="open = !open"
               class="w-full flex items-center px-4 py-2 rounded-xl transition-all duration-200 text-gray-300 hover:bg-white/10 hover:text-white">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="font-medium flex-1 text-left">Destinasi</span>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="sidebarOpen && open" x-transition class="ml-10 mt-1 space-y-1">
                <a href="{{ route('admin.destinations.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.destinations.index') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white' }} transition-colors">Kelola Destinasi</a>
                <a href="{{ route('admin.trending.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.trending.index') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white' }} transition-colors">Trending Destinasi</a>
            </div>
            <!-- Tooltip (collapsed) -->
            <div x-show="!sidebarOpen" class="sidebar-tooltip">
                <span>Destinasi</span>
                <div class="sidebar-tooltip-arrow"></div>
            </div>
        </div>

        <!-- Kelola Event -->
        <div class="relative group/item">
            <a href="{{ route('admin.events.index') }}"
               class="flex items-center rounded-xl transition-all duration-200 {{ request()->routeIs('admin.events.*') ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-transform duration-200 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Kelola Event</span>
            </a>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Kelola Event</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>

        <!-- Carousel & Banner -->
        <div class="relative group/item">
            <a href="{{ route('admin.carousel_banners.index') }}"
               class="flex items-center rounded-xl transition-all duration-200 {{ request()->routeIs('admin.carousel_banners.*') ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-transform duration-200 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Carousel dan Banner</span>
            </a>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Carousel dan Banner</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>

        <!-- Fasilitas Umum -->
        <div class="relative group/item">
            <a href="{{ route('admin.fasilitas_umum.index') }}"
               class="flex items-center rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fasilitas_umum.*') ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-transform duration-200 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Fasilitas Umum</span>
            </a>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Fasilitas Umum</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>

        <!-- Berita & Promosi -->
        <div class="relative group/item">
            <a href="{{ route('admin.berita_promosi.index') }}"
               class="flex items-center rounded-xl transition-all duration-200 {{ request()->routeIs('admin.berita_promosi.*') ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-transform duration-200 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v4a2 2 0 002 2h4"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h3m-3 4h5m-5 4h5"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Berita &amp; Promosi</span>
            </a>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Berita &amp; Promosi</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>

        <!-- Budaya dan Warisan -->
        <div class="relative group/item">
            <a href="{{ route('admin.budaya.index') }}"
               class="flex items-center rounded-xl transition-all duration-200 {{ request()->routeIs('admin.budaya.*') ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-transform duration-200 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Budaya dan Warisan</span>
            </a>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Budaya dan Warisan</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>

        <!-- Administration Section -->
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="px-4 py-3 mt-4 mb-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</p>
        </div>
        <div x-show="!sidebarOpen" class="my-2 border-t border-white/10"></div>

        <!-- Manajemen Pengguna -->
        <div class="relative group/item">
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-transform duration-200 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Manajemen Pengguna</span>
            </a>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Manajemen Pengguna</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>

        <!-- Monitoring Section -->
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="px-4 py-3 mt-4 mb-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Monitoring</p>
        </div>
        <div x-show="!sidebarOpen" class="my-2 border-t border-white/10"></div>

        <!-- Fitur AI dan Cerdas (Dropdown) -->
        <div x-data="{ open: openMenus.smartFeatures }" class="relative group/item">
            <a x-show="!sidebarOpen" href="{{ route('admin.chatbot-logs.index') }}"
               class="flex justify-center items-center py-2 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.chatbot-logs.*') || request()->routeIs('admin.recommendations.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-6 h-6 transition-transform duration-200 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </a>
            <button x-show="sidebarOpen" @click="open = !open"
               class="w-full flex items-center px-4 py-2 rounded-xl transition-all duration-200 text-gray-300 hover:bg-white/10 hover:text-white">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span class="font-medium flex-1 text-left">Fitur AI dan Cerdas</span>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="sidebarOpen && open" x-transition class="ml-10 mt-1 space-y-1">
                <a href="{{ route('admin.chatbot-logs.index') }}" class="block px-4 py-2 text-sm rounded-lg text-gray-400 hover:text-white transition-colors">Log Chatbot</a>
                <a href="{{ route('admin.recommendations.index') }}" class="block px-4 py-2 text-sm rounded-lg text-gray-400 hover:text-white transition-colors">Log Rekomendasi</a>
            </div>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Fitur AI dan Cerdas</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>

        <!-- Ulasan & Laporan (Dropdown) -->
        <div x-data="{ open: openMenus.ulasan }" class="relative group/item">
            <a x-show="!sidebarOpen" href="{{ route('admin.reviews.index') }}"
               class="flex justify-center items-center py-2 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reviews.*') || request()->routeIs('admin.reports.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-6 h-6 transition-transform duration-200 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </a>
            <button x-show="sidebarOpen" @click="open = !open"
               class="w-full flex items-center px-4 py-2 rounded-xl transition-all duration-200 text-gray-300 hover:bg-white/10 hover:text-white">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <span class="font-medium flex-1 text-left">Ulasan &amp; Laporan</span>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="sidebarOpen && open" x-transition class="ml-10 mt-1 space-y-1">
                <a href="{{ route('admin.reviews.index') }}" class="block px-4 py-2 text-sm rounded-lg text-gray-400 hover:text-white transition-colors">Ringkasan Ulasan</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-2 text-sm rounded-lg text-gray-400 hover:text-white transition-colors">Laporan Masuk</a>
            </div>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Ulasan &amp; Laporan</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>

        <!-- Settings Section -->
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="px-4 py-3 mt-4 mb-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</p>
        </div>
        <div x-show="!sidebarOpen" class="my-2 border-t border-white/10"></div>

        <!-- Pengaturan Sistem -->
        <div class="relative group/item">
            <a href="{{ route('admin.settings.general') }}"
               class="flex items-center rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-transform duration-200 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Pengaturan Sistem</span>
            </a>
            <div x-show="!sidebarOpen" class="sidebar-tooltip"><span>Pengaturan Sistem</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>
    </nav>

    <!-- Logout Section -->
    <div class="mt-auto border-t border-white/10 transition-all duration-300"
         :class="sidebarOpen ? 'px-5 py-6' : 'px-2 py-4'">
        <div class="relative group/item">
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-logout-modal'))"
                    class="w-full flex items-center rounded-xl transition-all duration-200 text-gray-300 hover:bg-red-500/10 hover:text-red-400 group"
                    :class="sidebarOpen ? 'px-4 py-2' : 'px-0 py-2 justify-center'">
                <svg class="flex-shrink-0 transition-all duration-200 group-hover:-translate-x-0.5 group-hover/item:scale-110"
                     :class="sidebarOpen ? 'w-5 h-5 mr-3' : 'w-6 h-6'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="font-medium whitespace-nowrap">Logout</span>
            </button>
            <div x-show="!sidebarOpen" class="sidebar-tooltip sidebar-tooltip-up"><span>Logout</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

    /* ── Sidebar tooltip (shown when collapsed) ── */
    .sidebar-tooltip {
        position: absolute;
        left: calc(100% + 12px);
        top: 50%;
        transform: translateY(-50%) translateX(4px);
        z-index: 9999;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.18s ease, transform 0.18s ease;
        white-space: nowrap;
    }
    .sidebar-tooltip span {
        display: block;
        background: #111827;
        color: #f9fafb;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.35);
    }
    .sidebar-tooltip-arrow {
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        border: 5px solid transparent;
        border-right-color: #111827;
    }
    /* Tooltip pointing upward for bottom items */
    .sidebar-tooltip-up {
        top: auto;
        bottom: 50%;
        transform: translateY(50%) translateX(4px);
    }
    .sidebar-tooltip-up .sidebar-tooltip-arrow {
        top: 50%;
    }

    /* Show tooltip on hover of the parent group */
    .group\/item:hover .sidebar-tooltip {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
    }
    .group\/item:hover .sidebar-tooltip-up {
        opacity: 1;
        transform: translateY(50%) translateX(0);
    }

    /* Icon glow pulse on hover when collapsed */
    .group\/item:hover svg {
        filter: drop-shadow(0 0 6px rgba(229,188,61,0.55));
    }
</style>
