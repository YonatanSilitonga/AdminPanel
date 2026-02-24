@extends('admin.layouts.app')

@section('title', 'User Activity')
@section('page_title', 'User Activity')
@section('page_description', 'User profile and activity logs')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-dark">User Info</h3>
        <p class="text-sm text-gray-600">{{ $user->name ?? '-' }} · {{ $user->email ?? '-' }}</p>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-dark mb-3">Activity Logs</h3>
        <div class="space-y-3">
            @forelse(($activities ?? []) as $activity)
                <div class="p-3 border border-gray-100 rounded-lg">
                    <p class="text-sm text-dark">{{ $activity->description ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ optional($activity->created_at)->diffForHumans() ?? '-' }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No activity logs.</p>
            @endforelse
        </div>
    </div>

    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded-lg">Back</a>
</div>
@endsection
