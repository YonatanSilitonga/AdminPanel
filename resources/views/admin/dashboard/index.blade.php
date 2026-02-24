@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_description', 'Overview of system activity and content')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Total Destinations</p>
        <p class="text-2xl font-bold text-dark">{{ $stats['total_destinations'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Total Events</p>
        <p class="text-2xl font-bold text-dark">{{ $stats['total_events'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Total Users</p>
        <p class="text-2xl font-bold text-dark">{{ $stats['total_users'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Pending Reviews</p>
        <p class="text-2xl font-bold text-dark">{{ $stats['pending_reviews'] ?? 0 }}</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-5 xl:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-dark">Monthly Activity</h2>
        </div>
        <canvas id="monthlyChart" height="120"></canvas>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="text-lg font-semibold text-dark mb-4">Pending Items</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Pending Reviews</span>
                <span class="text-sm font-semibold">{{ $pendingReviews ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Pending Reports</span>
                <span class="text-sm font-semibold">{{ $pendingReports ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="text-lg font-semibold text-dark mb-4">Recent Activity</h2>
        <div class="space-y-3">
            @forelse(($recentActivity ?? []) as $log)
                <div class="p-3 border border-gray-100 rounded-lg">
                    <p class="text-sm text-dark font-medium">{{ $log->action ?? '-' }}</p>
                    <p class="text-xs text-gray-600">
                        {{ optional($log->admin)->name ?? '-' }}
                        · {{ optional($log->created_at)->diffForHumans() ?? '-' }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No recent activity.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="text-lg font-semibold text-dark mb-4">Featured Destinations</h2>
        <div class="space-y-3">
            @forelse(($featuredDestinations ?? []) as $destination)
                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-dark">{{ $destination->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">Rating: {{ $destination->rating ?? '-' }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ optional($destination->created_at)->format('d M Y') ?? '-' }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No featured destinations.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    const chartData = @json($chartData ?? []);
    const labels = chartData.map(item => item.month ?? '-');
    const destinations = chartData.map(item => item.destinations ?? 0);
    const events = chartData.map(item => item.events ?? 0);
    const reviews = chartData.map(item => item.reviews ?? 0);
    const reports = chartData.map(item => item.reports ?? 0);

    const ctx = document.getElementById('monthlyChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'Destinations', data: destinations, borderColor: '#3B82F6', fill: false },
                    { label: 'Events', data: events, borderColor: '#10B981', fill: false },
                    { label: 'Reviews', data: reviews, borderColor: '#F59E0B', fill: false },
                    { label: 'Reports', data: reports, borderColor: '#EF4444', fill: false }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
</script>
@endpush
@endsection
