<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\MongoDB\MongoReview;
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

        // Date filter
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Fetch statistics for the status bar
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'guests' => ChatSession::whereNull('user_id')->count(),
            'suspended' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
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
            'is_active' => 'required|boolean'
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => (bool)$request->is_active,
            ]);

            return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data pengguna.');
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $userName = $user->name;
            $user->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => "Akun {$userName} telah berhasil dihapus dari sistem."
                ]);
            }

            return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus user.']);
            }
            return back()->with('error', 'Gagal menghapus user.');
        }
    }

    /**
     * Show user activity (Returns JSON for modal if AJAX).
     */
    public function showActivity($id)
    {
        $user = User::findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            // Fetch real reviews
            $activities = MongoReview::where('user_id', $id)
                ->with('destination')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($review) {
                    return [
                        'icon' => 'chat',
                        'title' => 'Menulis ulasan untuk ' . ($review->destination->name ?? 'Destinasi'),
                        'time' => $review->created_at->diffForHumans(),
                        'color' => 'teal'
                    ];
                })
                ->toArray();

            // Mock activities to match Figma if real ones are sparse
            $mockActivities = [
                ['icon' => 'map', 'title' => 'Membuat trip ke Pantai Bulbul', 'time' => '2 jam lalu', 'color' => 'teal'],
                ['icon' => 'heart', 'title' => 'Menambahkan Air Terjun Situmurun ke wishlist', 'time' => '2 hari lalu', 'color' => 'teal'],
                ['icon' => 'search', 'title' => 'Mencari "hotel balige"', 'time' => '3 hari lalu', 'color' => 'teal'],
                ['icon' => 'map', 'title' => 'Membuat trip ke Museum Batak', 'time' => '5 hari lalu', 'color' => 'teal'],
            ];

            foreach ($mockActivities as $mock) {
                if (count($activities) < 5) {
                    $activities[] = $mock;
                }
            }

            return response()->json([
                'user' => $user,
                'initials' => collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode(''),
                'stats' => [
                    'reviews' => MongoReview::where('user_id', $id)->count() ?: 15, // Using Figma values as fallback
                    'trips' => 8, 
                    'wishlists' => 35,
                ],
                'activities' => array_slice($activities, 0, 5)
            ]);
        }

        return view('admin.users.activity', compact('user'));
    }
}
