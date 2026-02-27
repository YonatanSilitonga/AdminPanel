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
        $query = ChatSession::orderBy('updated_at', 'desc');

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

        $perPage = 20;
        $sessions = $query->paginate($perPage);

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
}
