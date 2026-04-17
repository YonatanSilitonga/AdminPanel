<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\MongoDB\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends BaseAdminController
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $roles = User::pluck('role')->unique()->filter()->values()->all();
        if (empty($roles)) { $roles = ['user', 'admin']; }

        // Fetch statistics for the status bar
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'guests' => ChatSession::whereNull('user_id')->count(),
            'suspended' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'is_active' => 'boolean'
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified user.
     * Returns JSON for the Alpine.js modal.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json($user);
        }

        // If not ajax, show index with user
        return redirect()->route('admin.users.index');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id . ',_id',
            'password' => 'nullable|string|min:8',
            'role' => 'required|string',
            'is_active' => 'boolean'
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'is_active' => $request->has('is_active'),
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.']);
            }

            return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal memperbarui user.']);
            }
            return back()->with('error', 'Gagal memperbarui user.')->withInput();
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus user.');
        }
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = !($user->is_active ?? false);
            $user->save();

            return back()->with('success', 'Status user berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui status user.');
        }
    }

    /**
     * Show user activity (Returns JSON for modal if AJAX).
     */
    public function showActivity($id)
    {
        $user = User::findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            // Fetch real reviews as activity
            $realActivities = MongoReview::where('user_id', $id)
                ->with('destination')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($review) {
                    return [
                        'icon' => 'chat',
                        'title' => 'Menulis ulasan untuk ' . ($review->destination->name ?? 'Destinasi'),
                        'time' => $review->created_at->diffForHumans(),
                        'color' => 'blue'
                    ];
                })
                ->toArray();

            // Mock activities if real ones are sparse, but prioritize real data
            $mockActivities = [
                ['icon' => 'search', 'title' => 'Mencari "destinasi populer"', 'time' => '1 hari lalu', 'color' => 'teal'],
                ['icon' => 'map', 'title' => 'Melihat petunjuk jalan', 'time' => '3 hari lalu', 'color' => 'teal'],
            ];

            $activities = array_merge($realActivities, array_slice($mockActivities, 0, 5 - count($realActivities)));

            return response()->json([
                'user' => $user,
                'initials' => collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode(''),
                'stats' => [
                    'reviews' => MongoReview::where('user_id', $id)->count(),
                    'trips' => rand(5, 15), // Placeholder as no Trip model found yet
                    'wishlists' => rand(10, 30), // Placeholder as no Wishlist model found yet
                ],
                'activities' => $activities
            ]);
        }

        return view('admin.users.activity', compact('user'));
    }
}
