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
        color: #10B981;
        border-bottom: 3px solid #10B981;
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

<!-- Unified Navigation Tabs (Simplified) -->
<div class="flex items-center gap-8 border-b border-gray-100 mb-8 px-2">
    <div class="pb-4 text-[15px] settings-tab-active">Admin Profile</div>
</div>

<div class="space-y-6">
    <!-- Profile Header Card -->
    <div class="bg-white rounded-[20px] border border-gray-100 p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]">
        <div class="flex items-center gap-6">
            <!-- Avatar -->
            <div class="relative flex-shrink-0">
                <div class="w-[88px] h-[88px] rounded-full bg-[#10B981] flex items-center justify-center text-white text-4xl font-bold shadow-lg border-4 border-white">
                    {{ $initial }}
                </div>
                <!-- Camera Button -->
                <button class="absolute bottom-0 right-0 w-[32px] h-[32px] bg-white rounded-full shadow-[0_4px_10px_rgba(0,0,0,0.15)] flex items-center justify-center text-[#10B981] hover:scale-110 transition-transform z-10">
                    <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </button>
            </div>
            <!-- Info -->
            <div>
                <h2 class="text-[24px] font-bold text-gray-900 mb-1.5 leading-tight">{{ $name }}</h2>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="px-2.5 py-1 bg-green-50 text-[#10B981] font-bold text-[11px] rounded-md uppercase tracking-wider">{{ $roleName }}</span>
                    <span class="text-gray-300 font-bold">•</span>
                    <span class="text-gray-500 font-medium text-[14px]">Toba Tourism Authority</span>
                </div>
            </div>
        </div>
        <button @click="$dispatch('open-edit-profile')" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-[10px] hover:bg-gray-50 hover:text-gray-900 transition-all text-[13px] shadow-sm">
            Edit Profile
        </button>
    </div>

    <!-- Informasi Pribadi Card (Full Width) -->
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
                    <svg class="w-[22px] h-[22px] text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
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
                        <div class="w-24 h-24 bg-[#10B981] rounded-full flex items-center justify-center text-white text-4xl font-bold border-4 border-white shadow-lg">
                            {{ $initial }}
                        </div>
                        <button class="absolute bottom-0 right-0 w-[32px] h-[32px] bg-white rounded-full shadow-[0_4px_10px_rgba(0,0,0,0.15)] flex items-center justify-center text-[#10B981] hover:scale-110 transition-transform">
                            <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                    </div>
                    <button class="text-[#10B981] text-[13px] font-bold hover:underline mb-1.5">Ubah Foto Profil</button>
                    <p class="text-[11px] text-gray-400 font-medium tracking-wide">JPG, PNG atau GIF. Maks 2MB.</p>
                </div>

                <!-- Form Fields -->
                <form id="editProfileForm" class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2 text-[12px] font-bold text-gray-700 mb-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Nama Lengkap
                        </label>
                        <input type="text" x-model="name" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#10B981]/20 focus:border-[#10B981] outline-none transition-all text-gray-800">
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-[12px] font-bold text-gray-700 mb-2">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Email
                        </label>
                        <input type="email" x-model="email" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#10B981]/20 focus:border-[#10B981] outline-none transition-all text-gray-800">
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
                            <input type="text" x-model="phone" class="w-full px-4 py-2.5 text-[14px] border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#10B981]/20 focus:border-[#10B981] outline-none transition-all text-gray-800">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-8 py-5 border-t border-gray-100 bg-white flex items-center justify-end gap-4 rounded-b-[1.5rem]">
                <button type="button" @click="show = false" class="px-2 py-2.5 text-[14px] font-bold text-gray-600 hover:text-gray-900 transition-colors">
                    Batal
                </button>
                <button type="button" @click="show = false" class="flex items-center gap-2.5 px-6 py-2.5 text-[14px] font-bold text-white bg-[#10B981] hover:bg-emerald-600 rounded-lg transition-all shadow-lg shadow-emerald-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2-2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
