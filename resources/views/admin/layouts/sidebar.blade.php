<!-- resources/views/admin/layouts/sidebar.blade.php -->
<div 
    x-data="{ open: true }" 
    class="w-64 bg-dark text-white h-screen flex flex-col shadow-lg"
>
    <!-- Logo -->
    <div class="px-6 py-4 border-b border-gray-700">
        <h2 class="text-2xl font-bold">
            <span class="text-primary">Smart</span>Tourism
        </h2>
        <p class="text-xs text-gray-400 mt-1">Admin Dashboard</p>
    </div>

    <!-- Admin Info -->
    <div class="px-6 py-4 border-b border-gray-700">
        <p class="text-sm font-semibold">{{ auth('admin')->user()?->name ?? '-' }}</p>
        <p class="text-xs text-gray-400 text-uppercase">
            {{ optional(optional(auth('admin')->user())->role)->name ?? '-' }}
        </p>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                <path d="M3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                <path d="M14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Destinations (Admin Only) -->
        @if (optional(auth('admin')->user())->hasAnyRole('admin', 'super_admin'))
            <a href="{{ route('admin.destinations.index') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.destinations.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06m0 0L4.5 17h10l-.74-4.435m0 0a1 1 0 01.54-1.06l.74-4.435A1 1 0 0015.847 3H18a1 1 0 011 1v2a1 1 0 01-1 1v7.5a2.5 2.5 0 01-2.5 2.5h-3a2.5 2.5 0 01-2.5-2.5V9a1 1 0 01-1-1V4a1 1 0 011-1z"></path>
                </svg>
                <span>Destinations</span>
            </a>
        @endif

        <!-- Events (Admin Only) -->
        @if (optional(auth('admin')->user())->hasAnyRole('admin', 'super_admin'))
            <a href="{{ route('admin.events.index') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.events.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
                <span>Events</span>
            </a>
        @endif

        <!-- Reviews (Admin & Moderator) -->
        @if (optional(auth('admin')->user())->hasAnyRole('admin', 'moderator', 'super_admin'))
            <a href="{{ route('admin.reviews.index') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.reviews.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"></path>
                    <path d="M6.5 7a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm0 6a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" fill="white"></path>
                </svg>
                <span>Reviews</span>
                @if (optional(auth('admin')->user())->hasPermission('view_reviews'))
                    @if (($pendingReviewsCount ?? 0) > 0)
                        <span class="ml-auto bg-danger text-white text-xs px-2 py-1 rounded-full">{{ $pendingReviewsCount }}</span>
                    @endif
                @endif
            </a>
        @endif

        <!-- Reports (Admin & Moderator) -->
        @if (optional(auth('admin')->user())->hasAnyRole('admin', 'moderator', 'super_admin'))
            <a href="{{ route('admin.reports.index') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.reports.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"></path>
                </svg>
                <span>Reports</span>
                @if (($pendingReportsCount ?? 0) > 0)
                    <span class="ml-auto bg-danger text-white text-xs px-2 py-1 rounded-full">{{ $pendingReportsCount }}</span>
                @endif
            </a>
        @endif

        <!-- Users (Admin Only) -->
        @if (optional(auth('admin')->user())->hasAnyRole('admin', 'super_admin'))
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 12a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                </svg>
                <span>Users</span>
            </a>
        @endif

        <!-- Logs (Admin Only) -->
        @if (optional(auth('admin')->user())->hasAnyRole('admin', 'super_admin'))
            <a href="{{ route('admin.recommendations.index') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.recommendations.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5.5 13a3 3 0 01-.369-5.98 5 5 0 119.753 1H15a2 2 0 010 4h-4l-2.835-2.828A5 5 0 005.5 13z"></path>
                </svg>
                <span>Recommendations</span>
            </a>
        @endif

        <a href="{{ route('admin.chatbot-logs.index') }}" 
           class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.chatbot-logs.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"></path>
            </svg>
            <span>Chatbot Logs</span>
        </a>

        <!-- Analytics (Admin Only) -->
        @if (optional(auth('admin')->user())->hasAnyRole('admin', 'super_admin'))
            <a href="{{ route('admin.analytics.dashboard') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.analytics.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                </svg>
                <span>Analytics</span>
            </a>
        @endif

        <!-- Settings (Super Admin Only) -->
        @if (optional(auth('admin')->user())->isSuperAdmin())
            <a href="{{ route('admin.settings.general') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.settings.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                </svg>
                <span>Settings</span>
            </a>
        @endif
    </nav>

    <!-- User Menu & Logout -->
    <div class="px-6 py-4 border-t border-gray-700 space-y-2">
        <a href="{{ route('admin.profile') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-700 transition">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
            </svg>
            <span>Profile</span>
        </a>

        <form action="{{ route('admin.logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-red-600 hover:text-white transition">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>
