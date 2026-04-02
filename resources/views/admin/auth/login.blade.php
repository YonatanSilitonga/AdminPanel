<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toba Tourism - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
        }
        .bg-gradient-purple {
            background: linear-gradient(135deg, #3d2a5a 0%, #2d1b3d 50%, #1a0f2e 100%);
        }
        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gradient-purple min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- Logo and Header -->
        <div class="text-center mb-12">
            <div class="flex justify-center mb-6">
                <div class="bg-gray-700 bg-opacity-50 rounded-2xl p-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-white">Toba Tourism</h1>
            <p class="text-gray-300 text-sm mt-2">Admin Panel</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <!-- Card Title -->
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Masuk ke Dashboard</h2>

            @if(session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        placeholder="admin@tobatourism.id"
                        required
                        autofocus
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition pr-10"
                            placeholder="••••••••"
                            required
                        >
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <button 
                            type="button" 
                            class="password-toggle absolute right-3 top-3.5 text-gray-500 hover:text-gray-700"
                            onclick="togglePasswordVisibility()"
                        >
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold py-3 px-4 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 mt-6"
                >
                    Masuk
                </button>
            </form>

            <!-- Forgot Password Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Lupa password? <a href="#" class="text-purple-600 hover:text-purple-700 font-medium">Hubungi Superadmin</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-gray-400 text-sm">© 2026 Toba Tourism. All rights reserved.</p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('text-purple-600');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('text-purple-600');
            }
        }
    </script>
</body>
</html>