@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 to-orange-100">
    <div class="text-center max-w-md">
        <!-- Error Code -->
        <div class="mb-4">
            <h1 class="text-6xl font-bold text-red-600">403</h1>
        </div>

        <!-- Error Message -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Access Denied</h2>
            <p class="text-gray-600">You don't have permission to access this resource.</p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <a href="{{ route('admin.dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                ← Back to Dashboard
            </a>
            <br>
            <a href="{{ route('admin.login') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Login with different account
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-gray-600 text-sm">
                If you believe this is a mistake, please contact the system administrator.
            </p>
        </div>
    </div>
</div>
@endsection
