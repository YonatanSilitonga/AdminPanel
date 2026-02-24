@extends('admin.layouts.app')

@section('title', 'Analytics')
@section('page_title', 'Analytics Dashboard')
@section('page_description', 'High-level analytics overview')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-dark mb-4">Summary</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 border rounded-lg">
            <p class="text-sm text-gray-500">Total Views</p>
            <p class="text-xl font-bold">{{ $summary['total_views'] ?? 0 }}</p>
        </div>
        <div class="p-4 border rounded-lg">
            <p class="text-sm text-gray-500">Total Searches</p>
            <p class="text-xl font-bold">{{ $summary['total_searches'] ?? 0 }}</p>
        </div>
        <div class="p-4 border rounded-lg">
            <p class="text-sm text-gray-500">Active Users</p>
            <p class="text-xl font-bold">{{ $summary['active_users'] ?? 0 }}</p>
        </div>
    </div>
</div>
@endsection
