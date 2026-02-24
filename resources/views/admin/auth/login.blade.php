@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Smart Tourism</h1>
            <p class="text-gray-600 text-sm mt-2">Admin Panel</p>
            <p class="text-gray-500 text-xs mt-1">v1.0</p>
        </div>

        <!-- Title -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
            <p class="text-gray-600 text-sm mt-1">Sign in to your admin account</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 text-sm font-medium">{{ $errors->first() }}</p>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
            @csrf

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                    placeholder="admin@smarttourism.local"
                    required
                    autofocus
                >
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-sm text-gray-600">Remember me for 30 days</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Sign In
            </button>
        </form>

        <!-- Forgot Password Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('admin.forgot-password') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Forgot your password?
            </a>
        </div>

        <!-- Demo Credentials (For Development Only) -->
        @if (app()->environment('local'))
            <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800 text-xs font-semibold mb-2">📋 Demo Credentials (Local Only)</p>
                <div class="space-y-1 text-xs text-yellow-700">
                    <p><strong>Super Admin:</strong> superadmin@smarttourism.local / SuperAdmin@123</p>
                    <p><strong>Admin:</strong> admin@smarttourism.local / Admin@123</p>
                    <p><strong>Moderator:</strong> moderator@smarttourism.local / Moderator@123</p>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-center text-gray-600 text-xs">
                Smart Tourism Admin Panel &copy; 2026.<br>
                All rights reserved.
            </p>
        </div>
    </div>
</div>
@endsection
