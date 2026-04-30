@extends('admin.layouts.app')

@section('title', 'Report Detail')
@section('page_title', 'Report Detail')
@section('page_description', 'Review and take action in MongoDB')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div class="flex justify-between items-start">
        <div>
            <h3 class="text-lg font-semibold text-dark">Report Information</h3>
            <p class="text-sm text-gray-600">{{ $report->created_at?->format('d M Y H:i') }}</p>
        </div>
        <span class="px-3 py-1 text-sm rounded-full 
            @if($report->status === 'pending') bg-yellow-100 text-yellow-700
            @elseif($report->status === 'reviewed') bg-blue-100 text-blue-700
            @elseif($report->status === 'resolved') bg-green-100 text-green-700
            @else bg-gray-100 text-gray-600 @endif">
            {{ ucfirst($report->status ?? 'pending') }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-y py-6">
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wider">Reporter ID</p>
            <p class="text-dark font-medium">{{ $report->user_id ?? 'Anonymous' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wider">Target Destination</p>
            <p class="text-dark font-medium">{{ optional($report->destination)->name ?? 'General / Not Found' }}</p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500 uppercase tracking-wider">Description</p>
            <p class="text-dark mt-1">{{ $report->description ?? '-' }}</p>
        </div>
        @if($report->all_image_urls && count($report->all_image_urls) > 0)
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500 uppercase tracking-wider mb-2">Attachments</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($report->all_image_urls as $url)
                <div class="relative group aspect-video rounded-lg overflow-hidden border">
                    <img src="{{ $url }}" alt="Report Image" class="w-full h-full object-cover">
                    <a href="{{ $url }}" target="_blank" class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wider">Assigned Admin ID</p>
            <p class="text-dark font-medium">{{ $report->assigned_to ?? 'Unassigned' }}</p>
        </div>
    </div>

    <div class="space-y-6">
        <h4 class="font-semibold text-dark">Take Action</h4>
        
        <form action="{{ route('admin.reports.action', $report->_id) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Action Type</label>
                    <select name="action" class="mt-1 w-full border rounded-lg px-4 py-2">
                        <option value="ignore" @selected(old('action', $report->action_taken) === 'ignore')>Ignore/Dismiss</option>
                        <option value="warn_user" @selected(old('action', $report->action_taken) === 'warn_user')>Warn User</option>
                        <option value="delete_content" @selected(old('action', $report->action_taken) === 'delete_content')>Delete Content</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Action Reason / Internal Notes</label>
                <textarea name="action_reason" rows="4" class="mt-1 w-full border rounded-lg px-4 py-2" placeholder="Explain why this action was taken...">{{ old('action_reason', $report->action_reason) }}</textarea>
                @error('action_reason')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">Submit Action & Resolve</button>
        </form>

        <div class="flex items-center gap-3 pt-4 border-t">
            @if($report->status !== 'reviewed')
            <form action="{{ route('admin.reports.assign', $report->_id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Assign to Me & Review</button>
            </form>
            @endif

            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">Back to List</a>
        </div>
    </div>
</div>
@endsection
