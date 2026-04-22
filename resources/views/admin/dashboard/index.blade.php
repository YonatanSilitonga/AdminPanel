@extends('admin.layouts.app')

@section('title', 'Dashboard Overview')
@section('navbar_title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
<!-- Header Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Destinasi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Destinasi</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_destinations'] ?? 0 }}</p>
            </div>
            <div class="p-3 bg-purple-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-purple-600 font-medium">Total Destinasi</span>
        </div>
    </div>

    <!-- Card 2: Event Aktif -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Event</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_events'] ?? 0 }}</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">Total Event</span>
        </div>
    </div>

    <!-- Card 3: Pengguna -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Pengguna</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_users'] ?? 0) }}</p>
            </div>
            <div class="p-3 bg-orange-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-orange-500 font-medium">Total Pengguna</span>
        </div>
    </div>

    <!-- Card 4: Laporan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Laporan Pending</p>
                <p class="text-3xl font-bold text-gray-900">{{ $pendingReports ?? 0 }}</p>
            </div>
            <div class="p-3 bg-red-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-red-500 font-medium">Belum Ditangani</span>
        </div>
    </div>
</div>


<!-- Middle Section: Chart and Activity Timeline -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <!-- Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 xl:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Aktivitas Bulanan</h2>
            <div class="flex space-x-4 text-sm">
                <div class="flex items-center">
                    <span class="w-3 h-0.5 bg-purple-600 mr-2"></span>
                    <span class="text-gray-500">Destinasi</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-0.5 bg-green-600 mr-2"></span>
                    <span class="text-gray-500">Event</span>
                </div>
            </div>
        </div>
        <div class="relative h-64 md:h-72 w-full mt-6">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Aktivitas Terbaru</h2>
        <div class="relative pl-6 border-l-2 border-gray-100 space-y-6">
        @forelse(($recentActivity ?? []) as $index => $log)
            <div class="relative">
                <div class="absolute -left-[33px] bg-white p-1 rounded-full">
                    @php
                        $colors = ['bg-green-600', 'bg-red-500', 'bg-yellow-500', 'bg-purple-600', 'bg-blue-500'];
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div class="w-2.5 h-2.5 {{ $color }} rounded-full"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $log->action ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ optional($log->admin)->name ?? '-' }}
                        · {{ optional($log->created_at)->diffForHumans() ?? '-' }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">No recent activity.</p>
        @endforelse
        </div>
    </div>
</div>

<!-- Bottom Section: 3 Columns -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8 mt-2">
    <!-- Top Destinasi Dikunjungi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <h2 class="text-lg font-bold text-gray-900">Featured Destinasi</h2>
        </div>
        <div class="space-y-4">
        @forelse(($featuredDestinations ?? []) as $index => $destination)
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <span class="w-6 h-6 rounded-full {{ $index < 3 ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center text-xs font-bold mr-3">{{ $index + 1 }}</span>
                    <span class="text-sm text-gray-700 truncate w-32 md:w-48">{{ $destination->name ?? '-' }}</span>
                </div>
                <span class="text-sm font-semibold text-gray-500">Rating: {{ $destination->rating ?? '-' }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500">No featured destinations.</p>
        @endforelse
        </div>
    </div>

    <!-- Top 5 Pencarian Hari Ini -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Top 5 Pencarian Hari Ini</h2>
        <div class="space-y-3">
            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                <span class="text-gray-400 text-sm w-6 font-medium">1.</span>
                <span class="text-sm text-gray-700 font-medium ml-2">"pantai"</span>
            </div>
            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                <span class="text-gray-400 text-sm w-6 font-medium">2.</span>
                <span class="text-sm text-gray-700 font-medium ml-2">"hotel balige"</span>
            </div>
            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                <span class="text-gray-400 text-sm w-6 font-medium">3.</span>
                <span class="text-sm text-gray-700 font-medium ml-2">"festival toba"</span>
            </div>
            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                <span class="text-gray-400 text-sm w-6 font-medium">4.</span>
                <span class="text-sm text-gray-700 font-medium ml-2">"wisata alam"</span>
            </div>
            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                <span class="text-gray-400 text-sm w-6 font-medium">5.</span>
                <span class="text-sm text-gray-700 font-medium ml-2">"kuliner batak"</span>
            </div>
        </div>
    </div>

    <!-- Trip Dibuat -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <h2 class="text-lg font-bold text-gray-900">Trip Dibuat</h2>
        </div>
        <div class="space-y-6 mt-4">
            <!-- Hari ini -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500">Hari ini</span>
                    <span class="text-sm font-bold text-gray-900">18</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-purple-600 h-2.5 rounded-full" style="width: 15%"></div>
                </div>
            </div>
            <!-- Minggu ini -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500">Minggu ini</span>
                    <span class="text-sm font-bold text-gray-900">94</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: 45%"></div>
                </div>
            </div>
            <!-- Bulan ini -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500">Bulan ini</span>
                    <span class="text-sm font-bold text-gray-900">312</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 85%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartData = @json($chartData ?? []);
        const labels = chartData.map(item => item.month ?? '-');
        const destinations = chartData.map(item => item.destinations ?? 0);
        const events = chartData.map(item => item.events ?? 0);

        const ctx = document.getElementById('monthlyChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.length ? labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Destinasi',
                            data: destinations.length ? destinations : [12, 35, 15, 60, 10, 85],
                            borderColor: '#7e22ce', // purple-700
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            fill: false
                        },
                        {
                            label: 'Event',
                            data: events.length ? events : [5, 18, 15, 35, 15, 60],
                            borderColor: '#16a34a', // green-600
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            display: false,
                            min: 0,
                        },
                        x: {
                            grid: {
                                color: '#f3f4f6', // gray-100
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#9ca3af', // gray-400
                                font: { size: 12 }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
