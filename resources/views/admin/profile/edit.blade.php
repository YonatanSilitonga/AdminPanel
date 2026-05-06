@extends('admin.layouts.app')

@section('title', 'Profil Admin')
@section('page_title', 'Profil Saya')
@section('page_description', 'Kelola informasi profil, foto, dan kata sandi Anda.')

@section('content')
<style>
    /* Hide the default empty page title container */
    .mb-5:has(h1:empty) { display: none !important; }
    
    .settings-tab-active {
        color: #6349A5;
        border-bottom: 3px solid #6349A5;
        font-weight: 700;
    }
</style>

<!-- Breadcrumb Area -->
<div class="flex items-center gap-2 text-[14px] text-gray-500 mb-6">
    <span>Pengaturan</span>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    <span class="font-bold text-gray-900">Profil Saya</span>
</div>

<!-- Unified Navigation Tabs -->
<div class="flex items-center gap-8 border-b border-gray-100 mb-8 px-2 overflow-x-auto whitespace-nowrap custom-scrollbar">
    <a href="{{ route('admin.profile') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.profile') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Profil Saya</a>
    <a href="{{ route('admin.settings.general') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.general') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Pengaturan Umum</a>
    <a href="{{ route('admin.settings.api-keys') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.api-keys') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">API & Integrasi</a>
    <a href="{{ route('admin.settings.ai-config') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.ai-config') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Konfigurasi AI</a>
    <a href="{{ route('admin.settings.audit-logs') }}" class="pb-4 text-[15px] {{ request()->routeIs('admin.settings.audit-logs') ? 'settings-tab-active' : 'font-medium text-gray-400 hover:text-gray-700 transition-colors' }}">Log Audit</a>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left: Profile Info & Photo -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 flex flex-col items-center text-center">
                <!-- Profile Photo with Upload -->
                <div class="relative group" x-data="{ 
                    preview: '{{ $admin->profile_photo ? image_url($admin->profile_photo) : '' }}',
                    handleFile(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => this.preview = e.target.result;
                            reader.readAsDataURL(file);
                        }
                    }
                }">
                    <div class="w-32 h-32 bg-[#10B981] rounded-full flex items-center justify-center text-white font-bold text-4xl shadow-xl overflow-hidden border-4 border-white">
                        <template x-if="preview">
                            <img :src="preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!preview">
                            <span>{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                        </template>
                    </div>
                    
                    <label for="profile_photo" class="absolute bottom-0 right-0 w-10 h-10 bg-white rounded-full shadow-[0_4px_12px_rgba(0,0,0,0.15)] flex items-center justify-center cursor-pointer hover:bg-gray-50 transition-all group-hover:scale-110 z-10">
                        <svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </label>
                </div>

                <h3 class="text-2xl font-bold text-dark mt-6 mb-1">{{ $admin->name }}</h3>
                <p class="text-gray-500">{{ $admin->email }}</p>
                
                <div class="mt-5 inline-flex items-center px-4 py-1.5 rounded-full bg-green-50 text-green-600 text-sm font-semibold">
                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2.5"></span>
                    Akif
                </div>
            </div>

            <div class="p-8 bg-gray-50/50 border-t border-gray-100 space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Role</span>
                    <span class="font-bold text-dark capitalize">{{ str_replace('_', ' ', (is_object($admin->role) ? $admin->role->name : ($admin->role ?? 'Admin'))) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Terakhir Login</span>
                    <span class="font-bold text-dark">{{ $admin->last_login_at ? \Carbon\Carbon::parse($admin->last_login_at)->diffForHumans() : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 p-6 rounded-[2rem] border border-blue-100">
            <h4 class="font-bold text-blue-900 mb-2 flex items-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Informasi
            </h4>
            <p class="text-xs text-blue-700 leading-relaxed">
                Gunakan email aktif untuk menerima notifikasi sistem. Foto profil akan muncul pada log aktivitas dan laporan admin.
            </p>
        </div>
    </div>

    <!-- Right: Edit Forms -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Basic Info Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
            <h3 class="text-xl font-bold text-dark mb-8 flex items-center">
                <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Dasar
            </h3>

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Hidden Input for Photo -->
                <input type="file" id="profile_photo" name="profile_photo" class="hidden" @change="handleFile($event)">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none font-medium" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none font-medium" required>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-8 py-3.5 bg-sidebar text-white font-bold rounded-2xl hover:bg-sidebar-hover transition-all shadow-lg shadow-sidebar/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Change Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
            <h3 class="text-xl font-bold text-dark mb-8 flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Keamanan Akun
            </h3>

            <form action="{{ route('admin.profile.password.update') }}" method="POST" x-data="{ 
                showCurrent: false, 
                showNew: false, 
                showConfirm: false 
            }">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kata Sandi Saat Ini</label>
                        <div class="relative">
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none font-medium pr-12" required>
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kata Sandi Baru</label>
                            <div class="relative">
                                <input :type="showNew ? 'text' : 'password'" name="password" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none font-medium pr-12" required>
                                <button type="button" @click="showNew = !showNew" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Sandi Baru</label>
                            <div class="relative">
                                <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none font-medium pr-12" required>
                                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29m0 0l3.29 3.29"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-8 py-3.5 bg-orange-500 text-white font-bold rounded-2xl hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<style>
    [x-cloak] { display: none !important; }
</style>
