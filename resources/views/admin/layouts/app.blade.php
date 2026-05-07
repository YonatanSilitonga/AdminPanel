<!-- resources/views/admin/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Smart Tourism</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        danger: '#EF4444',
                        warning: '#F59E0B',
                        info: '#06B6D4',
                        dark: '#1F2937',
                        light: '#F9FAFB',
                        sidebar: {
                            DEFAULT: '#066466',
                            hover: '#055456',
                            active: '#197a7c',
                        },
                        toba: {
                            gold: '#e5bc3d',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- Icons (Heroicons) -->
    <script src="https://cdn.jsdelivr.net/npm/heroicons@1.0.6/solid/index.min.js"></script>

    @stack('styles')
    <style>
        html {
            font-size: 13px;
        }
        @media (min-width: 1536px) {
            html {
                font-size: 14px;
            }
        }
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(6,100,102,0.2); border-radius: 2px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(6,100,102,0.4); }

        /* Global Gradient for Buttons */
        button.bg-sidebar, a.bg-sidebar, button.bg-primary, a.bg-primary, button[type="submit"]:not(.bg-[#EF4444]):not(.bg-[#dc2626]) {
            background: linear-gradient(135deg, #065f46, #047857, #059669) !important;
            border: none !important;
            transition: all 0.3s ease !important;
        }
        button.bg-sidebar:hover, a.bg-sidebar:hover, button.bg-primary:hover, a.bg-primary:hover, button[type="submit"]:not(.bg-[#EF4444]):not(.bg-[#dc2626]):hover {
            background: linear-gradient(135deg, #047857, #059669, #065f46) !important;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3) !important;
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
    </style>
</head>
<body class="bg-light">
    @auth('admin')
           <div class="flex h-screen overflow-hidden sidebar-no-transition"
               x-data="{ sidebarOpen: {{ request()->cookie('sidebarOpen', 'true') === 'true' ? 'true' : 'false' }} }"
               x-init="$watch('sidebarOpen', value => document.cookie = 'sidebarOpen=' + value + '; path=/; max-age=31536000')">

            <!-- Sidebar -->
            @include('admin.layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Header -->
                @include('admin.layouts.navbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-auto custom-scrollbar">
                    <div id="page-content" class="max-w-[1700px] mx-auto px-6 md:px-10 py-8 page-enter">
                        <!-- Breadcrumb -->
                        @yield('breadcrumb')

                        <!-- Page Title & Actions -->
                        <div class="mb-5 flex flex-col md:flex-row md:items-start md:items-center justify-between gap-4">
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
                        @if ($errors->any())
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Validation Errors</h3>
                                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.707-9.707a1 1 0 011.414 0L12 9.586l1.293-1.293a1 1 0 111.414 1.414L13.414 11l1.293 1.293a1 1 0 01-1.414 1.414L12 12.414l-1.293 1.293a1 1 0 01-1.414-1.414L9.586 11 8.293 9.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zM9 8a1 1 0 112 0v5a1 1 0 11-2 0V8zm1-3a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Page Content -->
                        @yield('content')
                    </div>
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
         x-show="show" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-md px-8 pt-10 pb-8 overflow-hidden text-center align-middle transition-all transform bg-white shadow-2xl rounded-[2rem] sm:my-8 text-gray-800">
                
                <div class="w-20 h-20 bg-[#FEE2E2] rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-900 mb-4" x-text="title"></h3>
                
                <p class="text-[15px] text-gray-500 mb-10 leading-relaxed px-2">
                    Apakah Anda yakin ingin menghapus <span x-text="type"></span> <strong class="text-gray-800" x-text="`&quot;${name}&quot;`"></strong>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="flex items-center justify-center gap-4 bg-gray-50/50 -mx-8 -mb-8 px-8 py-6 border-t border-gray-50">
                    <button type="button" @click="show = false" class="w-full px-6 py-3.5 text-[15px] font-bold text-gray-700 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                        Batal
                    </button>
                    <form :action="action" method="POST" class="w-full m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-3.5 text-[15px] font-bold text-white bg-[#EF4444] rounded-2xl hover:bg-red-600 transition-all shadow-[0_8px_20px_-6px_rgba(239,68,68,0.5)]">
                            <span x-text="title"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div x-data="{ showLogout: false }"
         @open-logout-modal.window="showLogout = true"
         x-show="showLogout" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="showLogout" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="showLogout = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="showLogout" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-[400px] p-8 text-center align-middle transition-all transform bg-white shadow-2xl rounded-[1.5rem] sm:my-8 text-gray-800 relative z-10">
                
                <div class="w-[72px] h-[72px] bg-[#fef2f2] rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-[#dc2626]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </div>

                <h3 class="text-[20px] font-bold text-gray-900 mb-3">Keluar dari Sistem?</h3>
                <p class="text-[14px] text-gray-500 mb-8 leading-relaxed px-4">
                    Apakah Anda yakin ingin keluar? Anda harus login kembali untuk mengakses panel admin.
                </p>

                <form action="{{ route('admin.logout') }}" method="POST" class="flex flex-col gap-3">
                    @csrf
                    <button type="submit" class="w-full py-3 text-[14px] font-bold text-white bg-[#dc2626] hover:bg-[#b91c1c] rounded-xl transition-all shadow-[0_4px_12px_-4px_rgba(220,38,38,0.5)]">
                        Ya, Keluar
                    </button>
                    <button type="button" @click="showLogout = false" class="w-full py-3 text-[14px] font-bold text-gray-500 hover:text-gray-800 transition-colors bg-transparent border-none">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // CSRF token for AJAX (Vanilla JS)
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (typeof jQuery !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        }

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

        // Page navigation transition disabled.
    </script>

    @stack('scripts')
</body>
</html>
