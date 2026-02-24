@extends('admin.layouts.app')

@section('title', 'API Keys')
@section('page_title', 'API Keys')
@section('page_description', 'Manage external API keys')

@section('content')
<form action="{{ route('admin.settings.api-keys.update') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm font-medium text-gray-700">Maps API Key</label>
        <input type="text" name="maps_api_key" value="{{ old('maps_api_key', $settings['maps_api_key'] ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
        @error('maps_api_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">AI API Key</label>
        <input type="text" name="ai_api_key" value="{{ old('ai_api_key', $settings['ai_api_key'] ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
        @error('ai_api_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Save Keys</button>
    </div>
</form>
@endsection
