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
        <button @click="$dispatch('open-edit-profile')" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-[10px] hover:bg-gray-50 hover:text-gray-900 transition-all text-[13px] shadow-sm">
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
                            <button @click="$dispatch('open-change-password')" class="text-[13px] font-bold text-[#066466] hover:text-[#055456] transition-colors">Change</button>
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

<!-- Edit Profile Modal -->
<div x-data="{ 
        show: false, 
        name: '{{ $name }}', 
        email: '{{ $email }}', 
        phone: '+62-812-3456-7890' 
     }"
     @open-edit-profile.window="show = true"
     x-show="show" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
    
    <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="show = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block w-full max-w-[500px] text-left align-middle transition-all transform bg-white shadow-2xl rounded-[1.5rem] sm:my-8 text-gray-800 relative z-10">
            
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-[22px] h-[22px] text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <h3 class="text-[18px] font-bold text-gray-900">Edit Profil Admin</h3>
                </div>
                <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-8 py-6 space-y-6">
                <!-- Avatar Upload -->
                <div class="flex flex-col items-center justify-center">
                    <div class="relative mb-3">
                        <div class="w-24 h-24 bg-black rounded-full flex items-center justify-center text-white text-4xl font-bold border-4 border-white shadow-[0_2px_10px_-2px_rgba(0,0,0,0.1)]">
                        </div>
                        <button class="absolute bottom-0 right-0 w-[30px] h-[30px] bg-[#066466] border-2 border-white rounded-full flex items-center justify-center text-white hover:bg-[#055456] transition-colors shadow-sm cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                    </div>
                    <button class="text-[#066466] text-[13px] font-bold hover:underline mb-1.5">Ubah Foto Profil</button>
                    <p class="text-[11px] text-gray-400 font-medium tracking-wide">JPG, PNG atau GIF. Maks 2MB.</p>
                </div>

                <!-- Form Fields -->
                <form id="editProfileForm" class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2 text-[12px] font-bold text-gray-700 mb-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Nama Lengkap
                        </label>
                        <input type="text" x-model="name" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#066466]/20 focus:border-[#066466] outline-none transition-all text-gray-800">
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-[12px] font-bold text-gray-700 mb-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Email
                        </label>
                        <input type="email" x-model="email" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#066466]/20 focus:border-[#066466] outline-none transition-all text-gray-800">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-2 text-[12px] font-bold text-gray-700 mb-2">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Jabatan
                            </label>
                            <input type="text" value="Super Administrator" disabled class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg bg-gray-50 text-gray-500 outline-none cursor-not-allowed">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-[12px] font-bold text-gray-700 mb-2">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Nomor HP
                            </label>
                            <input type="text" x-model="phone" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#066466]/20 focus:border-[#066466] outline-none transition-all text-gray-800">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-8 py-5 border-t border-gray-100 bg-white flex items-center justify-end gap-4 rounded-b-[1.5rem]">
                <button type="button" @click="show = false" class="px-2 py-2.5 text-[14px] font-bold text-gray-600 hover:text-gray-900 transition-colors">
                    Batal
                </button>
                <button type="button" @click="show = false" class="flex items-center gap-2.5 px-6 py-2.5 text-[14px] font-bold text-white bg-[#066466] hover:bg-[#055456] rounded-lg transition-all shadow-[0_4px_12px_-4px_rgba(6,100,102,0.4)]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div x-data="{ 
        show: {{ $errors->hasAny(['current_password', 'password']) ? 'true' : 'false' }},
        showCurrent: false,
        showNew: false,
        showConfirm: false
     }"
     @open-change-password.window="show = true"
     x-show="show" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
    
    <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="show = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block w-full max-w-[480px] text-left align-middle transition-all transform bg-white shadow-2xl rounded-[1.5rem] sm:my-8 text-gray-800 relative z-10">
            
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-[18px] font-bold text-gray-900">Ubah Kata Sandi</h3>
                <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-8 py-6 space-y-5">
                
                <!-- Info Alert -->
                <div class="flex p-4 text-[13px] text-gray-700 bg-[#fff6ed] border-l-[3px] border-[#f97316] rounded-r-lg" role="alert">
                    <div class="font-medium leading-relaxed">
                        Gunakan minimal 8 karakter dengan kombinasi huruf dan angka untuk keamanan akun Anda.
                    </div>
                </div>

                <!-- Form Fields -->
                <form action="{{ route('admin.profile.password.update') }}" method="POST" id="changePasswordForm" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <!-- Current Password -->
                    <div>
                        <label class="block text-[12px] font-bold text-gray-700 mb-2">
                            Kata Sandi Saat Ini
                        </label>
                        <div class="relative">
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" placeholder="Masukkan kata sandi saat ini" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#066466]/20 focus:border-[#066466] outline-none transition-all placeholder:text-gray-400 text-gray-800">
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <!-- Eye Icon -->
                                <svg x-show="!showCurrent" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <!-- Eye off Icon -->
                                <svg x-show="showCurrent" x-cloak class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        @error('current_password')<p class="text-[11px] font-medium text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-[12px] font-bold text-gray-700 mb-2">
                            Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'" name="password" placeholder="Masukkan kata sandi baru" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#066466]/20 focus:border-[#066466] outline-none transition-all placeholder:text-gray-400 text-gray-800">
                            <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <!-- Eye off icon as default to match mockup -->
                                <svg x-show="showNew" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="!showNew" x-cloak class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        @error('password')<p class="text-[11px] font-medium text-red-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label class="block text-[12px] font-bold text-gray-700 mb-2">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi kata sandi baru" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#066466]/20 focus:border-[#066466] outline-none transition-all placeholder:text-gray-400 text-gray-800">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="showConfirm" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="!showConfirm" x-cloak class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                    </div>
                
            </div>

            <!-- Footer -->
            <div class="px-8 py-5 border-t border-gray-100 bg-white flex items-center justify-end gap-3 rounded-b-[1.5rem]">
                <button type="button" @click="show = false" class="px-5 py-2.5 text-[14px] font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg transition-colors shadow-sm">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 text-[14px] font-bold text-white bg-[#066466] hover:bg-[#055456] rounded-lg transition-all shadow-[0_4px_12px_-4px_rgba(6,100,102,0.4)]">
                    Perbarui Password
                </button>
            </div>
            </form>

        </div>
    </div>
</div>
@endsection
