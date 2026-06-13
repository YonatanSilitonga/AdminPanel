<!-- resources/views/admin/layouts/navbar.blade.php -->
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Left: Toggle Button -->
        <div class="flex items-center space-x-4 min-w-0">
            <!-- Sidebar Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-600 bg-gray-50/50 hover:bg-emerald-50 hover:text-emerald-600 border border-gray-200 hover:border-emerald-200 rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 flex-shrink-0 shadow-sm" aria-label="Toggle Sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Right: Notifications, User Menu -->
        <div class="flex items-center space-x-4 sm:space-x-6">
            <!-- Search Form -->
            <div class="hidden md:block">
                <form action="{{ route('admin.search') }}" method="GET" class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari destinasi, event, hotel..." class="w-64 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-emerald-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-500 hover:text-emerald-600 relative p-2 hover:bg-emerald-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if (($pendingNotificationsCount ?? 0) > 0)
                        <span class="absolute top-1.5 right-1.5 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform bg-red-500 rounded-full animate-pulse">
                            {{ $pendingNotificationsCount }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition 
                     @click.outside="open = false"
                     x-cloak
                     class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-50">
                        <h3 class="text-sm font-bold text-gray-800 mb-3">📢 Notifikasi</h3>
                        
                        @php
                            $pendingReviewsCount = (int) ($pendingReviews ?? 0);
                            $pendingReportsCount = (int) ($pendingReports ?? 0);
                            $totalNotifications = $pendingReviewsCount + $pendingReportsCount;
                        @endphp

                        @if($totalNotifications > 0)
                            @if($pendingReviewsCount > 0)
                                <a href="{{ route('admin.reviews.index') }}" class="block mb-2 p-3 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors">
                                    <p class="font-medium text-blue-900 text-sm">⭐ {{ $pendingReviewsCount }} Ulasan Pending</p>
                                    <p class="text-blue-600 text-[11px] mt-1">Klik untuk review</p>
                                </a>
                            @endif

                            @if($pendingReportsCount > 0)
                                <a href="{{ route('admin.reports.index') }}" class="block p-3 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                                    <p class="font-medium text-red-900 text-sm">⚠️ {{ $pendingReportsCount }} Laporan Pending</p>
                                    <p class="text-red-600 text-[11px] mt-1">Klik untuk proses</p>
                                </a>
                            @endif
                        @else
                            <div class="py-6 text-center">
                                <p class="text-gray-400 text-sm">✓ Tidak ada notifikasi baru</p>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 bg-gray-50 text-center border-t border-gray-100">
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Terakhir diperbarui: {{ now()->format('H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-3 text-gray-600 hover:text-emerald-600 p-1.5 hover:bg-gray-50 rounded-xl transition-all">
                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm overflow-hidden">
                        @if(auth('admin')->user()?->profile_photo)
                            <img src="{{ image_url(auth('admin')->user()->profile_photo) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth('admin')->user()?->name ?? '-', 0, 1)) }}
                        @endif
                    </div>
                    <div class="hidden md:flex flex-col items-start leading-tight">
                        <span class="text-sm font-bold text-gray-800">{{ \Illuminate\Support\Str::limit(auth('admin')->user()?->name ?? '-', 15) }}</span>
                        <span class="text-[10px] text-gray-400 font-medium">Administrator</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition 
                     @click.outside="open = false"
                     x-cloak
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">
                    <!-- User Info -->
                    <div class="px-4 py-4 bg-gray-50 border-b border-gray-100">
                        <p class="text-sm font-bold text-gray-800">{{ auth('admin')->user()?->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500 truncate mt-1">{{ auth('admin')->user()?->email ?? 'admin@example.com' }}</p>
                    </div>
                    
                    <!-- Menu Items -->
                    <div class="p-1.5">
                        <a href="{{ route('admin.profile') }}" class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors">
                            <span class="text-base">👤</span>
                            <span class="font-medium">Profil Saya</span>
                        </a>
                    </div>
                    
                    <!-- Logout -->
                    <div class="p-1.5 border-t border-gray-100 bg-gray-50/50">
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-logout-modal'))" class="w-full flex items-center space-x-3 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors font-bold">
                            <span class="text-base">🚪</span>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Update current time every second -->
<script>
    setInterval(function() {
        const now = new Date();
        const time = now.getHours().toString().padStart(2, '0') + ':' + 
                     now.getMinutes().toString().padStart(2, '0') + ':' + 
                     now.getSeconds().toString().padStart(2, '0');
        const el = document.getElementById('current-time');
        if (el) el.textContent = time;
    }, 1000);
</script>
