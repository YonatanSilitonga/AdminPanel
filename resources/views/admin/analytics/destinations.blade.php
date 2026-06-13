@extends('admin.layouts.app')

@section('title', 'Destination Analytics')
@section('page_title', 'Destination Analytics')
@section('page_description', 'Performance per destination')

@section('content')
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-50">
            <thead class="bg-white">
                <tr>
                    <th class="px-10 py-6 text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[13px] font-bold text-gray-500 uppercase tracking-wider">Destinasi</span>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Nama destinasi wisata yang terdaftar dalam sistem.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-sans">Tabel kinerja destinasi.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-10 py-6 text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[13px] font-bold text-gray-500 uppercase tracking-wider">Tayangan (Views)</span>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Total berapa kali halaman destinasi ini dibuka oleh pengguna di aplikasi mobile.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-sans">Tabel kinerja destinasi.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-10 py-6 text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[13px] font-bold text-gray-500 uppercase tracking-wider">Perencanaan (Bookings)</span>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Jumlah rencana perjalanan (trip) yang dibuat oleh pengguna yang menyertakan destinasi ini.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-sans">Tabel kinerja destinasi.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-10 py-6 text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[13px] font-bold text-gray-500 uppercase tracking-wider">Rating</span>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-[#066466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-yellow-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-sans">Rata-rata ulasan penilaian bintang yang diberikan oleh pengunjung.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-yellow-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-sans">Tabel kinerja destinasi.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse(($destinationStats ?? []) as $stat)
                    <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                        <td class="px-10 py-6 text-sm font-bold text-gray-700">{{ $stat->name ?? '-' }}</td>
                        <td class="px-10 py-6 text-sm text-gray-500 font-medium">{{ number_format($stat->views ?? 0) }}</td>
                        <td class="px-10 py-6 text-sm text-gray-500 font-medium">{{ number_format($stat->bookings ?? 0) }}</td>
                        <td class="px-10 py-6 text-sm font-bold text-amber-500">
                            @if($stat->rating)
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <span>{{ number_format((float) $stat->rating, 1) }}</span>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-10 py-14 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <p class="text-sm font-medium">Tidak ada data analitik ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
