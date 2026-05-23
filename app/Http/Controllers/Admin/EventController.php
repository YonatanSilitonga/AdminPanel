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
        $events = $this->eventService->getPaginatedEvents([
            'search' => $request->get('search'),
            'category' => $request->get('category'),
            'status' => $request->get('status', 'all'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => $request->get('per_page', 10),
        ]);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,webp|max:10240',
            'banner' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
            'schedule' => 'nullable|array',
            'schedule.*.time' => 'nullable|string',
            'schedule.*.activity' => 'nullable|string',
            'opening_hours' => 'nullable|string|max:255',
            'opening_hours_start' => 'nullable|date_format:H:i',
            'opening_hours_end' => 'nullable|date_format:H:i',
            'ticket_price' => 'nullable|string|max:255',
            'best_time' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Combine opening hours if separate fields are provided
            $openingHours = $validated['opening_hours'] ?? null;
            if (!$openingHours && !empty($validated['opening_hours_start']) && !empty($validated['opening_hours_end'])) {
                $openingHours = $validated['opening_hours_start'] . ' - ' . $validated['opening_hours_end'];
            }

            $event = new MongoEvent();
            $event->name = $validated['name'];
            $event->category = $validated['category'];
            $event->location = $validated['location'];
            $event->organizer = $validated['organizer'] ?? null;
            $event->description = $validated['description'];
            $event->long_description = $validated['long_description'] ?? null;
            $event->start_date = $validated['start_date'];
            $event->end_date = $validated['end_date'];
            $event->opening_hours = $openingHours ?? '08:00 - 17:00';
            $event->ticket_price = $validated['ticket_price'] ?? 'Gratis';
            $event->best_time = $validated['best_time'] ?? 'Kapan saja';
            $event->latitude = isset($validated['latitude']) ? (float)$validated['latitude'] : null;
            $event->longitude = isset($validated['longitude']) ? (float)$validated['longitude'] : null;
            $event->is_active = $request->boolean('is_active', true);
            $event->admin_id = auth('admin')->id();
            $event->slug = \Illuminate\Support\Str::slug($validated['name']) . '-' . \Illuminate\Support\Str::random(5);

            // Tags handling
            if (!empty($request->tags)) {
                $event->tags = array_values(array_filter(array_map('trim', explode(',', $request->tags))));
            } else {
                $event->tags = [];
            }

            // Schedule handling
            if (!empty($request->schedule) && is_array($request->schedule)) {
                $event->schedule = array_values(array_filter($request->schedule, function($item) {
                    return !empty($item['activity']);
                }));
            } else {
                $event->schedule = [];
            }

            $uploadedImages = [];
            if ($request->hasFile('banner')) {
                $path = $this->processImage($request->file('banner'), 'events');
                if ($path) $uploadedImages[] = $path;
            }
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $this->processImage($file, 'events');
                    if ($path) $uploadedImages[] = $path;
                }
            }
            if (count($uploadedImages) > 0) {
                $event->banner_url = $uploadedImages[0];
                $event->images = $uploadedImages;
            }

            $event->save();
            
            try {
                $this->logActivity('create', 'event', (string)$event->_id, null, $event->toArray());
            } catch (\Exception $logEx) {
                Log::warning('Event activity log failed: ' . $logEx->getMessage());
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil ditambahkan',
                    'event' => $event
                ]);
            }

            $this->clearDashboardCache();
            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil ditambahkan');
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
        $event = MongoEvent::findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            if ($event->banner_url) {
                $event->banner_url_full = image_url($event->banner_url);
            }
            if ($event->images && is_array($event->images)) {
                $event->images_url = array_map(function($img) {
                    return image_url($img);
                }, $event->images);
                $event->images_data = array_map(function($img) {
                    return [
                        'path' => $img,
                        'url' => image_url($img)
                    ];
                }, $event->images);
            }
            return response()->json($event);
        }

        if ($event->images && is_array($event->images)) {
            $event->images_data = array_map(function($img) {
                return [
                    'path' => $img,
                    'url' => image_url($img)
                ];
            }, $event->images);
        }

        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, string $id)
    {
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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,webp|max:10240',
            'banner' => 'nullable|image|mimes:jpeg,png,webp|max:10240',
            'schedule' => 'nullable|array',
            'schedule.*.time' => 'nullable|string',
            'schedule.*.activity' => 'nullable|string',
            'opening_hours' => 'nullable|string|max:255',
            'opening_hours_start' => 'nullable|date_format:H:i',
            'opening_hours_end' => 'nullable|date_format:H:i',
            'ticket_price' => 'nullable|string|max:255',
            'best_time' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $oldValues = $event->toArray();

            // Combine opening hours if separate fields are provided
            $openingHours = $validated['opening_hours'] ?? null;
            if (!$openingHours && !empty($validated['opening_hours_start']) && !empty($validated['opening_hours_end'])) {
                $openingHours = $validated['opening_hours_start'] . ' - ' . $validated['opening_hours_end'];
            }

            $event->name = $validated['name'];
            $event->category = $validated['category'];
            $event->location = $validated['location'];
            $event->organizer = $validated['organizer'] ?? $event->organizer;
            $event->description = $validated['description'];
            $event->long_description = $validated['long_description'] ?? $event->long_description;
            $event->start_date = $validated['start_date'];
            $event->end_date = $validated['end_date'];
            $event->opening_hours = $openingHours ?? $event->opening_hours;
            $event->ticket_price = $validated['ticket_price'] ?? $event->ticket_price;
            $event->best_time = $validated['best_time'] ?? $event->best_time;
            $event->latitude = isset($validated['latitude']) ? (float)$validated['latitude'] : $event->latitude;
            $event->longitude = isset($validated['longitude']) ? (float)$validated['longitude'] : $event->longitude;
            $event->is_active = $request->boolean('is_active');

            // Tags handling
            if ($request->has('tags')) {
                $event->tags = array_values(array_filter(array_map('trim', explode(',', $request->tags))));
            }

            // Schedule handling
            if ($request->has('schedule') && is_array($request->schedule)) {
                $event->schedule = array_values(array_filter($request->schedule, function($item) {
                    return !empty($item['activity']);
                }));
            }

            // Image handling
            $deleteImages = $request->input('delete_images', []);
            $existingImages = $event->images ?? [];
            if ($event->banner_url && empty($existingImages)) {
                $existingImages = [$event->banner_url];
            }

            if (!empty($deleteImages) && is_array($deleteImages)) {
                foreach ($deleteImages as $delImg) {
                    $this->deleteFile($delImg);
                    $existingImages = array_filter($existingImages, function($img) use ($delImg) {
                        return !$this->pathsMatch($img, $delImg);
                    });
                }
                $existingImages = array_values($existingImages);
            }

            $uploadedImages = [];
            if ($request->hasFile('banner')) {
                $path = $this->processImage($request->file('banner'), 'events');
                if ($path) {
                    if (count($existingImages) > 0) {
                        $this->deleteFile($existingImages[0]);
                        $existingImages[0] = $path;
                    } else {
                        array_unshift($existingImages, $path);
                    }
                }
            }
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $this->processImage($file, 'events');
                    if ($path) $uploadedImages[] = $path;
                }
                $existingImages = array_merge($existingImages, $uploadedImages);
            }

            if (count($existingImages) > 0) {
                $event->banner_url = $existingImages[0];
                $event->images = $existingImages;
            } else {
                $event->banner_url = null;
                $event->images = [];
            }

            // Slug update
            if ($event->isDirty('name')) {
                $event->slug = \Illuminate\Support\Str::slug($event->name) . '-' . \Illuminate\Support\Str::random(5);
            }
            
            $event->save();
            
            try {
                $this->logActivity('update', 'event', (string)$event->_id, $oldValues, $event->toArray());
            } catch (\Exception $logEx) {
                Log::warning('Event activity log failed: ' . $logEx->getMessage());
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil diperbarui',
                    'event' => $event
                ]);
            }

            $this->clearDashboardCache();
            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil diperbarui');
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
        try {
            $event = MongoEvent::findOrFail($id);
            
            // Log activity before deletion
            $this->logActivity('delete', 'event', (string)$event->_id, $event->toArray());
            
            $this->eventService->deleteEvent($event);

            $this->clearDashboardCache();
            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.events.index')
                ->with('error', 'Event tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus event: ' . $e->getMessage());
        }
    }
    /**
     * Toggle event status.
     */
    public function toggleStatus(string $id)
    {
        $event = MongoEvent::findOrFail($id);

        $oldStatus = $event->is_active;
        $this->eventService->toggleStatus($event);
        
        $this->logActivity('update_status', 'event', (string)$event->_id, 
            ['is_active' => $oldStatus], 
            ['is_active' => $event->is_active]
        );

        return back()->with('success', 'Status event berhasil diperbarui');
    }
}
