<!-- resources/views/admin/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id" class="{{ app_setting('dark_mode') ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Smart Tourism</title>
    @if(app_setting('favicon'))
        <link rel="icon" href="{{ image_url(app_setting('favicon')) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sidebar: 'var(--primary-color, #066466)',
                        'sidebar-hover': 'var(--primary-color, #055456)',
                        'sidebar-active': 'var(--secondary-color, #10B981)',
                        primary: 'var(--primary-color, #066466)',
                        secondary: 'var(--secondary-color, #10B981)',
                        'toba-gold': '#e5bc3d',
                        light: '#F9FAFB',
                    }
                }
            }
        }
    </script>

    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js (Conditional) -->
    @stack('charts')

    <!-- Icons (Heroicons) -->
    <script defer src="https://cdn.jsdelivr.net/npm/heroicons@1.0.6/solid/index.min.js"></script>

    @stack('styles')
    <style>
        :root {
            --primary-color: {{ app_setting('primary_color', '#066466') }};
            --secondary-color: {{ app_setting('secondary_color', '#10B981') }};
        }
        [x-cloak] {
            display: none !important;
        }
         html {
             font-size: 13px;
         }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(6,100,102,0.2); border-radius: 2px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(6,100,102,0.4); }

        /* Global Gradient for Buttons */
        button.bg-sidebar, a.bg-sidebar, button.bg-primary, a.bg-primary, button[type="submit"]:not(.bg-[#EF4444]):not(.bg-[#dc2626]) {
            background: var(--primary-color, #066466) !important;
            border: none !important;
            transition: all 0.3s ease !important;
        }
        button.bg-sidebar:hover, a.bg-sidebar:hover, button.bg-primary:hover, a.bg-primary:hover, button[type="submit"]:not(.bg-[#EF4444]):not(.bg-[#dc2626]):hover {
            background: var(--primary-color, #066466) !important;
            filter: brightness(90%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-1px);
        }
        /* Page transition disabled to keep navigation fully static */
        .page-enter,
        .page-leave {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        /* Sidebar is fixed in expanded mode */
        .sidebar-collapsed-only {
            display: none !important;
        }

        /* ── Sidebar: suppress transition on first paint ── */
        .sidebar-no-transition,
        .sidebar-no-transition * {
            transition: none !important;
        }

        /* Content management modal polish */
        div[x-show="showCreateModal"].fixed.inset-0.z-50.overflow-y-auto > div > div.bg-white,
        div[x-show="showEditModal"].fixed.inset-0.z-50.overflow-y-auto > div > div.bg-white {
            border-radius: 1.5rem !important;
            border: 1px solid rgba(229, 231, 235, 0.9) !important;
            box-shadow: 0 35px 80px -20px rgba(15, 23, 42, 0.35) !important;
        }

        /* Scroll performance: disable pointer-events on hover tooltips while scrolling */
        .is-scrolling .group:hover > div[class*="opacity-0"],
        .is-scrolling [class*="group-hover\\:opacity-100"] {
            opacity: 0 !important;
            pointer-events: none !important;
        }

        /* Tooltip transition — hanya opacity, bukan transition-all */
        [class*="group-hover\\:opacity-100"] {
            transition: opacity 150ms ease !important;
        }

        /* Isolate table repaint dari halaman */
        .review-table-wrap {
            contain: layout;
        }

        /* ══════════════════════════════════════════════════════════════
           Dark Mode Global Overrides — aktif saat <html> memiliki .dark
           ══════════════════════════════════════════════════════════════ */
        html.dark { color-scheme: dark; }
        html.dark body { background-color: #0f172a !important; color: #e2e8f0; }

        /* ── Backgrounds ─────────────────────────────────────────────── */
        html.dark .bg-white          { background-color: #1e293b !important; }
        html.dark .bg-light          { background-color: #0f172a !important; }
        html.dark .bg-gray-50        { background-color: #162032 !important; }
        html.dark .bg-gray-100       { background-color: #1e293b !important; }
        html.dark .bg-gray-200       { background-color: #293c52 !important; }
        html.dark .bg-gray-50\/30    { background-color: rgba(22,32,50,.6) !important; }
        html.dark .bg-gray-50\/50    { background-color: rgba(22,32,50,.5) !important; }
        html.dark .hover\:bg-gray-50:hover  { background-color: #1e293b !important; }
        html.dark .hover\:bg-gray-100:hover { background-color: #293c52 !important; }
        html.dark .hover\:bg-gray-200:hover { background-color: #334155 !important; }
        html.dark .focus\:bg-white:focus    { background-color: #1e293b !important; }

        /* ── Text ────────────────────────────────────────────────────── */
        html.dark .text-gray-900 { color: #f1f5f9 !important; }
        html.dark .text-gray-800 { color: #e2e8f0 !important; }
        html.dark .text-gray-700 { color: #cbd5e1 !important; }
        html.dark .text-gray-600 { color: #94a3b8 !important; }
        html.dark .text-gray-500 { color: #64748b !important; }
        html.dark .text-gray-400 { color: #475569 !important; }
        html.dark h1, html.dark h2, html.dark h3, html.dark h4 { color: #f1f5f9; }

        /* ── Borders ─────────────────────────────────────────────────── */
        html.dark .border-gray-100 { border-color: #1e3358 !important; }
        html.dark .border-gray-200 { border-color: #334155 !important; }
        html.dark .divide-gray-100 > * + * { border-color: #1e3358 !important; }
        html.dark .divide-gray-200 > * + * { border-color: #334155 !important; }

        /* ── Form Controls ───────────────────────────────────────────── */
        html.dark input:not([type=checkbox]):not([type=radio]):not([type=color]):not([type=file]),
        html.dark select,
        html.dark textarea {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder { color: #475569 !important; }
        html.dark label.text-gray-700  { color: #cbd5e1 !important; }

        /* ── Header / Navbar ─────────────────────────────────────────── */
        html.dark header {
            background-color: #1e293b !important;
            border-color: #1e3358 !important;
        }

        /* ── Tables ──────────────────────────────────────────────────── */
        html.dark thead tr  { background-color: #162032 !important; }
        html.dark thead th  { color: #94a3b8 !important; border-color: #1e3358 !important; }
        html.dark tbody tr  { border-color: #1e3358 !important; }
        html.dark tbody tr:hover { background-color: #162032 !important; }
        html.dark tbody td  { color: #cbd5e1 !important; border-color: #1e3358 !important; }

        /* ── Dropdowns & Modals ──────────────────────────────────────── */
        html.dark .shadow-xl  { box-shadow: 0 20px 40px -12px rgba(0,0,0,.7) !important; }
        html.dark .shadow-2xl { box-shadow: 0 30px 60px -12px rgba(0,0,0,.7) !important; }

        /* ── Colored Alerts — keep tinted but dimmed ─────────────────── */
        html.dark .bg-red-50      { background-color: rgba(239,68,68,.12)   !important; }
        html.dark .bg-blue-50     { background-color: rgba(59,130,246,.12)  !important; }
        html.dark .bg-emerald-50  { background-color: rgba(16,185,129,.12)  !important; }
        html.dark .bg-green-50    { background-color: rgba(16,185,129,.12)  !important; }
        html.dark .bg-yellow-50   { background-color: rgba(234,179,8,.12)   !important; }
        html.dark .bg-amber-50    { background-color: rgba(245,158,11,.12)  !important; }
        html.dark .bg-purple-50   { background-color: rgba(168,85,247,.12)  !important; }
        html.dark .bg-indigo-50   { background-color: rgba(99,102,241,.12)  !important; }
        html.dark .bg-orange-50   { background-color: rgba(249,115,22,.12)  !important; }

        /* ── Alert text colors — keep vivid ──────────────────────────── */
        html.dark .text-red-800    { color: #fca5a5 !important; }
        html.dark .text-blue-900   { color: #93c5fd !important; }
        html.dark .text-blue-600   { color: #60a5fa !important; }
        html.dark .text-red-900    { color: #fca5a5 !important; }
        html.dark .text-red-600    { color: #f87171 !important; }

        /* ── Borders on alert boxes ───────────────────────────────────── */
        html.dark .border-red-200\/40  { border-color: rgba(248,113,113,.25)  !important; }
        html.dark .border-blue-200     { border-color: rgba(147,197,253,.25)  !important; }
        html.dark .border-red-200      { border-color: rgba(248,113,113,.25)  !important; }

        /* ── Footer ──────────────────────────────────────────────────── */
        html.dark footer { border-color: #1e3358 !important; }
        html.dark footer p { color: #475569 !important; }

        /* ── Sidebar active menu items ────────────────────────────────── */
        html.dark .bg-white\/15 { background-color: rgba(255,255,255,.15) !important; }
        html.dark .bg-white\/10 { background-color: rgba(255,255,255,.10) !important; }
        html.dark .hover\:bg-white\/10:hover { background-color: rgba(255,255,255,.10) !important; }

        /* ── Hover states used in navbar & menus ─────────────────────────── */
        html.dark .hover\:bg-emerald-50:hover { background-color: rgba(16,185,129,.08) !important; }
        html.dark .hover\:bg-emerald-100:hover { background-color: rgba(16,185,129,.15) !important; }
        html.dark .hover\:text-emerald-600:hover { color: #34d399 !important; }
        html.dark .hover\:text-emerald-700:hover { color: #34d399 !important; }
        html.dark .focus\:ring-sidebar\/10 { --tw-ring-color: rgba(6,100,102,.2) !important; }

        /* ── Success flash message bg ────────────────────────────────────── */
        html.dark .bg-\[\#E6F6F2\] { background-color: rgba(0,168,132,.1) !important; }
        html.dark .border-\[\#00A884\]\/20 { border-color: rgba(0,168,132,.2) !important; }

        /* ── Dark mode smooth page-level transition only ─────────────────── */
        /* Avoid html.dark * { transition } — too broad, causes lag on interactions */
        html { transition: background-color 300ms ease; }
        html.dark body, html.dark header, html.dark main, html.dark footer,
        html.dark nav, html.dark aside {
            transition: background-color 300ms ease, border-color 200ms ease;
        }
    </style>
</head>
<body class="bg-light">
    @auth('admin')
           <div class="flex h-screen overflow-hidden sidebar-no-transition"
               x-data="{ sidebarOpen: {{ request()->cookie('sidebarOpen', 'true') === 'true' ? 'true' : 'false' }} }"
               x-init="
                    $watch('sidebarOpen', value => document.cookie = 'sidebarOpen=' + value + '; path=/; max-age=31536000');
                    window.addEventListener('resize', () => {
                        if (window.innerWidth < 1024 && sidebarOpen && !window.hasClosedSidebarForMobile) {
                            sidebarOpen = false;
                            window.hasClosedSidebarForMobile = true;
                        } else if (window.innerWidth >= 1024) {
                            window.hasClosedSidebarForMobile = false;
                        }
                    });
                    if (window.innerWidth < 1024) { sidebarOpen = false; }
               ">

            <!-- Mobile Backdrop -->
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false" 
                 x-transition:enter="transition-opacity ease-linear duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition-opacity ease-linear duration-300" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-black/50 z-40 lg:hidden" 
                 x-cloak>
            </div>

            <!-- Sidebar -->
            @include('admin.layouts.sidebar')

            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen" 
                 x-transition.opacity 
                 class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden backdrop-blur-sm"
                 @click="sidebarOpen = false"
                 x-cloak></div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Header -->
                @include('admin.layouts.navbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-auto custom-scrollbar flex flex-col">
                    <div id="page-content" class="max-w-[1700px] w-full mx-auto px-6 md:px-10 py-8 page-enter flex-1">
                        <!-- Breadcrumb -->
                        @yield('breadcrumb')

                        <!-- Page Title & Actions -->
                        <div class="mb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">@yield('page_title')</h1>
                                <p class="text-gray-500 text-sm mt-1">@yield('page_description')</p>
                            </div>
                            @hasSection('page_actions')
                            <div>
                                @yield('page_actions')
                            </div>
                            @endif
                        </div>

                        <!-- Alerts -->
                        <div id="dynamic-alerts-container"></div>

                        @if ($errors->any())
                            <div class="mb-5 p-4 bg-red-50 border border-red-200/40 rounded-2xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex-shrink-0 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-white">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-red-800">Kesalahan Validasi</p>
                                </div>
                                <ul class="list-disc list-inside text-xs text-red-700 pl-8 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="mb-5 p-4 bg-[#E6F6F2] border border-[#00A884]/20 rounded-2xl flex items-center gap-3">
                                <div class="flex-shrink-0 w-5 h-5 bg-[#00A884] rounded-full flex items-center justify-center text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-[#00A884]">{{ session('success') }}</p>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-5 p-4 bg-red-50 border border-red-200/40 rounded-2xl flex items-center gap-3">
                                <div class="flex-shrink-0 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="mb-5 p-4 bg-blue-50 border border-blue-200/40 rounded-2xl flex items-center gap-3">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-blue-800">{{ session('info') }}</p>
                            </div>
                        @endif

                        <!-- Page Content -->
                        @yield('content')
                    </div>

                    <!-- Footer -->
                    <footer class="w-full px-6 md:px-10 py-6 mt-auto">
                        <div class="max-w-[1700px] mx-auto border-t border-gray-100 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                            <p class="text-[13px] text-gray-400 font-medium">
                                &copy; {{ date('Y') }} <span class="text-sidebar font-bold">Aplikasi Wisata Toba</span>. Hak Cipta Dilindungi.
                            </p>
                            <p class="text-[13px] text-gray-400 font-medium">
                                Versi 1.0.0
                            </p>
                        </div>
                    </footer>
                </main>
            </div>
        </div>
    @endauth

    @guest('admin')
        <main class="min-h-screen">
            @yield('content')
        </main>
    @endguest

    <!-- Global Delete Modal -->
    <div x-data="{ show: false, action: '', title: '', type: '', name: '' }"
         @open-delete-modal.window="show = true; action = $event.detail.action; title = $event.detail.title; type = $event.detail.type; name = $event.detail.name"
         x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md bg-white shadow-2xl rounded-[2rem] text-gray-800 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <div class="px-8 pt-10 pb-6 text-center">
                    <div class="w-20 h-20 bg-[#FEE2E2] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="40" height="40" style="width: 40px; height: 40px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-3" x-text="title"></h3>
                    
                    <p class="text-[15px] text-gray-500 mb-4 leading-relaxed">
                        Apakah Anda yakin ingin menghapus <span x-text="type"></span> <strong class="text-gray-800" x-text="`&quot;${name}&quot;`"></strong>?
                    </p>
                    <p class="text-[13px] text-red-500 font-medium">Tindakan ini tidak dapat dibatalkan.</p>
                </div>

                <div class="flex items-center justify-center gap-4 px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                    <button type="button" @click="show = false" class="w-full px-6 py-3.5 text-[15px] font-bold text-gray-700 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                        Batal
                    </button>
                    <form :action="action" method="POST" class="w-full m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-3.5 text-[15px] font-bold text-white bg-[#EF4444] rounded-2xl hover:bg-red-600 transition-all shadow-[0_8px_20px_-6px_rgba(239,68,68,0.5)] whitespace-nowrap">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div x-data="{ showLogout: false }"
         @open-logout-modal.window="showLogout = true"
         x-show="showLogout" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <!-- Backdrop -->
            <div x-show="showLogout" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="showLogout = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="showLogout" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-sm bg-white shadow-2xl rounded-[2rem] text-gray-800 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <div class="px-8 py-6 text-center">
                    <div class="w-[72px] h-[72px] bg-[#fef2f2] rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-[#dc2626]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </div>

                    <h3 class="text-[20px] font-bold text-gray-900 mb-3">Keluar dari Sistem?</h3>
                    <p class="text-[14px] text-gray-500 mb-2 leading-relaxed">
                        Apakah Anda yakin ingin keluar? Anda harus login kembali untuk mengakses panel admin.
                    </p>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST" class="flex flex-col gap-3 px-8 py-6 border-t border-gray-100">
                    @csrf
                    <button type="submit" class="w-full py-3 text-[14px] font-bold text-white bg-[#dc2626] hover:bg-[#b91c1c] rounded-xl transition-all shadow-[0_4px_12px_-4px_rgba(220,38,38,0.5)]">
                        Ya, Keluar
                    </button>
                </form>
                <div class="flex gap-3 px-8 pb-6">
                    <button type="button" @click="showLogout = false" class="flex-1 py-3 text-[14px] font-bold text-gray-600 border border-gray-200 rounded-2xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Alert Modal -->
    <div id="global-alert-modal"
         x-data="{ show: false, title: 'Perhatian', message: '', type: 'error' }"
         @show-alert.window="show = true; message = $event.detail.message; title = $event.detail.title || 'Perhatian'; type = $event.detail.type || 'error'"
         x-show="show" class="fixed inset-0 z-[150] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <!-- Backdrop -->
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-sm bg-white shadow-2xl rounded-[2rem] text-gray-800 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <div class="px-8 py-6 text-center mt-4">
                    <!-- Icon based on type -->
                    <div class="mx-auto mb-6">
                        <!-- Error Icon -->
                        <div x-show="type === 'error'" class="w-[72px] h-[72px] bg-red-50 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        
                        <!-- Warning Icon -->
                        <div x-show="type === 'warning'" class="w-[72px] h-[72px] bg-amber-50 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                        </div>

                        <!-- Success Icon -->
                        <div x-show="type === 'success'" class="w-[72px] h-[72px] bg-emerald-50 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>

                        <!-- Info Icon -->
                        <div x-show="type === 'info'" class="w-[72px] h-[72px] bg-blue-50 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-[20px] font-bold text-gray-900 mb-3" x-text="title"></h3>
                    <p class="text-[14px] text-gray-500 mb-2 leading-relaxed px-2" x-text="message"></p>
                </div>

                <div class="flex gap-3 px-8 pb-6 justify-center">
                    <button type="button" @click="show = false" class="w-full py-3 text-[14px] font-bold text-white bg-sidebar rounded-xl transition-all shadow-[0_4px_12px_-4px_rgba(6,100,102,0.3)]">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Global helper to show the custom alert modal
        // Uses direct DOM manipulation as primary method + Alpine event as fallback
        window.showAlert = function(message, title, type) {
            title = title || 'Perhatian';
            type  = type  || 'error';

            // --- Method 1: Direct DOM manipulation (works even before Alpine is ready) ---
            var alertEl = document.getElementById('global-alert-modal');
            if (alertEl) {
                var _x = Alpine && Alpine.$data ? Alpine.$data(alertEl) : null;
                if (_x) {
                    _x.show    = true;
                    _x.message = message;
                    _x.title   = title;
                    _x.type    = type;
                    return;
                }
            }

            // --- Method 2: CustomEvent (Alpine listener) ---
            var doDispatch = function() {
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { message: message, title: title, type: type }
                }));
            };
            doDispatch();
            setTimeout(doDispatch, 80);
        };

        // Override default window.alert to route through the custom modal
        window.alert = function(message) {
            window.showAlert(message, 'Perhatian', 'error');
        };

        // Global error handler for server responses (422, 500, etc)
        // Standardized handling of error.errors field validation messages
        window.handleServerError = function(error, currentThis) {
            if (currentThis && typeof currentThis.showUploadProgress !== 'undefined') {
                currentThis.showUploadProgress = false;
            }
            
            if (error && error.errors) {
                // Tampilkan error validasi field pertama yang ditemukan
                const firstField = Object.keys(error.errors)[0];
                const firstMsg = error.errors[firstField][0];
                window.showAlert(
                    (error.message || 'Terdapat kesalahan validasi.') + '\n\n' + firstMsg,
                    'Validasi Gagal',
                    'error'
                );
            } else {
                window.showAlert(error?.message || 'Gagal menyimpan data. Silakan coba lagi.', 'Gagal', 'error');
            }
        };

        // CSRF token for AJAX (Vanilla JS)
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (typeof jQuery !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        }

        // Helper to safely parse JSON from a fetch response (handles prepended PHP warnings)
        window.safeParseJSON = async function(response) {
            let text = '';
            try {
                text = await response.text();
            } catch (e) {
                // CORB or network error blocked the response body
                throw new Error('Response was blocked or network error occurred. Status: ' + response.status);
            }
            if (!text || text.trim() === '') {
                // Empty body — could be CORB block or redirect
                if (!response.ok) {
                    throw new Error('Server returned status ' + response.status + ' with empty response.');
                }
                return null;
            }
            try {
                return JSON.parse(text);
            } catch (error) {
                try {
                    // Find the first { or [ to extract valid JSON
                    const firstBrace = text.indexOf('{');
                    const firstBracket = text.indexOf('[');
                    const startIdx = (firstBrace !== -1 && firstBracket !== -1) 
                        ? Math.min(firstBrace, firstBracket) 
                        : Math.max(firstBrace, firstBracket);
                        
                    if (startIdx !== -1) {
                        return JSON.parse(text.substring(startIdx));
                    }
                } catch (fallbackError) {}
                
                console.error("Invalid JSON response:", text.substring(0, 500));
                throw new Error("Server returned an invalid response. Status: " + response.status);
            }
        };

        // Show confirmation before delete
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }

        // Format currency
        function formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }

        // Format number
        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        // Skeleton Loading Injector for Instant Perceived Navigation & Refresh
        document.addEventListener('DOMContentLoaded', () => {
            
            // Check for pending success/error notifications in localStorage
            const pendingSuccess = localStorage.getItem('pending_success_toast');
            if (pendingSuccess) {
                const container = document.getElementById('dynamic-alerts-container');
                if (container) {
                    container.innerHTML = `
                        <div class="mb-5 p-4 bg-[#E6F6F2] border border-[#00A884]/20 rounded-2xl flex items-center gap-3">
                            <div class="flex-shrink-0 w-5 h-5 bg-[#00A884] rounded-full flex items-center justify-center text-white">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-[#00A884]">${pendingSuccess}</p>
                        </div>
                    `;
                }
                localStorage.removeItem('pending_success_toast');
            }

            const pendingError = localStorage.getItem('pending_error_toast');
            if (pendingError) {
                const container = document.getElementById('dynamic-alerts-container');
                if (container) {
                    container.innerHTML = `
                        <div class="mb-5 p-4 bg-red-50 border border-red-200/40 rounded-2xl flex items-center gap-3">
                            <div class="flex-shrink-0 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-red-800">${pendingError}</p>
                        </div>
                    `;
                }
                localStorage.removeItem('pending_error_toast');
            }
            
            // Generic Loading State for ALL layouts (Dashboard, Settings, Lists)
            const showSkeletonLoader = () => {
                const pageContent = document.getElementById('page-content');
                if (pageContent && !pageContent.dataset.skeletonActive) {
                    pageContent.dataset.skeletonActive = "true";
                    
                    // Instead of a fake skeleton that mismatches the layout, 
                    // we dim the CURRENT layout and make it pulse smoothly.
                    pageContent.style.transition = 'opacity 0.2s ease-out';
                    pageContent.classList.add('opacity-40', 'animate-pulse', 'pointer-events-none', 'select-none');
                    
                    // Optional: Add a small loading indicator at the top right of the page content
                    const loaderBadge = document.createElement('div');
                    loaderBadge.className = 'fixed top-24 right-10 bg-white/80 backdrop-blur-md px-4 py-2 rounded-full shadow-lg border border-gray-100 flex items-center gap-3 z-50 animate-bounce';
                    loaderBadge.innerHTML = `
                        <svg class="animate-spin h-5 w-5 text-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Memuat...</span>
                    `;
                    document.body.appendChild(loaderBadge);
                }
            };

            // 1. Intercept sidebar & internal link clicks
            const internalLinks = document.querySelectorAll('a[href^="{{ url('/admin') }}"]:not([target="_blank"]):not([href="#"])');
            internalLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.defaultPrevented) return;
                    const href = this.getAttribute('href');
                    if (href === window.location.href) return;
                    showSkeletonLoader();
                });
            });

            // 2. Intercept Page Refresh (F5), Form Submissions, and Browser Navigation
            window.addEventListener('beforeunload', function () {
                showSkeletonLoader();
            });
        });
    </script>

    @if (!empty($errors) && $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showAlert('Terdapat kesalahan validasi pada formulir. Silakan periksa kembali input Anda.', 'Validasi Gagal', 'error');
            });
        </script>
    @endif

    @stack('scripts')
    <script>
        // Disable expensive hover effects while scrolling for better frame rate
        (function() {
            const main = document.querySelector('main.overflow-auto');
            if (!main) return;
            let t;
            main.addEventListener('scroll', function() {
                document.body.classList.add('is-scrolling');
                clearTimeout(t);
                t = setTimeout(function() {
                    document.body.classList.remove('is-scrolling');
                }, 150);
            }, { passive: true });
        })();
    </script>
</body>
</html>
