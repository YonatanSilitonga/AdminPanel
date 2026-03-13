<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\MongoDB\MongoEvent;
use App\Models\MongoDB\MongoDestination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class EventService
{
    /**
     * Get paginated events with optional filters
     */
    public function getPaginatedEvents(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = MongoEvent::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool)$filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create a new event
     */
    public function createEvent(array $data, ?UploadedFile $banner = null): MongoEvent
    {
        if ($banner) {
            $data['banner_url'] = $this->uploadBanner($banner);
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        }

        $data['admin_id'] = auth('admin')->id();

        return MongoEvent::create($data);
    }

    /**
     * Update an existing event
     */
    public function updateEvent(MongoEvent $event, array $data, ?UploadedFile $banner = null): MongoEvent
    {
        if ($banner) {
            // Delete old banner
            $this->deleteBanner($event->banner_url);
            $data['banner_url'] = $this->uploadBanner($banner);
        }

        if (!empty($data['name']) && $data['name'] !== $event->name && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        }

        $event->update($data);
        return $event;
    }

    /**
     * Delete an event
     */
    public function deleteEvent(MongoEvent $event): bool
    {
        $this->deleteBanner($event->banner_url);
        return $event->delete();
    }

    /**
     * Toggle event status
     */
    public function toggleStatus(MongoEvent $event): bool
    {
        $event->is_active = !$event->is_active;
        return $event->save();
    }

    /**
     * Upload banner image
     */
    public function uploadBanner(UploadedFile $file): string
    {
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('events', $filename, 'public');
    }

    /**
     * Delete banner image
     */
    public function deleteBanner(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
