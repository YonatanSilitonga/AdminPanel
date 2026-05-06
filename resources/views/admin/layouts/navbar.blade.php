<!-- resources/views/admin/layouts/navbar.blade.php -->
<header class="sticky top-0 z-30" style="background: linear-gradient(135deg, #065f46, #047857, #059669); box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Left: Toggle Button -->
        <div class="flex items-center space-x-4 min-w-0">
            <!-- Sidebar Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors focus:outline-none flex-shrink-0" aria-label="Toggle Sidebar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Right: Notifications, User Menu -->
        <div class="flex items-center space-x-4 sm:space-x-6">
            <!-- Search Form -->
            <div class="hidden md:block">
                <form action="{{ route('admin.search') }}" method="GET" class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari destinasi, event, hotel..." class="w-64 px-4 py-2 bg-white/15 border border-white/20 rounded-lg text-sm text-white placeholder-white/50 focus:outline-none focus:border-white/40 focus:ring-1 focus:ring-white/30 focus:bg-white/20 transition-all">
                    <button type="submit" class="absolute right-3 top-2.5 text-white/50 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-white/80 hover:text-white relative p-2 hover:bg-white/10 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if (($pendingNotificationsCount ?? 0) > 0)
                        <span class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white transform bg-red-500 rounded-full animate-pulse">
                            {{ $pendingNotificationsCount }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition 
                     @click.outside="open = false"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 border border-gray-200">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-dark mb-3">📢 Notifikasi</h3>
                        
                        @php
                            $pendingReviewsCount = (int) ($pendingReviews ?? 0);
                            $pendingReportsCount = (int) ($pendingReports ?? 0);
                            $totalNotifications = $pendingReviewsCount + $pendingReportsCount;
                        @endphp

                        @if($totalNotifications > 0)
                            @if($pendingReviewsCount > 0)
                                <a href="{{ route('admin.reviews.index') }}" class="block mb-2 p-3 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors">
                                    <p class="font-medium text-dark">⭐ {{ $pendingReviewsCount }} Ulasan Pending</p>
                                    <p class="text-gray-600 text-xs">Klik untuk review</p>
                                </a>
                            @endif

                            @if($pendingReportsCount > 0)
                                <a href="{{ route('admin.reports.index') }}" class="block p-3 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                                    <p class="font-medium text-dark">⚠️ {{ $pendingReportsCount }} Laporan Pending</p>
                                    <p class="text-gray-600 text-xs">Klik untuk proses</p>
                                </a>
                            @endif
                        @else
                            <p class="text-center text-gray-500 text-sm py-4">✓ Tidak ada notifikasi baru</p>
                        @endif
                    </div>
                    <div class="p-3 bg-gray-50 text-center rounded-b-lg">
                        <p class="text-xs text-gray-600">Last update: {{ now()->format('H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 text-white/90 hover:text-white p-2 hover:bg-white/10 rounded-lg transition-colors">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm border border-white/20">
                        {{ strtoupper(substr(auth('admin')->user()?->name ?? '-', 0, 1)) }}
                    </div>
                    <span class="hidden md:inline text-sm font-medium text-white">{{ \Illuminate\Support\Str::limit(auth('admin')->user()?->name ?? '-', 15) }}</span>
                    <svg class="w-4 h-4 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition 
                     @click.outside="open = false"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg z-50 border border-gray-200 overflow-hidden">
                    <!-- User Info -->
                    <div class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-gray-200">
                        <p class="text-sm font-semibold text-dark">{{ auth('admin')->user()?->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-600 truncate">{{ auth('admin')->user()?->email ?? 'admin@example.com' }}</p>
                    </div>
                    
                    <!-- Menu Items -->
                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 transition-colors border-b border-gray-100">
                        👤 Profil Saya
                    </a>
                    
                    <!-- Logout -->
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-logout-modal'))" class="w-full text-left px-4 py-2.5 text-sm text-danger hover:bg-red-50 transition-colors font-medium block border-t border-gray-100 mt-1 pt-2">
                        🚪 Logout
                    </button>
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
