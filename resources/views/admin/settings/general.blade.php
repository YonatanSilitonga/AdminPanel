@extends('admin.layouts.app')

@section('title', 'Pengaturan')
@section('navbar_title', 'Pengaturan')
@section('page_title', 'Pengaturan')
@section('page_description', 'Konfigurasi fitur platform, tampilan, dan notifikasi sistem')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Pengaturan</span>
</nav>
@endsection

@section('content')

<!-- Settings Navigation Tabs -->
@include('admin.settings.partials.tabs')

<form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <!-- Fitur Platform & Moderasi Konten (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Fitur Platform -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm flex flex-col">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-4 rounded-t-[2rem]">
                <div class="w-11 h-11 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Fitur Platform</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Aktifkan atau nonaktifkan fitur utama</p>
                </div>
            </div>

            <div class="p-8 space-y-5 flex-1">
                <!-- Enable Reviews -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-sm font-bold text-gray-900">Sistem Ulasan</p>
                                <div class="relative group cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-normal">Mengaktifkan atau menonaktifkan fitur ulasan dan rating bintang oleh wisatawan.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                                <p class="text-slate-200 font-normal">Halaman detail destinasi wisata pada situs publik (frontend) untuk memberikan ulasan.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 font-medium">Izinkan pengguna memberikan ulasan</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_reviews" value="1" class="sr-only peer" @checked(old('enable_reviews', $settings['enable_reviews'] ?? true))>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sidebar/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                    </label>
                </div>

                <!-- Enable Reports -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-sm font-bold text-gray-900">Sistem Laporan</p>
                                <div class="relative group cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-normal">Menyediakan tombol bagi pengunjung untuk melaporkan masalah atau konten ulasan yang tidak pantas.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                                <p class="text-slate-200 font-normal">Halaman ulasan destinasi di situs publik (frontend) agar laporan dapat masuk ke menu moderasi admin.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 font-medium">Izinkan pengguna melaporkan isu</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_reports" value="1" class="sr-only peer" @checked(old('enable_reports', $settings['enable_reports'] ?? true))>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sidebar/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Moderasi Konten -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm flex flex-col">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-4 rounded-t-[2rem]">
                <div class="w-11 h-11 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Moderasi Konten</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Kontrol konten sebelum ditampilkan</p>
                </div>
            </div>

            <div class="p-8 space-y-5 flex-1">
                <!-- Moderate Reviews -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-sm font-bold text-gray-900">Moderasi Ulasan</p>
                                <div class="relative group cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-normal">Memastikan setiap ulasan baru dari wisatawan disaring dan disetujui terlebih dahulu oleh admin sebelum dipublikasikan.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                                <p class="text-slate-200 font-normal">Menu daftar ulasan di panel admin dan penayangan ulasan di halaman detail destinasi publik.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 font-medium">Ulasan butuh persetujuan admin</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="moderate_reviews" value="1" class="sr-only peer" @checked(old('moderate_reviews', $settings['moderate_reviews'] ?? false))>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sidebar/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                    </label>
                </div>

            </div>
        </div>
    </div>

    <!-- Logo & Branding -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-4 rounded-t-[2rem]">
            <div class="w-11 h-11 bg-sidebar/10 rounded-2xl flex items-center justify-center text-sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Logo & Branding</h3>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Upload logo dan favicon aplikasi</p>
            </div>
        </div>

        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Logo -->
                <div class="space-y-2">
                    <div class="flex items-center gap-1.5 mb-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest">Logo Aplikasi</label>
                        <div class="relative group cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case">
                                <div class="space-y-2 font-normal">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200">Menentukan gambar logo resmi sebagai identitas visual utama platform.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200">Bagian atas sidebar menu admin panel, halaman login, serta header dokumen cetak/invoice.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    @if(isset($settings['logo']) && $settings['logo'])
                        <div class="mb-3 p-4 bg-gray-50 rounded-xl border border-gray-100 max-w-[200px]">
                            <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo" class="h-16 object-contain">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg,image/svg+xml"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sidebar/10 file:text-sidebar hover:file:bg-sidebar/20">
                    <p class="text-xs text-gray-400 font-medium">Format: PNG, JPG, JPEG, SVG. Max: 2MB</p>
                    @error('logo')<p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Favicon -->
                <div class="space-y-2">
                    <div class="flex items-center gap-1.5 mb-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest">Favicon</label>
                        <div class="relative group cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case">
                                <div class="space-y-2 font-normal">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200">Mengunggah gambar ikon kecil untuk identitas tab peramban/browser.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200">Tab browser pengunjung dan admin di sebelah judul halaman aplikasi web.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    @if(isset($settings['favicon']) && $settings['favicon'])
                        <div class="mb-3 p-4 bg-gray-50 rounded-xl border border-gray-100 max-w-[100px]">
                            <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="Favicon" class="h-8 object-contain">
                        </div>
                    @endif
                    <input type="file" name="favicon" accept="image/png,image/jpg,image/jpeg,image/x-icon"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sidebar/10 file:text-sidebar hover:file:bg-sidebar/20">
                    <p class="text-xs text-gray-400 font-medium">Format: PNG, JPG, ICO. Max: 512KB</p>
                    @error('favicon')<p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Tema Warna & Pengaturan Interface (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Tema Warna -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm flex flex-col">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-4 rounded-t-[2rem]">
                <div class="w-11 h-11 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Tema Warna</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Kustomisasi warna utama aplikasi</p>
                </div>
            </div>

            <div class="p-8 space-y-6 flex-1">
                <!-- Primary Color -->
                <div class="space-y-2">
                    <div class="flex items-center gap-1.5 mb-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest">Warna Utama <span class="text-red-500">*</span></label>
                        <div class="relative group cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case">
                                <div class="space-y-2 font-normal">
                                    <div>
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200">Menetapkan warna aksen utama untuk elemen penarik perhatian di seluruh antarmuka.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200">Tombol aksi utama (submit), link aktif, status penting, serta hover states di seluruh halaman admin dan publik.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#3B82F6') }}" required
                            class="h-12 w-20 bg-gray-50 border border-gray-100 rounded-xl cursor-pointer">
                        <input type="text" value="{{ old('primary_color', $settings['primary_color'] ?? '#3B82F6') }}" readonly
                            class="flex-1 px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-mono font-medium text-gray-700">
                    </div>
                    @error('primary_color')<p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Secondary Color -->
                <div class="space-y-2">
                    <div class="flex items-center gap-1.5 mb-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest">Warna Sekunder <span class="text-red-500">*</span></label>
                        <div class="relative group cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case">
                                <div class="space-y-2 font-normal">
                                    <div>
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200">Menetapkan warna pendukung untuk menciptakan kontras dan variasi visual yang harmonis.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                        <p class="text-slate-200">Badge info, tombol sekunder (batal), elemen grafis kecil, serta status sukses/informasi.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#10B981') }}" required
                            class="h-12 w-20 bg-gray-50 border border-gray-100 rounded-xl cursor-pointer">
                        <input type="text" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#10B981') }}" readonly
                            class="flex-1 px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-mono font-medium text-gray-700">
                    </div>
                    @error('secondary_color')<p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Pengaturan Interface -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm flex flex-col">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-4 rounded-t-[2rem]">
                <div class="w-11 h-11 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Pengaturan Interface</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Preferensi tampilan dan bahasa</p>
                </div>
            </div>

            <div class="p-8 space-y-6 flex-1">
                <!-- Default Language -->
                <div class="space-y-2">
                    <div class="flex items-center gap-1.5 mb-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest">Bahasa Default <span class="text-red-500">*</span></label>
                        <div class="relative group cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case">
                                <div class="space-y-2 font-normal">
                                    <div>
                                        <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200">Menentukan bahasa pengantar bawaan sistem ketika pertama kali diakses oleh pengguna.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                        <p class="text-slate-200">Seluruh teks statis, label formulir, dan pesan sistem pada aplikasi web admin maupun publik.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <select name="default_language" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all cursor-pointer">
                        <option value="id" @selected(old('default_language', $settings['default_language'] ?? 'id') === 'id')>🇮🇩 Bahasa Indonesia</option>
                        <option value="en" @selected(old('default_language', $settings['default_language'] ?? '') === 'en')>🇬🇧 English</option>
                    </select>
                    @error('default_language')<p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Dark Mode -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl mt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-sm font-bold text-gray-900">Mode Gelap</p>
                                <div class="relative group cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                                <p class="text-slate-200 font-normal">Mengaktifkan skema warna gelap untuk mengurangi ketegangan mata dan menghemat daya perangkat.</p>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-800">
                                                <span class="block font-bold text-indigo-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                                <p class="text-slate-200 font-normal">Seluruh antarmuka admin panel (sidebar, background, tabel, dan formulir).</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 font-medium">Aktifkan tema gelap untuk interface</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dark_mode" value="1" class="sr-only peer" @checked(old('dark_mode', $settings['dark_mode'] ?? false))>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sidebar/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Preferensi Notifikasi -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-4 rounded-t-[2rem]">
            <div class="w-11 h-11 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Preferensi Notifikasi</h3>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Pilih notifikasi email yang ingin dikirim ke admin</p>
            </div>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Notify New Review -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-sm font-bold text-gray-900">Ulasan Baru</p>
                            <div class="relative group cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Mengirim email notifikasi kepada admin saat ada ulasan baru masuk dari wisatawan.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Digunakan Di</span>
                                            <p class="text-slate-200 font-normal">Sistem pengiriman notifikasi email admin saat ada ulasan destinasi baru yang disubmit.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">Notifikasi saat ada ulasan masuk</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="notify_new_review" value="1" class="sr-only peer" @checked(old('notify_new_review', $settings['notify_new_review'] ?? true))>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sidebar/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                </label>
            </div>

            <!-- Notify New Report -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-sm font-bold text-gray-900">Laporan Baru</p>
                            <div class="relative group cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Mengirim email peringatan kepada admin ketika ada ulasan atau masalah yang dilaporkan oleh pengguna.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-normal">Sistem pengiriman email admin saat tabel laporan mendapat baris baru dari frontend.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">Notifikasi saat ada laporan masalah baru</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="notify_new_report" value="1" class="sr-only peer" @checked(old('notify_new_report', $settings['notify_new_report'] ?? true))>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sidebar/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                </label>
            </div>

            <!-- Notify New User -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-sm font-bold text-gray-900">Pengguna Baru</p>
                            <div class="relative group cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50">
                                    <div class="space-y-2 font-normal">
                                        <div>
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200">Mengirim email pemberitahuan ke admin saat ada pengunjung baru mendaftarkan akun.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200">Sistem event listener Laravel setelah proses registrasi pengguna selesai.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">Notifikasi saat pendaftaran akun baru</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="notify_new_user" value="1" class="sr-only peer" @checked(old('notify_new_user', $settings['notify_new_user'] ?? false))>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sidebar/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                </label>
            </div>

            <!-- Notify System Error -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-sm font-bold text-gray-900">Error Sistem</p>
                            <div class="relative group cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Mengirim laporan bug, error database, atau crash aplikasi ke email admin untuk penanganan cepat.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-normal">Handler exception global Laravel untuk mengirimkan crash log penting.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">Notifikasi ketika terjadi kendala sistem</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="notify_system_error" value="1" class="sr-only peer" @checked(old('notify_system_error', $settings['notify_system_error'] ?? true))>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sidebar/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                </label>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-end gap-4">
        <a href="{{ route('admin.dashboard') }}" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-2xl font-bold hover:bg-gray-200 transition-all text-sm">
            Batal
        </a>
        <button type="submit" class="px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-90 transition-all shadow-sm text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Simpan Perubahan
        </button>
    </div>
</form>

@endsection
