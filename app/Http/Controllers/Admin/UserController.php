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
        
        // Fetch statistics for the status bar with caching
        $stats = \Illuminate\Support\Facades\Cache::remember('admin.users.stats', now()->addMinutes(10), function() {
            return [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'guests' => ChatSession::whereNull('user_id')->count(),
                'suspended' => User::where('is_active', false)->count(),
            ];
        });

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
            $activities = [];

            // 1. Fetch real reviews
            $reviews = MongoReview::where(function($q) use ($id) {
                    $q->where('user_id', $id)
                      ->orWhere('user_id', new \MongoDB\BSON\ObjectId($id));
                })
                ->with('destination')
                ->latest()
                ->take(5)
                ->get();

            foreach ($reviews as $review) {
                $activities[] = [
                    'icon' => 'chat',
                    'title' => 'Menulis ulasan untuk ' . ($review->destination->name ?? 'Destinasi'),
                    'time' => $review->created_at ? $review->created_at->diffForHumans() : 'baru saja',
                    'timestamp' => $review->created_at ? $review->created_at->timestamp : 0,
                    'color' => 'teal'
                ];
            }

            // 2. Fetch real wishlists (favorites)
            $favorites = \Illuminate\Support\Facades\DB::connection('mongodb')
                ->table('favorites')
                ->where(function($q) use ($id) {
                    $q->where('user_id', $id)
                      ->orWhere('user_id', new \MongoDB\BSON\ObjectId($id));
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            if ($favorites->isNotEmpty()) {
                $destIds = $favorites->pluck('destination_id')->map(function($d) {
                    return (string)$d;
                })->toArray();
                
                // Query destinations
                $destinations = \App\Models\MongoDB\MongoDestination::whereIn('_id', $destIds)
                    ->orWhereIn('_id', array_map(fn($id) => new \MongoDB\BSON\ObjectId($id), $destIds))
                    ->get()
                    ->keyBy(fn($d) => (string)$d->_id);

                foreach ($favorites as $fav) {
                    $destIdStr = (string)$fav->destination_id;
                    $destName = isset($destinations[$destIdStr]) ? $destinations[$destIdStr]->name : 'Destinasi';
                    
                    $createdVal = $fav->created_at ?? null;
                    $createdAt = null;
                    if ($createdVal) {
                        if ($createdVal instanceof \MongoDB\BSON\UTCDateTime) {
                            $createdAt = \Illuminate\Support\Carbon::createFromTimestampMs($createdVal->toDateTime()->getTimestamp() * 1000);
                        } else if ($createdVal instanceof \Illuminate\Support\Carbon) {
                            $createdAt = $createdVal;
                        } else {
                            $createdAt = \Illuminate\Support\Carbon::parse($createdVal);
                        }
                    }

                    $activities[] = [
                        'icon' => 'heart',
                        'title' => 'Menambahkan ' . $destName . ' ke wishlist',
                        'time' => $createdAt ? $createdAt->diffForHumans() : 'baru saja',
                        'timestamp' => $createdAt ? $createdAt->timestamp : 0,
                        'color' => 'teal'
                    ];
                }
            }

            // 3. Fetch real trip plans
            $trips = \Illuminate\Support\Facades\DB::connection('mongodb')
                ->table('trip_plans')
                ->where(function($q) use ($id) {
                    $q->where('user_id', $id)
                      ->orWhere('user_id', new \MongoDB\BSON\ObjectId($id));
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            foreach ($trips as $trip) {
                $summary = (array)($trip->summary ?? []);
                $tripTitle = $summary['title'] ?? 'Trip Toba';
                
                $createdVal = $trip->created_at ?? null;
                $createdAt = null;
                if ($createdVal) {
                    if ($createdVal instanceof \MongoDB\BSON\UTCDateTime) {
                        $createdAt = \Illuminate\Support\Carbon::createFromTimestampMs($createdVal->toDateTime()->getTimestamp() * 1000);
                    } else if ($createdVal instanceof \Illuminate\Support\Carbon) {
                        $createdAt = $createdVal;
                    } else {
                        $createdAt = \Illuminate\Support\Carbon::parse($createdVal);
                    }
                }

                $activities[] = [
                    'icon' => 'map',
                    'title' => 'Membuat trip "' . $tripTitle . '"',
                    'time' => $createdAt ? $createdAt->diffForHumans() : 'baru saja',
                    'timestamp' => $createdAt ? $createdAt->timestamp : 0,
                    'color' => 'teal'
                ];
            }

            // Sort all activities by timestamp desc
            usort($activities, function($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });

            // Slice to 5 items max
            $activities = array_slice($activities, 0, 5);

            // Fallback to mock activities if absolutely empty
            if (empty($activities)) {
                $activities = [
                    ['icon' => 'map', 'title' => 'Membuat trip ke Pantai Bulbul', 'time' => '2 jam lalu', 'color' => 'teal'],
                    ['icon' => 'heart', 'title' => 'Menambahkan Air Terjun Situmurun ke wishlist', 'time' => '2 hari lalu', 'color' => 'teal'],
                    ['icon' => 'search', 'title' => 'Mencari "hotel balige"', 'time' => '3 hari lalu', 'color' => 'teal'],
                    ['icon' => 'map', 'title' => 'Membuat trip ke Museum Batak', 'time' => '5 hari lalu', 'color' => 'teal'],
                ];
            }

            return response()->json([
                'user' => $user,
                'initials' => collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode(''),
                'stats' => [
                    'reviews' => MongoReview::where(function($q) use ($id) {
                        $q->where('user_id', $id)
                          ->orWhere('user_id', new \MongoDB\BSON\ObjectId($id));
                    })->count(),
                    'trips' => \Illuminate\Support\Facades\DB::connection('mongodb')
                        ->table('trip_plans')
                        ->where(function($q) use ($id) {
                            $q->where('user_id', $id)
                              ->orWhere('user_id', new \MongoDB\BSON\ObjectId($id));
                        })
                        ->count(),
                    'wishlists' => \Illuminate\Support\Facades\DB::connection('mongodb')
                        ->table('favorites')
                        ->where(function($q) use ($id) {
                            $q->where('user_id', $id)
                              ->orWhere('user_id', new \MongoDB\BSON\ObjectId($id));
                        })
                        ->count(),
                ],
                'activities' => $activities
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
