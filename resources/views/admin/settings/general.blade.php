@extends('admin.layouts.app')

@section('title', 'Admin Profile')

@section('page_title')
@endsection

@section('page_description')
@endsection

@section('content')
<style>
    /* Hide the default empty page title container */
    .mb-5:has(h1:empty) { display: none !important; }
    
    .settings-tab-active {
        color: #066466;
        border-bottom: 3px solid #066466;
        font-weight: 700;
    }
</style>

@php
    $admin = auth('admin')->user();
    $name = $admin?->name ?? 'Budi Santoso';
    $email = $admin?->email ?? 'superadmin@toba.id';
    $initial = strtoupper(substr($name, 0, 1));
    $roleName = optional($admin?->role)->name ?? 'Super Admin';
@endphp

<!-- Breadcrumb Area -->
<div class="flex items-center gap-2 text-[14px] text-gray-500 mb-6">
    <span>Settings</span>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    <span class="font-bold text-gray-900">Admin Profile</span>
</div>

<!-- Unified Navigation Tabs -->
<div class="flex items-center gap-8 border-b border-gray-100 mb-8 px-2 overflow-x-auto whitespace-nowrap custom-scrollbar">
    <a href="{{ route('admin.settings.general') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.general') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Admin Profile</a>
    <a href="{{ route('admin.settings.audit-logs') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.audit-logs') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Audit Log</a>
    <a href="#" class="pb-4 text-[15px] font-medium text-gray-400 hover:text-gray-700 transition-colors">Backup & Restore</a>
</div>

<div class="space-y-6">
    <!-- Profile Header Card -->
    <div class="bg-white rounded-[20px] border border-gray-100 p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]">
        <div class="flex items-center gap-6">
            <!-- Avatar -->
            <div class="relative flex-shrink-0">
                <div class="w-[88px] h-[88px] rounded-full bg-[#066466] flex items-center justify-center text-white text-4xl font-bold shadow-sm">
                    {{ $initial }}
                </div>
                <!-- Edit Avatar Button -->
                <button class="absolute bottom-0 right-0 w-[30px] h-[30px] bg-white rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:text-sidebar shadow-sm transition-colors">
                    <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
            </div>
            <!-- Info -->
            <div>
                <h2 class="text-[24px] font-bold text-gray-900 mb-1.5 leading-tight">{{ $name }}</h2>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="px-2.5 py-1 bg-[#e1f0f1] text-[#066466] font-bold text-[11px] rounded-md uppercase tracking-wider">{{ $roleName }}</span>
                    <span class="text-gray-300 font-bold">•</span>
                    <span class="text-gray-500 font-medium text-[14px]">Toba Tourism Authority</span>
                </div>
            </div>
        </div>
        <button class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-[10px] hover:bg-gray-50 hover:text-gray-900 transition-all text-[13px] shadow-sm">
            Edit Profile
        </button>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Informasi Pribadi Card -->
            <div class="bg-white rounded-[20px] border border-gray-100 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-[16px] font-bold text-gray-900">Informasi Pribadi</h3>
                    <div class="text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-7 gap-x-12">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Nama Lengkap</p>
                            <p class="text-[15px] font-semibold text-gray-800">{{ $name }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Alamat Email</p>
                            <p class="text-[15px] font-semibold text-gray-800">{{ $email }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Jabatan</p>
                            <p class="text-[15px] font-semibold text-gray-800">Head of Digital Systems</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Nomor Telepon</p>
                            <p class="text-[15px] font-semibold text-gray-800">+62 812-3456-7890</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Security Card -->
            <div class="bg-white rounded-[20px] border border-gray-100 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-[16px] font-bold text-gray-900">Security</h3>
                    <div class="text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-50">
                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                <h4 class="text-[14px] font-bold text-gray-900">Password</h4>
                            </div>
                            <button class="text-[13px] font-bold text-[#066466] hover:text-[#055456] transition-colors">Change</button>
                        </div>
                        <p class="text-[13px] text-gray-500 font-medium pl-[28px]">Last changed: 3 months ago</p>
                    </div>

                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <h4 class="text-[14px] font-bold text-gray-900">Two-factor (2FA)</h4>
                            </div>
                            <button type="button" class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-200 transition-colors duration-200 ease-in-out focus:outline-none">
                                <span class="translate-x-0 pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </div>
                        <p class="text-[13px] text-gray-500 font-medium leading-relaxed mb-3">
                            Add an extra layer of security to your account by requiring more than just a password.
                        </p>
                        <span class="text-[11px] font-bold text-[#EF4444] uppercase tracking-[0.05em]">Currently Disabled</span>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="bg-[#0f172a] rounded-[20px] p-8 text-white relative overflow-hidden shadow-xl">
                <div class="relative z-10">
                    <h3 class="text-[18px] font-bold mb-2">Need help?</h3>
                    <p class="text-[13px] text-gray-300 font-medium leading-relaxed mb-6">
                        Contact our technical support if you have issues with your account settings.
                    </p>
                    <button class="w-full py-3 bg-[#066466] hover:bg-[#055456] text-white text-[13px] font-bold rounded-xl transition-all shadow-lg shadow-[#066466]/40">
                        Contact Support
                    </button>
                </div>
                <!-- Abstract Decorations -->
                <div class="absolute -bottom-10 -right-10 w-[180px] h-[180px] border-[10px] border-white/5 rounded-full pointer-events-none"></div>
                <div class="absolute top-8 right-8 w-3 h-3 bg-white/10 rounded-full blur-[1px] pointer-events-none"></div>
            </div>
        </div>
    </div>
</div>
@endsection
