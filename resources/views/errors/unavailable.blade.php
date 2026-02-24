<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-6">
        <div class="bg-white rounded-lg shadow p-8 max-w-md text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Service Unavailable</h1>
            <p class="text-gray-600 mb-6">The system is currently in maintenance mode. Please try again later.</p>
            <a href="{{ url('/') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Back to Home</a>
        </div>
    </div>
</body>
</html>
