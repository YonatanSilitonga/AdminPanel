@extends('admin.layouts.app')

@section('title', 'Profile')
@section('page_title', 'Profile')
@section('page_description', 'Manage your account')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-dark">Profile Information</h3>
        <p class="text-sm text-gray-600">{{ auth('admin')->user()?->name ?? '-' }} · {{ auth('admin')->user()?->email ?? '-' }}</p>
    </div>

    <form action="{{ route('admin.profile.password.update') }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Current Password</label>
            <input type="password" name="current_password" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('current_password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">New Password</label>
            <input type="password" name="password" class="mt-1 w-full border rounded-lg px-4 py-2">
            @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" class="mt-1 w-full border rounded-lg px-4 py-2">
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Update Password</button>
        </div>
    </form>
</div>
@endsection
