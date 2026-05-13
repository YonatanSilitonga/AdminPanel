@extends('admin.layouts.app')

@section('title', 'Recommendation Logs')
@section('page_title', 'Recommendation Log')
@section('page_description', 'Monitor dan analisis rekomendasi destinasi')

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-sidebar transition-colors">Home</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Monitoring</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Fitur AI dan Cerdas</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Recommendation Log</span>
</nav>
@endsection

@section('content')

{{-- Dashboard Stats Row --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    {{-- Hari Ini --}}
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium">HARI INI</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($todayLogs) }}</p>
            </div>
            <span class="text-green-500 text-sm font-semibold">↑ 12%</span>
        </div>
    </div>

    {{-- Minggu Ini --}}
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium">MINGGU INI</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($weekLogs) }}</p>
            </div>
            <span class="text-green-500 text-sm font-semibold">↑ 8%</span>
        </div>
    </div>

    {{-- Bulan Ini --}}
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium">BULAN INI</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($monthLogs) }}</p>
            </div>
            <span class="text-red-500 text-sm font-semibold">↓ 3.2%</span>
        </div>
    </div>

    {{-- Rata-rata Durasi --}}
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium">RATA-RATA DURASI</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($avgDuration, 1) }} Hari</p>
            </div>
        </div>
    </div>
</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-3 gap-6 mb-6">

    {{-- LEFT: Chart & Stats --}}
    <div class="col-span-2">
        {{-- Rekomendasi Terpopuler --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-gray-800">Destinasi Terpopuler</h3>
                    <p class="text-xs text-gray-400 mt-1">Top 5 destinasi yang paling banyak direkomendasikan</p>
                </div>
                <span class="inline-block bg-teal-100 text-teal-700 px-3 py-1 rounded-full text-xs font-semibold">REKOMENDASI TERPOPULER</span>
            </div>

            {{-- Featured Destination Card --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg overflow-hidden mb-6 text-white">
                <div class="p-6">
                    @if($popularDestinations->first())
                        <h4 class="text-xl font-bold mb-2">{{ $popularDestinations->first()->destination?->name ?? 'N/A' }}</h4>
                        <p class="text-sm text-blue-100 mb-4">
                            {{ $popularDestinations->first()->destination?->description ?? 'Destinasi populer dari sistem rekomendasi' }}
                        </p>
                        <p class="text-sm">
                            <strong>{{ $popularDestinations->first()->count }}</strong> rekomendasi
                        </p>
                    @endif
                </div>
            </div>

            {{-- Distribution Chart --}}
            <div class="mt-6">
                <p class="text-sm font-semibold text-gray-700 mb-4">Distribusi Durasi Trip</p>
                <div class="space-y-3">
                    @foreach($distributionData as $label => $count)
                        @php
                            $total = array_sum($distributionData);
                            $percent = $total > 0 ? ($count / $total) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ $label }}</span>
                                <span class="font-semibold text-gray-800">{{ $count }} ({{ round($percent) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Preferences Card --}}
    <div>
        {{-- Preferensi Populer --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-800 mb-1">Preferensi Populer</h3>
            <p class="text-xs text-gray-400 mb-6">Kategori pilihan pengunjung</p>

            <div class="space-y-4">
                @foreach($userPreferences as $preference => $count)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $preference }}</span>
                            <span class="font-semibold text-gray-800">{{ $count }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $count }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Trip Distribution --}}
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-bold text-gray-800 mb-4">Distribusi Durasi Trip</h4>
                <svg width="160" height="120" viewBox="0 0 160 120" class="mx-auto">
                    {{-- Pie Chart --}}
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#e5e7eb" stroke-width="8" stroke-dasharray="65 314" stroke-dashoffset="0"></circle>
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#0f766e" stroke-width="8" stroke-dasharray="78 314" stroke-dashoffset="-65"></circle>
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#99f6e4" stroke-width="8" stroke-dasharray="171 314" stroke-dashoffset="-143"></circle>

                    {{-- Legend --}}
                    <text x="120" y="30" font-size="10" fill="#666">1-3 Hari (20%)</text>
                    <text x="120" y="50" font-size="10" fill="#666">4-7 Hari (25%)</text>
                    <text x="120" y="70" font-size="10" fill="#666">8+ Hari (55%)</text>
                </svg>
            </div>
        </div>
    </div>

</div>

{{-- Riwayat Trip Planner Table --}}
<div class="bg-white rounded-lg shadow">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <div>
            <h3 class="font-bold text-gray-800">Riwayat Trip Planner</h3>
            <p class="text-xs text-gray-400 mt-1">Menampilkan {{ $logs->count() }} dari {{ $logs->total() }} records</p>
        </div>
        <a href="{{ route('admin.recommendations.export') }}" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700 transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export CSV
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">TRIP ID</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">DURASI PERJALANAN</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">JML DESTINASI</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">PREFERENSI</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">DIBUAT TANGGAL</th>
                    <th class="text-center px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($logs as $index => $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs font-semibold text-teal-600">#TRP-2024-{{ str_pad($logs->firstItem() + $index, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">
                                {{ round($log->recommendation_score) }} Hari
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">5 Trik</td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600">Alam & Budaya</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.recommendations.show', $log->_id) }}" class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg transition-colors inline-block" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data rekomendasi ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    @endif
</div>

{{-- Detail Modal --}}
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-96 overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg text-gray-800">Detail Rencana Trip</h3>
                <p class="text-sm text-gray-500 mt-1" id="modalTripId"></p>
            </div>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 space-y-4">
            {{-- Destination Info --}}
            <div class="bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg p-4">
                <h4 class="font-bold text-lg mb-2" id="modalDestination">Pantai Bulbul</h4>
                <p class="text-sm text-teal-100">Pantai berpasir putih di tepiaan Danau Toba yang menjadi favorit traveler</p>
                <p class="text-sm font-semibold mt-3">65% pengunjung dalam perencanaan trip mereka minggu ini</p>
            </div>

            {{-- Itinerary --}}
            <div class="pt-4">
                <p class="text-xs font-bold text-gray-500 uppercase mb-3">Rencana Perjalanan (Itinerary)</p>
                <div class="space-y-3">
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                            <div class="w-0.5 h-12 bg-gray-300 my-1"></div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Hari Pertama: Wisata Alam</p>
                            <p class="text-sm text-gray-500">Mulai dari Pantai Bulbul, lalu ke Taman Goa</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">2</div>
                            <div class="w-0.5 h-12 bg-gray-300 my-1"></div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Hari Kedua: Eksplorasi Budaya</p>
                            <p class="text-sm text-gray-500">Museum TB Silalahi Center, Danau Toba Viewing</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">3</div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Hari Ketiga: Relaksasi</p>
                            <p class="text-sm text-gray-500">Pantai Paradise, Kolam renang alami</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-200">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Durasi</p>
                    <p class="text-2xl font-bold text-teal-600" id="modalDuration">3 Hari</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Tanggal Dibuat</p>
                    <p class="text-sm font-semibold text-gray-800" id="modalDate">24 Mar 2024, 16:20</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 p-6 text-right">
            <button onclick="closeDetailModal()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors mr-2">Tutup</button>
            <button class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">Lihat Detail</button>
        </div>
    </div>
</div>

<script>
function openDetailModal(id, destination, duration, date) {
    document.getElementById('modalTripId').textContent = '#TRP-2024-' + String(id).padStart(3, '0');
    document.getElementById('modalDestination').textContent = destination;
    document.getElementById('modalDuration').textContent = duration + ' Hari';
    document.getElementById('modalDate').textContent = date;
    document.getElementById('detailModal').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

// Close modal on background click
document.getElementById('detailModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'detailModal') closeDetailModal();
});
</script>

@endsection
