@extends('admin.layouts.app')

@section('title', 'Users')
@section('page_title', 'Users')
@section('page_description', 'Manage registered users')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Name</th>
                <th class="text-left px-4 py-3">Email</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($users ?? []) as $user)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $user->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $user->email ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ ($user->is_active ?? false) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ($user->is_active ?? false) ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('admin.users.activity', $user) }}" class="text-blue-600">Activity</a>
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-amber-600">Toggle Status</button>
                        </form>
                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.users.destroy', $user) }}', title: 'Hapus User', type: 'user', name: {{ json_encode($user->name) }} })" class="text-red-600">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (isset($users) && method_exists($users, 'links'))
    <div class="mt-4">{{ $users->links() }}</div>
@endif
@endsection
