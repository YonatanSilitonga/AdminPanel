@extends('admin.layouts.app')

@section('title', 'AI Configuration')
@section('page_title', 'AI Configuration')
@section('page_description', 'Configure AI behavior')

@section('content')
<form action="{{ route('admin.settings.ai-config.update') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm font-medium text-gray-700">Model Name</label>
        <input type="text" name="model_name" value="{{ old('model_name', $settings['model_name'] ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
        @error('model_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Temperature</label>
        <input type="number" step="0.1" min="0" max="2" name="temperature" value="{{ old('temperature', $settings['temperature'] ?? '') }}" class="mt-1 w-full border rounded-lg px-4 py-2">
        @error('temperature')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Save Configuration</button>
    </div>
</form>
@endsection
