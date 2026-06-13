<?php

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\ChatSession;
use Illuminate\Http\Request;

class ChatbotLogController extends BaseAdminController
{
    /**
     * Display chatbot session logs from MongoDB.
     */
    public function index(Request $request)
    {
        $query = ChatSession::query();

        // Filter by user type
        if ($request->filled('type')) {
            if ($request->type === 'guest') {
                $query->whereNull('user_id')->orWhere('user_id', '');
            } elseif ($request->type === 'user') {
                $query->whereNotNull('user_id')->where('user_id', '!=', '');
            }
        }

        // Filter by flagged status
        if ($request->filled('flagged')) {
            if ($request->flagged === 'yes') {
                $query->where('is_flagged', true);
            } elseif ($request->flagged === 'no') {
                $query->where(function($q) {
                    $q->where('is_flagged', false)->orWhereNull('is_flagged');
                });
            }
        }

        // Filter by user (Name/Email/ID)
        if ($request->filled('search')) {
            $search = $request->search;
            
            $users = \App\Models\User::where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->get(['_id']);
                
            $query->where(function($q) use ($search, $users) {
                $q->where('user_id', $search);
                if (preg_match('/^[a-f\d]{24}$/i', $search)) {
                    $q->orWhere('user_id', new \MongoDB\BSON\ObjectId($search));
                }
                
                if ($users->isNotEmpty()) {
                    $idsStr = $users->pluck('_id')->map(fn($id) => (string)$id)->toArray();
                    $q->orWhereIn('user_id', $idsStr);
                    
                    $objectIds = [];
                    foreach ($idsStr as $idStr) {
                        if (preg_match('/^[a-f\d]{24}$/i', $idStr)) {
                            $objectIds[] = new \MongoDB\BSON\ObjectId($idStr);
                        }
                    }
                    if (!empty($objectIds)) {
                        $q->orWhereIn('user_id', $objectIds);
                    }
                }
            });
        }

        // IMPORTANT: Filter out sessions without messages to avoid showing empty sessions
        // Uses MongoDB $expr operator to check array size > 0
        $query->whereRaw(['messages' => ['$exists' => true, '$not' => ['$size' => 0]]]);

        // Advanced Sorting
        $sortColumn = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['user_id', 'updated_at'];
        
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        // Rows per page
        $perPage = $request->get('per_page', 10);
        $sessions = $query->with('user')->paginate($perPage)->withQueryString();

        // Stats
        $totalSessions  = ChatSession::count();
        $userSessions   = ChatSession::whereNotNull('user_id')->where('user_id', '!=', '')->count();
        $guestSessions  = $totalSessions - $userSessions;
        $flaggedSessions = ChatSession::where('is_flagged', true)->count();

        return view('admin.chatbot-logs.index', compact(
            'sessions',
            'totalSessions',
            'userSessions',
            'guestSessions',
            'flaggedSessions'
        ));
    }

    /**
     * Show a single chatbot session detail.
     */
    public function show(string $id)
    {
        $session = ChatSession::where('_id', $id)->firstOrFail();
        return view('admin.chatbot-logs.show', compact('session'));
    }

    /**
     * Toggle flag status on a chatbot session.
     */
    public function flag(Request $request, string $id)
    {
        try {
            $session = ChatSession::where('_id', $id)->firstOrFail();

            $isFlagging = !$session->isFlagged();

            $session->is_flagged = $isFlagging;

            if ($isFlagging) {
                $session->flagged_at = now();
                $session->flagged_by = auth('admin')->id();
                $session->flag_reason = $request->input('flag_reason', null);
            } else {
                // Unflag — bersihkan metadata flag
                $session->flagged_at  = null;
                $session->flagged_by  = null;
                $session->flag_reason = null;
            }

            $session->save();

            $label = $isFlagging ? 'ditandai' : 'tandai dibatalkan';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'     => true,
                    'is_flagged'  => $session->is_flagged,
                    'message'     => "Sesi berhasil {$label}.",
                ]);
            }

            return back()->with('success', "Sesi berhasil {$label}.");

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal mengubah status flag: ' . $e->getMessage());
        }
    }

    /**
     * Export chatbot sessions to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = ChatSession::orderBy('updated_at', 'desc');

            // Apply same filters as index
            if ($request->filled('type')) {
                if ($request->type === 'guest') {
                    $query->whereNull('user_id')->orWhere('user_id', '');
                } elseif ($request->type === 'user') {
                    $query->whereNotNull('user_id')->where('user_id', '!=', '');
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $users = \App\Models\User::where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->get(['_id']);
                
                $query->where(function($q) use ($search, $users) {
                    $q->where('user_id', $search);
                    if (preg_match('/^[a-f\d]{24}$/i', $search)) {
                        $q->orWhere('user_id', new \MongoDB\BSON\ObjectId($search));
                    }
                    
                    if ($users->isNotEmpty()) {
                        $idsStr = $users->pluck('_id')->map(fn($id) => (string)$id)->toArray();
                        $q->orWhereIn('user_id', $idsStr);
                        
                        $objectIds = [];
                        foreach ($idsStr as $idStr) {
                            if (preg_match('/^[a-f\d]{24}$/i', $idStr)) {
                                $objectIds[] = new \MongoDB\BSON\ObjectId($idStr);
                            }
                        }
                        if (!empty($objectIds)) {
                            $q->orWhereIn('user_id', $objectIds);
                        }
                    }
                });
            }

            // IMPORTANT: Filter out sessions without messages to keep export clean
            // Uses MongoDB $expr operator to check array size > 0
            $query->whereRaw(['messages' => ['$exists' => true, '$not' => ['$size' => 0]]]);

            $sessions = $query->with('user')->get();

            $filename = 'chatbot_logs_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($sessions) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                fputcsv($file, ['Session ID', 'Nama User', 'Email User', 'Tipe', 'Total Pesan', 'Aktivitas Terakhir'], ';');

                foreach ($sessions as $session) {
                    $type = (!empty($session->user_id)) ? 'User' : 'Guest';
                    $userName = $session->user ? $session->user->name : ($session->user_id ? 'User Terdaftar (' . $session->user_id . ')' : 'Guest');
                    $userEmail = $session->user ? $session->user->email : ($session->user_id ? '-' : 'Guest');
                    $messageCount = is_array($session->messages) ? count($session->messages) : 0;
                    
                    fputcsv($file, [
                        $session->_id,
                        $userName,
                        $userEmail,
                        $type,
                        $messageCount,
                        $session->updated_at?->format('d-m-Y H:i') ?? '-',
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ChatbotLog export error: ' . $e->getMessage());
            return redirect()->route('admin.chatbot-logs.index')->with('error', 'Gagal mengekspor data chatbot.');
        }
    }
}
