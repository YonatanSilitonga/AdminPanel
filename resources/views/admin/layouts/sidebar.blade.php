<!-- resources/views/admin/layouts/sidebar.blade.php -->
@php
    $admin = auth('admin')->user();
    $name = $admin?->name ?? 'Admin User';
    $initials = collect(explode(' ', $name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('');
    $email = $admin?->email ?? 'admin@toba.id';
    $isSidebarOpen = request()->cookie('sidebarOpen', 'true') === 'true';

    // Arsitektur Sidebar Berbasis Template Array
    // Mengubah, menambah, atau menghapus menu cukup dilakukan pada array di bawah ini
    // tanpa perlu mengubah kerangka HTML sama sekali.
    $menuItems = [
        [
            'type' => 'link',
            'label' => 'Dashboard',
            'route' => route('admin.dashboard'),
            'active' => request()->routeIs('admin.dashboard'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>'
        ],
        [
            'type' => 'section',
            'label' => 'Content Management'
        ],
        [
            'type' => 'link',
            'label' => 'Destinasi',
            'route' => route('admin.destinations.index'),
            'active' => request()->routeIs('admin.destinations.*') || request()->routeIs('admin.trending.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>'
        ],
        [
            'type' => 'link',
            'label' => 'Kelola Event',
            'route' => route('admin.events.index'),
            'active' => request()->routeIs('admin.events.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>'
        ],
        [
            'type' => 'link',
            'label' => 'Carousel dan Banner',
            'route' => route('admin.carousel_banners.index'),
            'active' => request()->routeIs('admin.carousel_banners.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'
        ],
        [
            'type' => 'link',
            'label' => 'Fasilitas Umum',
            'route' => route('admin.fasilitas_umum.index'),
            'active' => request()->routeIs('admin.fasilitas_umum.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>'
        ],
        [
            'type' => 'link',
            'label' => 'Berita & Promosi',
            'route' => route('admin.berita_promosi.index'),
            'active' => request()->routeIs('admin.berita_promosi.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v4a2 2 0 002 2h4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h3m-3 4h5m-5 4h5"></path>'
        ],
        [
            'type' => 'link',
            'label' => 'Budaya dan Warisan',
            'route' => route('admin.budaya.index'),
            'active' => request()->routeIs('admin.budaya.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>'
        ],
        [
            'type' => 'section',
            'label' => 'Administration'
        ],
        [
            'type' => 'link',
            'label' => 'Manajemen Pengguna',
            'route' => route('admin.users.index'),
            'active' => request()->routeIs('admin.users.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>'
        ],
        [
            'type' => 'section',
            'label' => 'Monitoring'
        ],
        [
            'type' => 'dropdown',
            'label' => 'Fitur AI dan Cerdas',
            'openKey' => 'smartFeatures',
            'active' => request()->routeIs('admin.chatbot-logs.*') || request()->routeIs('admin.recommendations.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>',
            'subItems' => [
                ['label' => 'Log Chatbot', 'route' => route('admin.chatbot-logs.index'), 'active' => request()->routeIs('admin.chatbot-logs.index')],
                ['label' => 'Log Rekomendasi', 'route' => route('admin.recommendations.index'), 'active' => request()->routeIs('admin.recommendations.index')]
            ]
        ],
        [
            'type' => 'dropdown',
            'label' => 'Ulasan & Laporan',
            'openKey' => 'ulasan',
            'active' => request()->routeIs('admin.reviews.*') || request()->routeIs('admin.reports.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>',
            'subItems' => [
                ['label' => 'Ringkasan Ulasan', 'route' => route('admin.reviews.index'), 'active' => request()->routeIs('admin.reviews.index')],
                ['label' => 'Laporan Masuk', 'route' => route('admin.reports.index'), 'active' => request()->routeIs('admin.reports.index')]
            ]
        ],
        [
            'type' => 'section',
            'label' => 'Settings'
        ],
        [
            'type' => 'link',
            'label' => 'Pengaturan Sistem',
            'route' => route('admin.settings.general'),
            'active' => request()->routeIs('admin.settings.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>'
        ]
    ];
@endphp

<div 
    x-data="{ 
        openMenus: {
            destinasi: {{ request()->routeIs('admin.destinations.*') || request()->routeIs('admin.trending.*') ? 'true' : 'false' }},
            smartFeatures: {{ request()->routeIs('admin.chatbot-logs.*') || request()->routeIs('admin.recommendations.*') ? 'true' : 'false' }},
            ulasan: {{ request()->routeIs('admin.reviews.*') || request()->routeIs('admin.reports.*') ? 'true' : 'false' }}
        } 
    }" 
    class="fixed inset-y-0 left-0 lg:relative text-white h-screen flex flex-col shadow-2xl lg:shadow-xl overflow-hidden flex-shrink-0 z-50 transition-all duration-300 ease-in-out group/sidebar"
    style="background: linear-gradient(135deg, #065f46, #047857, #059669);"
    :class="{
        'w-72 translate-x-0': sidebarOpen,
        '-translate-x-full lg:translate-x-0 lg:w-16 sidebar-collapsed': !sidebarOpen
    }"
>
    <!-- Logo Section -->
    <div class="sidebar-header py-5 px-5 flex items-center justify-between border-b border-white/10 transition-all duration-300">
        <div class="flex items-center">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Toba Tourism Logo" class="w-10 h-10 rounded-full object-cover shadow-lg flex-shrink-0 transition-transform duration-200 hover:scale-110 border border-white/20" width="40" height="40" style="width: 40px; height: 40px;">
            <div class="sidebar-text ml-3 overflow-hidden whitespace-nowrap transition-all duration-300">
                <h2 class="text-lg font-bold tracking-wider leading-tight">TOBA TOURISM</h2>
                <p class="text-xs text-gray-300 opacity-80">Kawasan Danau Toba</p>
            </div>
        </div>
        
        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-toba-gold transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- <!-- User Profile Section -->
    <div class="sidebar-header py-4 px-5 flex items-center border-b border-white/10 mb-2 transition-all duration-300">
        <div class="relative group/avatar flex-shrink-0">
            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center border border-white/20 text-sm font-bold transition-transform duration-200 group-hover/avatar:scale-110 group-hover/avatar:border-toba-gold cursor-default">
                {{ $initials }}
            </div>
            <!-- Tooltip for collapsed avatar -->
            <div class="sidebar-tooltip-wrapper absolute left-full ml-3 top-1/2 -translate-y-1/2 z-[60] pointer-events-none transition-all duration-200 whitespace-nowrap opacity-0 group-hover/avatar:opacity-100 translate-x-1 group-hover/avatar:translate-x-0">
                <div class="bg-gray-900 text-white text-xs rounded-lg px-3 py-2 shadow-xl">
                    <p class="font-semibold">{{ $name }}</p>
                    <p class="text-gray-400 text-[10px]">{{ $email }}</p>
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-gray-900"></div>
                </div>
            </div>
        </div>
        <div class="sidebar-text ml-3 overflow-hidden whitespace-nowrap transition-all duration-300">
            <p class="text-sm font-semibold truncate">{{ $name }}</p>
            <p class="text-xs text-gray-400 truncate">{{ $email }}</p>
        </div>
    </div> --}}

    <!-- Navigation Menu (Dihasilkan Otomatis oleh Template) -->
    <nav class="sidebar-nav flex-1 overflow-y-auto py-4 px-4 space-y-1 custom-scrollbar transition-all duration-300">
        @foreach($menuItems as $item)
            @if($item['type'] === 'section')
                <!-- Section Header -->
                <div class="sidebar-section-header px-4 py-3 mt-4 mb-2 transition-all duration-300">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap overflow-hidden">{{ $item['label'] }}</p>
                </div>
                <div class="sidebar-divider my-2 border-t border-white/10 transition-all duration-300"></div>
            @elseif($item['type'] === 'link')
                <!-- Normal Link -->
                <div class="relative group/item">
                    <a href="{{ $item['route'] }}" class="sidebar-link px-4 py-2 flex items-center justify-start text-left rounded-xl transition-all duration-300 {{ $item['active'] ? 'bg-sidebar-active text-white shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="sidebar-icon w-5 h-5 flex-shrink-0 transition-all duration-300 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="width: 20px; height: 20px;">
                            {!! $item['icon'] !!}
                        </svg>
                        <span class="sidebar-text ml-3 font-medium whitespace-nowrap overflow-hidden transition-all duration-300">{{ $item['label'] }}</span>
                    </a>
                    <!-- Tooltip -->
                    <div class="sidebar-tooltip"><span>{{ $item['label'] }}</span><div class="sidebar-tooltip-arrow"></div></div>
                </div>
            @elseif($item['type'] === 'dropdown')
                <!-- Dropdown -->
                <div x-data="{ open: openMenus.{{ $item['openKey'] }} }" class="relative group/item">
                    <!-- Collapsed Icon Only Link -->
                    <a href="{{ $item['subItems'][0]['route'] ?? '#' }}" class="sidebar-collapsed-only flex justify-center items-center py-2 rounded-xl transition-all duration-200 {{ $item['active'] ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="sidebar-icon w-5 h-5 transition-transform duration-200 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="width: 20px; height: 20px;">
                            {!! $item['icon'] !!}
                        </svg>
                    </a>
                    <!-- Expanded Button -->
                    <button @click="open = !open" class="sidebar-expanded-only w-full flex items-center px-4 py-2 rounded-xl transition-all duration-200 {{ $item['active'] ? 'text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="sidebar-icon w-5 h-5 flex-shrink-0 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="width: 20px; height: 20px;">
                            {!! $item['icon'] !!}
                        </svg>
                        <span class="sidebar-text ml-3 font-medium flex-1 text-left whitespace-nowrap overflow-hidden transition-all duration-300">{{ $item['label'] }}</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 flex-shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <!-- Submenu Items -->
                    <div x-show="open" x-transition class="sidebar-expanded-only ml-10 mt-1 space-y-1" {!! $item['active'] ? '' : 'style="display: none;"' !!}>
                        @foreach($item['subItems'] as $subItem)
                            <a href="{{ $subItem['route'] }}" class="block px-4 py-2 text-sm rounded-lg {{ $subItem['active'] ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white' }} transition-colors whitespace-nowrap">
                                {{ $subItem['label'] }}
                            </a>
                        @endforeach
                    </div>
                    <!-- Tooltip -->
                    <div class="sidebar-tooltip"><span>{{ $item['label'] }}</span><div class="sidebar-tooltip-arrow"></div></div>
                </div>
            @endif
        @endforeach
    </nav>

    <!-- Logout Section -->
    <div class="sidebar-header mt-auto py-6 px-5 border-t border-white/10 transition-all duration-300">
        <div class="relative group/item">
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-logout-modal'))"
                    class="sidebar-link w-full px-4 py-2 flex items-center justify-start text-right rounded-xl transition-all duration-300 text-gray-300 hover:bg-red-500/10 hover:text-red-400 group">
                <svg class="sidebar-icon w-5 h-5 flex-shrink-0 transition-all duration-300 group-hover:-translate-x-0.5 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="sidebar-text ml-3 font-medium whitespace-nowrap overflow-hidden transition-all duration-300">Logout</span>
            </button>
            <div class="sidebar-tooltip sidebar-tooltip-up"><span>Logout</span><div class="sidebar-tooltip-arrow"></div></div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

    /* --- Sidebar CSS Template Logic --- */
    /* Menggantikan semua transisi Alpine x-show yang rawan glitch dengan kontrol murni CSS */

    .sidebar-collapsed-only { display: none !important; }
    .sidebar-divider { display: none; }
    
    .sidebar-tooltip-wrapper { display: none; }
    .sidebar-tooltip {
        display: none;
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
        display: block; background: #111827; color: #f9fafb; font-size: 0.75rem;
        font-weight: 500; padding: 6px 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.35);
    }
    .sidebar-tooltip-arrow {
        position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
        border: 5px solid transparent; border-right-color: #111827;
    }
    .sidebar-tooltip-up {
        top: auto; bottom: 50%; transform: translateY(50%) translateX(4px);
    }
    .sidebar-tooltip-up .sidebar-tooltip-arrow { top: 50%; }

    /* Ketika Sidebar di-collapse (Class .sidebar-collapsed ditambahkan oleh Alpine ke pembungkus utama) */
    .sidebar-collapsed .sidebar-header { padding-left: 0.75rem !important; padding-right: 0.75rem !important; justify-content: center !important; }
    .sidebar-collapsed .sidebar-nav { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
    .sidebar-collapsed .sidebar-text { max-width: 0 !important; opacity: 0 !important; margin-left: 0 !important; }
    .sidebar-collapsed .sidebar-icon { width: 1.5rem !important; height: 1.5rem !important; }
    .sidebar-collapsed .sidebar-link { padding-left: 0 !important; padding-right: 0 !important; justify-content: center !important; }
    
    .sidebar-collapsed .sidebar-section-header { display: none !important; }
    .sidebar-collapsed .sidebar-divider { display: block !important; }
    
    .sidebar-collapsed .sidebar-expanded-only { display: none !important; }
    .sidebar-collapsed .sidebar-collapsed-only { display: flex !important; }

    /* Tooltips hanya aktif ketika sidebar collapse */
    .sidebar-collapsed .sidebar-tooltip { display: block; }
    .sidebar-collapsed .sidebar-tooltip-wrapper { display: block; }
    .sidebar-collapsed .group\/item:hover .sidebar-tooltip { opacity: 1; transform: translateY(-50%) translateX(0); }
    .sidebar-collapsed .group\/item:hover .sidebar-tooltip-up { opacity: 1; transform: translateY(50%) translateX(0); }
    
    /* Icon glow pulse ketika di-hover (saat collapse) */
    .sidebar-collapsed .group\/item:hover svg { filter: drop-shadow(0 0 6px rgba(229,188,61,0.55)); }
</style>
