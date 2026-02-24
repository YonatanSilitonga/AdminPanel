@extends('admin.layouts.app')

@section('title', 'Chatbot Log Detail')
@section('page_title', 'Chatbot Log Detail')
@section('page_description', 'Conversation details')

@section('content')
<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div>
        <p class="text-sm text-gray-500">User</p>
        <p class="text-dark font-medium">{{ optional($log->user)->name ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Message</p>
        <p class="text-dark">{{ $log->message ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Response</p>
        <p class="text-dark">{{ $log->response ?? '-' }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-500">Created</p>
        <p class="text-dark">{{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}</p>
    </div>

    <div class="flex items-center gap-3">
        <form action="{{ route('admin.chatbot-logs.flag', $log ?? 0) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg">Flag</button>
        </form>
        <a href="{{ route('admin.chatbot-logs.index') }}" class="px-4 py-2 border rounded-lg">Back</a>
    </div>
</div>
@endsection
