<!-- resources/views/admin/layouts/navbar.blade.php -->
<header class="bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Left: Breadcrumb or Title -->
        <div class="flex items-center space-x-4">
            <!-- Sidebar Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-500 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors focus:outline-none" aria-label="Toggle Sidebar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h2 class="text-lg font-semibold text-dark">@yield('navbar_title', 'Dashboard')</h2>
        </div>

        <!-- Right: Notifications, User Menu -->
        <div class="flex items-center space-x-6">
            <!-- Current Date/Time -->
            <div class="text-sm text-gray-600">
                <span id="current-time" class="font-mono">{{ now()->format('H:i:s') }}</span>
            </div>

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-600 hover:text-primary relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if (($pendingNotificationsCount ?? 0) > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-danger rounded-full">
                            {{ $pendingNotificationsCount }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition 
                     @click.outside="open = false"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50">
                    <div class="p-4">
                        <h3 class="text-sm font-bold text-dark mb-3">Notifications</h3>
                        
                        @php
                            $pendingReviewsCount = (int) ($pendingReviews ?? 0);
                            $pendingReportsCount = (int) ($pendingReports ?? 0);
                            $totalNotifications = $pendingReviewsCount + $pendingReportsCount;
                        @endphp

                        @if($totalNotifications > 0)
                            @if($pendingReviewsCount > 0)
                                <div class="mb-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="font-medium text-dark">{{ $pendingReviewsCount }} Pending Review{{ $pendingReviewsCount !== 1 ? 's' : '' }}</p>
                                    <p class="text-gray-600 text-xs">Click to review</p>
                                </div>
                            @endif

                            @if($pendingReportsCount > 0)
                                <div class="mb-2 p-3 bg-red-50 rounded-lg border border-red-200">
                                    <p class="font-medium text-dark">{{ $pendingReportsCount }} Pending Report{{ $pendingReportsCount !== 1 ? 's' : '' }}</p>
                                    <p class="text-gray-600 text-xs">Click to resolve</p>
                                </div>
                            @endif
                        @else
                            <p class="text-center text-gray-500 text-sm py-4">No notifications</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-3 text-gray-700 hover:text-primary">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr(auth('admin')->user()?->name ?? '-', 0, 1)) }}
                    </div>
                    <span class="hidden md:inline text-sm font-medium">{{ \Illuminate\Support\Str::limit(auth('admin')->user()?->name ?? '-', 15) }}</span>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition 
                     @click.outside="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50">
                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                        Profile
                    </a>
                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Change Password
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-danger hover:bg-red-50 rounded-b-lg">
                            Logout
                        </button>
                    </form>
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
        document.getElementById('current-time').textContent = time;
    }, 1000);
</script>
