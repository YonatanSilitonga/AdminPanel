@extends('admin.layouts.app')

@section('title', 'General Settings')
@section('page_title', 'General Settings')
@section('page_description', 'Update application settings')

@section('content')
<form action="{{ route('admin.settings.general.update') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm font-medium text-gray-700">Site Name</label>
        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
        @error('site_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Support Email</label>
        <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
        @error('support_email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Save Settings</button>
    </div>
</form>
@endsection
