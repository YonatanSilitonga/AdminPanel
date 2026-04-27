@extends('admin.layouts.app')

@section('title', 'Hasil Pencarian')
@section('navbar_title', 'Pencarian')
@section('page_title', 'Hasil Pencarian')
@section('page_description', 'Menampilkan hasil pencarian untuk: "' . $query . '"')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Pencarian</span>
</nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">
            @if($totalCount > 0)
                Ditemukan {{ $totalCount }} hasil untuk <span class="text-primary">"{{ $query }}"</span>
            @else
                Tidak ada hasil untuk <span class="text-danger">"{{ $query }}"</span>
            @endif
        </h2>
        <p class="text-gray-500 mt-2">Cari di destinasi, event, berita, dan user</p>
    </div>

    @if($totalCount > 0)
        <div class="grid gap-4">
            @foreach($results as $result)
                <a href="{{ $result['url'] }}" class="group bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl hover:border-primary/20 transition-all duration-300 flex items-center gap-6">
                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 group-hover:bg-primary/5 transition-all duration-300">
                        {{ $result['icon'] }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                {{ $result['type'] }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-primary transition-colors">
                            {{ $result['title'] }}
                        </h3>
                        <p class="text-gray-500 text-sm mt-1 line-clamp-2">
                            {{ $result['description'] }}
                        </p>
                    </div>
                    <div class="text-gray-300 group-hover:text-primary group-hover:translate-x-1 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-[3rem] p-12 text-center border border-gray-100 shadow-sm">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Maaf, kami tidak menemukan apa pun</h3>
            <p class="text-gray-500 max-w-sm mx-auto">
                Coba gunakan kata kunci yang lebih umum atau periksa kembali ejaan Anda.
            </p>
            <div class="mt-8">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-white font-bold rounded-2xl hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
