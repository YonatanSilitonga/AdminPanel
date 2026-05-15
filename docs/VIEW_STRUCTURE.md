# SMART TOURISM ADMIN PANEL - VIEW FILES STRUCTURE

## 📁 Complete View Files Directory Structure

```
resources/views/admin/
├── layouts/
│   ├── app.blade.php              ✅ CREATED
│   ├── sidebar.blade.php          ✅ CREATED
│   ├── navbar.blade.php           ✅ CREATED
│   └── footer.blade.php           (To create)
│
├── auth/
│   ├── login.blade.php            (To create)
│   ├── forgot-password.blade.php  (To create)
│   ├── reset-password.blade.php   (To create)
│   └── email/
│       └── reset-password.blade.php (To create)
│
├── dashboard/
│   ├── index.blade.php            (To create)
│   └── widgets/
│       ├── stats.blade.php        (To create)
│       ├── charts.blade.php       (To create)
│       └── activity.blade.php     (To create)
│
├── destinations/
│   ├── index.blade.php            (To create)
│   ├── create.blade.php           (To create)
│   ├── edit.blade.php             (To create)
│   ├── show.blade.php             (To create)
│   └── partials/
│       ├── form.blade.php         (To create)
│       ├── gallery-section.blade.php (To create)
│       └── facilities-section.blade.php (To create)
│
├── events/
│   ├── index.blade.php            (To create)
│   ├── create.blade.php           (To create)
│   ├── edit.blade.php             (To create)
│   └── partials/
│       └── form.blade.php         (To create)
│
├── reviews/
│   ├── index.blade.php            (To create)
│   ├── show.blade.php             (To create)
│   └── partials/
│       └── table.blade.php        (To create)
│
├── reports/
│   ├── index.blade.php            (To create)
│   ├── show.blade.php             (To create)
│   └── partials/
│       └── action-modal.blade.php (To create)
│
├── users/
│   ├── index.blade.php            (To create)
│   ├── activity.blade.php         (To create)
│   └── partials/
│       └── table.blade.php        (To create)
│
├── recommendations/
│   ├── index.blade.php            (To create)
│   ├── show.blade.php             (To create)
│   └── export.blade.php           (To create)
│
├── chatbot-logs/
│   ├── index.blade.php            (To create)
│   ├── show.blade.php             (To create)
│   └── partials/
│       └── conversation.blade.php (To create)
│
├── analytics/
│   ├── dashboard.blade.php        (To create)
│   ├── destinations.blade.php     (To create)
│   ├── events.blade.php           (To create)
│   ├── reports.blade.php          (To create)
│   └── partials/
│       └── charts.blade.php       (To create)
│
├── settings/
│   ├── general.blade.php          (To create)
│   ├── api-keys.blade.php         (To create)
│   ├── ai-config.blade.php        (To create)
│   └── audit-logs.blade.php       (To create)
│
├── profile/
│   ├── edit.blade.php             (To create)
│   └── password.blade.php         (To create)
│
├── components/
│   ├── card.blade.php             (To create)
│   ├── stat-card.blade.php        (To create)
│   ├── table.blade.php            (To create)
│   ├── form.blade.php             (To create)
│   ├── modal.blade.php            (To create)
│   ├── pagination.blade.php       (To create)
│   ├── alert.blade.php            (To create)
│   ├── button.blade.php           (To create)
│   ├── input.blade.php            (To create)
│   ├── select.blade.php           (To create)
│   ├── textarea.blade.php         (To create)
│   ├── file-upload.blade.php      (To create)
│   └── badge.blade.php            (To create)
│
└── errors/
    ├── 403.blade.php              (To create)
    ├── 404.blade.php              (To create)
    └── 500.blade.php              (To create)
```

---

## 📄 VIEW FILE TEMPLATES

### 1. LOGIN PAGE

**File**: `resources/views/admin/auth/login.blade.php`

```blade
@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo -->
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Admin Panel
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Smart Tourism
            </p>
        </div>

        <!-- Login Form -->
        <form class="mt-8 space-y-6" action="{{ route('admin.login.post') }}" method="POST">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="sr-only">Email</label>
                <input 
                    id="email"
                    name="email"
                    type="email"
                    required
                    class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm"
                    placeholder="Email address"
                    value="{{ old('email') }}"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="sr-only">Password</label>
                <input 
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm"
                    placeholder="Password"
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('admin.forgot-password') }}" class="font-medium text-primary hover:text-primary-dark">
                        Forgot password?
                    </a>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                >
                    Sign in
                </button>
            </div>
        </form>

        <!-- Footer -->
        <p class="mt-2 text-center text-sm text-gray-600">
            v1.0 | {{ date('Y') }} Smart Tourism
        </p>
    </div>
</div>
@endsection
```

---

### 2. DASHBOARD PAGE

**File**: `resources/views/admin/dashboard/index.blade.php`

```blade
@extends('admin.layouts.app')

@section('page_title', 'Dashboard')
@section('page_description', 'Welcome to Smart Tourism Admin Panel')

@section('content')
<div class="space-y-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <x-stat-card 
            title="Total Destinations" 
            value="{{ $stats['total_destinations'] }}"
            icon="destination"
            trend="+12"
        />
        <x-stat-card 
            title="Total Events" 
            value="{{ $stats['total_events'] }}"
            icon="event"
            trend="+5"
        />
        <x-stat-card 
            title="Total Users" 
            value="{{ $stats['total_users'] }}"
            icon="users"
            trend="+8%"
        />
        <x-stat-card 
            title="Pending Reviews" 
            value="{{ $stats['pending_reviews'] }}"
            icon="review"
            highlight="true"
        />
        <x-stat-card 
            title="Pending Reports" 
            value="{{ $stats['pending_reports'] }}"
            icon="report"
            highlight="true"
        />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Data Chart -->
        <x-card title="Monthly Activity">
            <canvas id="monthlyChart"></canvas>
        </x-card>

        <!-- Recent Activity -->
        <x-card title="Recent Activity">
            <div class="space-y-3">
                @forelse($recentActivity as $activity)
                    <div class="flex items-center justify-between pb-3 border-b last:border-b-0">
                        <div>
                            <p class="text-sm font-medium">{{ $activity->admin->name }}</p>
                            <p class="text-xs text-gray-600">
                                {{ $activity->formatted_action }} {{ $activity->entity_type }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $activity->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-gray-500">No recent activity</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <!-- Featured Destinations -->
    <x-card title="Featured Destinations">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4">Name</th>
                        <th class="text-left py-3 px-4">Category</th>
                        <th class="text-left py-3 px-4">Rating</th>
                        <th class="text-left py-3 px-4">Reviews</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($featuredDestinations as $destination)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $destination->name }}</td>
                            <td class="py-3 px-4"><x-badge>{{ $destination->category }}</x-badge></td>
                            <td class="py-3 px-4">⭐ {{ $destination->rating }}/5</td>
                            <td class="py-3 px-4">{{ $destination->rating_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500">
                                No featured destinations
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@push('scripts')
<script>
    // Monthly Chart
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const chartData = @json($chartData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(d => d.month),
            datasets: [
                {
                    label: 'Destinations',
                    data: chartData.map(d => d.destinations),
                    borderColor: '#3B82F6',
                    tension: 0.4,
                },
                {
                    label: 'Events',
                    data: chartData.map(d => d.events),
                    borderColor: '#10B981',
                    tension: 0.4,
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            }
        }
    });
</script>
@endpush
@endsection
```

---

### 3. DESTINATIONS LIST

**File**: `resources/views/admin/destinations/index.blade.php`

```blade
@extends('admin.layouts.app')

@section('page_title', 'Destinations')
@section('page_description', 'Manage all tourism destinations')

@section('content')
<div class="space-y-4">
    <!-- Header with Create Button -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold">Destinations</h2>
        </div>
        <a href="{{ route('admin.destinations.create') }}" 
           class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
            + Add Destination
        </a>
    </div>

    <!-- Filters -->
    <x-card>
        <form method="GET" class="flex gap-4 flex-wrap">
            <!-- Search -->
            <input 
                type="text" 
                name="search" 
                placeholder="Search destinations..."
                value="{{ request('search') }}"
                class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
            >

            <!-- Category Filter -->
            <select name="category" class="px-4 py-2 border rounded-lg">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                        {{ ucfirst($cat) }}
                    </option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <!-- Search Button -->
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark">
                Search
            </button>
            <a href="{{ route('admin.destinations.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">
                Reset
            </a>
        </form>
    </x-card>

    <!-- Destinations Table -->
    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3">Name</th>
                        <th class="text-left px-4 py-3">Category</th>
                        <th class="text-left px-4 py-3">Rating</th>
                        <th class="text-left px-4 py-3">Created</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $destination)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($destination->thumbnail_url)
                                        <img src="{{ asset('storage/' . $destination->thumbnail_url) }}" 
                                             alt="thumbnail"
                                             class="w-10 h-10 rounded object-cover">
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $destination->name }}</p>
                                        <p class="text-xs text-gray-600">by {{ $destination->admin->name ?? 'System' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <x-badge type="info">{{ ucfirst($destination->category) }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                ⭐ {{ number_format($destination->rating, 1) }}/5
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $destination->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($destination->is_active)
                                    <x-badge type="success">Active</x-badge>
                                @else
                                    <x-badge type="warning">Inactive</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.destinations.edit', $destination) }}" 
                                       class="text-primary hover:underline text-sm">Edit</a>
                                    <form action="{{ route('admin.destinations.destroy', $destination) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Delete this destination?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger hover:underline text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                No destinations found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $destinations->links('admin.components.pagination') }}
        </div>
    </x-card>
</div>
@endsection
```

---

### 4. REUSABLE COMPONENTS

**File**: `resources/views/admin/components/stat-card.blade.php`

```blade
<div class="bg-white rounded-lg shadow p-6 {{ $highlight ?? false ? 'ring-2 ring-danger' : '' }}">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-600 text-sm font-medium">{{ $title }}</p>
            <p class="text-3xl font-bold text-dark mt-1">{{ $value }}</p>
            @if($trend ?? false)
                <p class="text-xs text-green-600 mt-1">{{ $trend }}</p>
            @endif
        </div>
        <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center">
            <!-- Icon goes here -->
            <span class="text-primary text-xl">📊</span>
        </div>
    </div>
</div>
```

**File**: `resources/views/admin/components/card.blade.php`

```blade
<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($title ?? false)
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-dark">{{ $title }}</h3>
            @if($action ?? false)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif

    <div class="px-6 py-4">
        {{ $slot }}
    </div>
</div>
```

---

## 🎯 REMAINING VIEWS TO CREATE

### High Priority (Needed for MVP)
1. ✅ Login page
2. ✅ Dashboard
3. ✅ Destinations list & CRUD
4. Events list & CRUD
5. Reviews moderation
6. Reports management
7. Audit logs viewer

### Medium Priority (Beta features)
8. Users management
9. Analytics dashboard
10. Settings panel

### Low Priority (Polish)
11. Profile management
12. Recommendation logs
13. Chatbot logs
14. Email templates

---

## 📝 VIEW FILE NAMING CONVENTION

- List pages: `index.blade.php`
- Create form: `create.blade.php`
- Edit form: `edit.blade.php`
- Detail view: `show.blade.php`
- Partials: `partials/` folder
- Components: `components/` folder
- Layouts: `layouts/` folder
- Shared: `admin/components/` for reusable

---

## 🏗️ COMPONENT STRUCTURE

All components should follow this pattern:

```blade
<!-- components/my-component.blade.php -->
<div class="component-class">
    <!-- Component content -->
    {{ $slot }}
</div>
```

Usage in another view:

```blade
<x-my-component attr="value">
    Content here
</x-my-component>
```

---

**Total Views to Create**: ~40 blade files  
**Estimated Time**: 3-4 weeks  
**Priority Order**: List → Create → Edit → Details → Components
