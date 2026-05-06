<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoEvent;
use App\Services\Admin\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends BaseAdminController
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        parent::__construct();
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of the events.
     */
    public function index(Request $request)
    {
        $this->checkPermission('view_events');

        $events = $this->eventService->getPaginatedEvents([
            'search' => $request->get('search'),
            'category' => $request->get('category'),
            'status' => $request->get('status', 'all'),
        ]);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $this->checkPermission('create_event');
        return view('admin.events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('create_event');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Budaya,Adat,Olahraga,Kuliner',
            'location' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'description' => 'required|string',
            'long_description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
            'schedule' => 'nullable|array',
            'schedule.*.time' => 'required_with:schedule|string',
            'schedule.*.activity' => 'required_with:schedule|string',
            'opening_hours' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|string|max:255',
            'best_time' => 'nullable|string|max:255',
        ]);

        try {
            if (isset($validated['tags']) && $validated['tags']) {
                $validated['tags'] = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
            } else {
                $validated['tags'] = [];
            }
            
            $event = $this->eventService->createEvent($validated, $request->file('banner'));
            
            $this->logActivity('create', 'event', (string)$event->_id, null, $event->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil dibuat.',
                    'event' => $event
                ]);
            }

            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat event: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->with('error', 'Gagal membuat event: ' . $e->getMessage());
        }
    }

    public function edit(string $id, Request $request)
    {
        $this->checkPermission('edit_event');
        $event = MongoEvent::findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($event);
        }

        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('edit_event');
        $event = MongoEvent::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Budaya,Adat,Olahraga,Kuliner',
            'location' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'description' => 'required|string',
            'long_description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
            'schedule' => 'nullable|array',
            'schedule.*.time' => 'required_with:schedule|string',
            'schedule.*.activity' => 'required_with:schedule|string',
            'opening_hours' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|string|max:255',
            'best_time' => 'nullable|string|max:255',
        ]);

        try {
            if (isset($validated['tags']) && $validated['tags']) {
                $validated['tags'] = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
            } else {
                $validated['tags'] = [];
            }

            $oldValues = $event->toArray();
            $event = $this->eventService->updateEvent($event, $validated, $request->file('banner'));
            
            $this->logActivity('update', 'event', (string)$event->_id, $oldValues, $event->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil diperbarui.',
                    'event' => $event
                ]);
            }

            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui event: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->with('error', 'Gagal memperbarui event: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(string $id)
    {
        $this->checkPermission('delete_event');
        $event = MongoEvent::findOrFail($id);

        try {
            $this->logActivity('delete', 'event', (string)$event->_id, $event->toArray());
            $this->eventService->deleteEvent($event);

            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus event.');
        }
    }

    /**
     * Toggle event status.
     */
    public function toggleStatus(string $id)
    {
        $this->checkPermission('toggle_event_status');
        $event = MongoEvent::findOrFail($id);

        $oldStatus = $event->is_active;
        $this->eventService->toggleStatus($event);
        
        $this->logActivity('update_status', 'event', (string)$event->_id, 
            ['is_active' => $oldStatus], 
            ['is_active' => $event->is_active]
        );

        return back()->with('success', 'Status event berhasil diperbarui.');
    }
}
