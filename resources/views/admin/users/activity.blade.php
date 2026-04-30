@extends('admin.layouts.app')

@section('title', 'Aktivitas Pengguna')
@section('page_title', 'Detail Aktivitas')
@section('page_description', 'Riwayat interaksi dan aktivitas log pengguna')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <a href="{{ route('admin.users.index') }}" class="hover:text-sidebar transition-colors">Manajemen Pengguna</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Aktivitas</span>
</nav>
@endsection

@section('content')
@php
    $name = $user->name ?? 'User Name';
    $initials = collect(explode(' ', $name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('');
@endphp

<div class="space-y-8">
    {{-- User Info Header Card --}}
    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 flex flex-wrap items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 bg-sidebar/5 rounded-[1.5rem] flex items-center justify-center border border-sidebar/10 shadow-inner">
                <span class="text-2xl font-black text-sidebar tracking-tighter">{{ $initials }}</span>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#00A884] bg-[#E6F6F2] px-3 py-1 rounded-lg">
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="text-sm text-gray-400 font-medium">{{ $user->email }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status Akun</p>
                <span class="{{ ($user->is_active ?? false) ? 'text-[#00A884]' : 'text-gray-400' }} font-bold">
                    {{ ($user->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-50 text-gray-500 rounded-2xl font-bold hover:bg-gray-100 transition-all text-sm border border-gray-100">
                Kembali
            </a>
        </div>
    </div>

    {{-- Activity Timeline Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Log List --}}
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-lg font-bold text-gray-800 ml-2 mb-4">Riwayat Aktivitas</h3>
            
            <div class="space-y-4">
                @forelse(($activities ?? []) as $activity)
                    <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-50 flex gap-4 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-xl bg-sidebar/5 flex-shrink-0 flex items-center justify-center text-sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-[15px] font-bold text-gray-800 leading-snug mb-1">{{ $activity->description ?? 'Melakukan aktivitas pada sistem' }}</p>
                            <p class="text-xs text-gray-400 font-medium">{{ optional($activity->created_at)->format('d M Y, H:i') ?? '-' }} · {{ optional($activity->created_at)->diffForHumans() ?? '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[2rem] p-12 text-center border border-dashed border-gray-200">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-gray-400 font-medium">Belum ada catatan aktivitas untuk pengguna ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Meta Info Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Informasi Akun</h3>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-xs text-gray-400 font-bold mb-1">MEMBER SINCE</p>
                        <p class="text-sm font-bold text-gray-800">{{ $user->created_at->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold mb-1">TERAKHIR DIPERBARUI</p>
                        <p class="text-sm font-bold text-gray-800">{{ $user->updated_at->format('d F Y, H:i') }}</p>
                    </div>
                    <div class="pt-4 border-t border-gray-50">
                        <p class="text-xs text-gray-400 font-bold mb-3">TINDAKAN CEPAT</p>
                        <div class="w-full py-3 {{ ($user->is_active ?? false) ? 'bg-[#E6F6F2] text-[#00A884]' : 'bg-gray-50 text-gray-400' }} rounded-xl text-xs font-extrabold uppercase tracking-widest text-center">
                            {{ ($user->is_active ?? false) ? 'Akun Aktif' : 'Akun Nonaktif' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
