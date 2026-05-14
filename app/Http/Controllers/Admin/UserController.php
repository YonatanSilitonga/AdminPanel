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

        // Advanced Sorting
        $sortColumn = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['name', 'email', 'role', 'is_active', 'created_at'];
        
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Rows per page
        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage)->withQueryString();
        
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
     * Toggle the specified user's active status.
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $newStatus = !$user->is_active;
            $user->is_active = $newStatus;
            $user->save();

            $statusText = $newStatus ? 'diaktifkan' : 'ditangguhkan (suspend)';
            
            if ($request->ajax() || $request->wantsJson()) {
                session()->flash('success', "Akun {$user->name} berhasil {$statusText}");
                return response()->json([
                    'success' => true,
                    'message' => "Akun {$user->name} berhasil {$statusText}",
                    'is_active' => $newStatus
                ]);
            }

            return redirect()->route('admin.users.index')->with('success', "Akun {$user->name} berhasil {$statusText}");
        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengubah status akun.'], 500);
            }
            return back()->with('error', 'Gagal mengubah status akun.');
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

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = User::query();

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $status = $request->status === 'active';
                $query->where('is_active', $status);
            }

            $users = $query->orderBy('created_at', 'desc')->get();

            $filename = 'users_report_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                fputcsv($file, ['ID', 'Nama', 'Email', 'Role', 'Status', 'Tanggal Bergabung'], ';');

                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->role ?? 'user',
                        $user->is_active ? 'Aktif' : 'Suspended',
                        $user->created_at?->format('d-m-Y H:i') ?? '-',
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('User export error: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Gagal mengekspor data pengguna.');
        }
    }
}
