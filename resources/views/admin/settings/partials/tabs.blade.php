<!-- Settings Navigation Tabs -->
<div class="flex items-center gap-8 border-b border-gray-200 mb-8 overflow-x-auto whitespace-nowrap custom-scrollbar">
    <a href="{{ route('admin.profile') }}"
       class="pb-4 text-sm font-bold border-b-2 transition-colors {{ request()->routeIs('admin.profile') ? 'border-sidebar text-sidebar' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Profil Saya
    </a>
    <a href="{{ route('admin.settings.general') }}"
       class="pb-4 text-sm font-bold border-b-2 transition-colors {{ request()->routeIs('admin.settings.general') ? 'border-sidebar text-sidebar' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Pengaturan
    </a>
    <a href="{{ route('admin.settings.audit-logs') }}"
       class="pb-4 text-sm font-bold border-b-2 transition-colors {{ request()->routeIs('admin.settings.audit-logs*') ? 'border-sidebar text-sidebar' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Log Audit
    </a>
</div>
