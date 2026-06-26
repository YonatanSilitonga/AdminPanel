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
            @php
                // Filter in-app notification counts based on admin notification settings
                $notifyReview  = app_setting('notify_new_review', true);
                $notifyReport  = app_setting('notify_new_report', true);
                $bellReviews   = $notifyReview  ? (int)($pendingReviews  ?? 0) : 0;
                $bellReports   = $notifyReport  ? (int)($pendingReports  ?? 0) : 0;
                $bellTotal     = $bellReviews + $bellReports;
            @endphp
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-500 hover:text-emerald-600 relative p-2 hover:bg-emerald-50 rounded-lg transition-colors" title="Notifikasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if ($bellTotal > 0)
                        <span class="absolute top-1.5 right-1.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full animate-pulse">
                            {{ $bellTotal > 99 ? '99+' : $bellTotal }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown -->
                <div x-show="open"
                     x-transition
                     @click.outside="open = false"
                     x-cloak
                     class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">

                    <!-- Header -->
                    <div class="px-4 py-3 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800">📢 Notifikasi</h3>
                        @if ($bellTotal > 0)
                            <span class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">{{ $bellTotal }} baru</span>
                        @endif
                    </div>

                    <!-- Items -->
                    <div class="p-3 space-y-2">
                        @if($bellTotal > 0)
                            @if($bellReviews > 0)
                                <a href="{{ route('admin.reviews.index') }}" class="flex items-start gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-200/60 transition-colors group">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-blue-900 text-sm">{{ $bellReviews }} Ulasan Menunggu</p>
                                        <p class="text-blue-600 text-[11px] mt-0.5">Perlu ditinjau & disetujui</p>
                                    </div>
                                </a>
                            @endif

                            @if($bellReports > 0)
                                <a href="{{ route('admin.reports.index') }}" class="flex items-start gap-3 p-3 bg-red-50 hover:bg-red-100 rounded-xl border border-red-200/60 transition-colors group">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-red-200 transition-colors">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-red-900 text-sm">{{ $bellReports }} Laporan Masuk</p>
                                        <p class="text-red-600 text-[11px] mt-0.5">Perlu diproses segera</p>
                                    </div>
                                </a>
                            @endif
                        @else
                            <div class="py-8 text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Semua beres!</p>
                                <p class="text-gray-400 text-xs mt-1">Tidak ada notifikasi baru</p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="px-4 py-2.5 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-[10px] text-gray-400">Diperbarui: {{ now()->format('H:i') }}</p>
                        @if(!$notifyReview || !$notifyReport)
                            <a href="{{ route('admin.settings.general') }}" class="text-[10px] text-sidebar font-semibold hover:underline">Atur notifikasi →</a>
                        @endif
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
                    <div class="p-1.5 font-sans">
                        <a href="{{ route('admin.profile') }}" class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="font-medium">Profil Saya</span>
                        </a>
                    </div>
                    
                    <!-- Logout -->
                    <div class="p-1.5 border-t border-gray-100 bg-gray-50/50 font-sans">
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-logout-modal'))" class="w-full flex items-center space-x-3 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors font-bold">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
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
