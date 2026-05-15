@extends('admin.layouts.app')

@section('title', 'Analytics')
@section('page_title', 'Analytics Dashboard')
@section('page_description', 'High-level analytics overview')

@section('content')
<div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 mb-8">
    <h2 class="text-lg font-bold text-gray-900 mb-6">Ringkasan Statistik</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-gray-50/50 border border-gray-100 rounded-[1.5rem] transition-all hover:bg-white hover:shadow-md group">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 group-hover:text-sidebar transition-colors">Total Tayangan</p>
            <p class="text-3xl font-black text-gray-900">{{ number_format($summary['total_views'] ?? 0) }}</p>
        </div>
        <div class="p-6 bg-gray-50/50 border border-gray-100 rounded-[1.5rem] transition-all hover:bg-white hover:shadow-md group">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 group-hover:text-sidebar transition-colors">Total Pencarian</p>
            <p class="text-3xl font-black text-gray-900">{{ number_format($summary['total_searches'] ?? 0) }}</p>
        </div>
        <div class="p-6 bg-gray-50/50 border border-gray-100 rounded-[1.5rem] transition-all hover:bg-white hover:shadow-md group">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 group-hover:text-sidebar transition-colors">Pengguna Aktif</p>
            <p class="text-3xl font-black text-gray-900">{{ number_format($summary['active_users'] ?? 0) }}</p>
        </div>
    </div>
</div>
@endsection
