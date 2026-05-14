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

        // Filter by user ID
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

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
        $sessions = $query->paginate($perPage)->withQueryString();

        // Stats
        $totalSessions = ChatSession::count();
        $userSessions  = ChatSession::whereNotNull('user_id')->where('user_id', '!=', '')->count();
        $guestSessions = $totalSessions - $userSessions;

        return view('admin.chatbot-logs.index', compact(
            'sessions',
            'totalSessions',
            'userSessions',
            'guestSessions'
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
     * Flag action — placeholder (MongoDB sessions don't have a flag field from Go backend).
     * Returns back with a notice.
     */
    public function flag(string $id)
    {
        return redirect()->route('admin.chatbot-logs.index')
            ->with('info', 'Fitur flag belum tersedia untuk sesi MongoDB.');
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

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $sessions = $query->get();

            $filename = 'chatbot_logs_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($sessions) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                fputcsv($file, ['Session ID', 'User ID', 'Type', 'Total Messages', 'Last Activity'], ';');

                foreach ($sessions as $session) {
                    $type = (!empty($session->user_id)) ? 'User' : 'Guest';
                    $messageCount = is_array($session->messages) ? count($session->messages) : 0;
                    
                    fputcsv($file, [
                        $session->_id,
                        $session->user_id ?? 'Guest',
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
