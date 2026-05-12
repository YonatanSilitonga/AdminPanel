<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoBeritaPromosi;
use App\Models\MongoDB\MongoBudaya;
use App\Models\MongoDB\CarouselBanner;
use App\Models\MongoDB\MongoFasilitasUmum;
use App\Models\MongoDB\MongoDestination;
use App\Models\MongoDB\MongoEvent;
use App\Models\MongoDB\MongoReport;
use App\Models\MongoDB\MongoReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GlobalSearchController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        
        if (empty($query)) {
            return view('admin.search.results', [
                'query' => $query,
                'results' => [],
                'totalCount' => 0
            ]);
        }

        $results = [];
        $regex = '/' . preg_quote((string) $query, '/') . '/i';

        // 1. Search Destinations
        $destinations = MongoDestination::where('name', 'regexp', $regex)
            ->orWhere('description', 'regexp', $regex)
            ->orWhere('category', 'regexp', $regex)
            ->orWhere('address', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($destinations as $item) {
            $results[] = [
                'type' => 'Destinasi',
                'title' => $item->name,
                'description' => \Illuminate\Support\Str::limit($item->description, 100),
                'url' => route('admin.destinations.index', ['search' => $item->name]),
                'icon' => '📍'
            ];
        }

        // 2. Search Events
        $events = MongoEvent::where('name', 'regexp', $regex)
            ->orWhere('description', 'regexp', $regex)
            ->orWhere('location', 'regexp', $regex)
            ->orWhere('category', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($events as $item) {
            $results[] = [
                'type' => 'Event',
                'title' => $item->name,
                'description' => \Illuminate\Support\Str::limit($item->description, 100),
                'url' => route('admin.events.index', ['search' => $item->name]),
                'icon' => '📅'
            ];
        }

        // 3. Search Budaya
        $budaya = MongoBudaya::where('name', 'regexp', $regex)
            ->orWhere('description', 'regexp', $regex)
            ->orWhere('category', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($budaya as $item) {
            $results[] = [
                'type' => 'Budaya',
                'title' => $item->name,
                'description' => \Illuminate\Support\Str::limit($item->description, 100),
                'url' => route('admin.budaya.index', ['search' => $item->name]),
                'icon' => '🏛️'
            ];
        }

        // 4. Search Berita & Promosi
        $berita = MongoBeritaPromosi::where('judul', 'regexp', $regex)
            ->orWhere('konten', 'regexp', $regex)
            ->orWhere('tipe', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($berita as $item) {
            $results[] = [
                'type' => 'Berita/Promosi',
                'title' => $item->judul,
                'description' => \Illuminate\Support\Str::limit($item->konten, 100),
                'url' => route('admin.berita_promosi.index', ['search' => $item->judul]),
                'icon' => '📰'
            ];
        }

        // 5. Search Fasilitas Umum
        $facilities = MongoFasilitasUmum::where('name', 'regexp', $regex)
            ->orWhere('type', 'regexp', $regex)
            ->orWhere('address', 'regexp', $regex)
            ->orWhere('description', 'regexp', $regex)
            ->orWhere('tags', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($facilities as $item) {
            $results[] = [
                'type' => 'Fasilitas Umum',
                'title' => $item->name,
                'description' => \Illuminate\Support\Str::limit(($item->address ?? $item->type ?? ''), 100),
                'url' => route('admin.fasilitas_umum.index', ['search' => $item->name]),
                'icon' => '🏪'
            ];
        }

        // 6. Search Carousel Banners
        $banners = CarouselBanner::where('title', 'regexp', $regex)
            ->orWhere('subtitle', 'regexp', $regex)
            ->orWhere('category_badge', 'regexp', $regex)
            ->orWhere('content_type', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($banners as $item) {
            $results[] = [
                'type' => 'Carousel',
                'title' => $item->title,
                'description' => \Illuminate\Support\Str::limit($item->subtitle ?? $item->category_badge ?? '', 100),
                'url' => route('admin.carousel_banners.index', ['search' => $item->title]),
                'icon' => '🖼️'
            ];
        }

        // 7. Search Reviews
        $reviews = MongoReview::where('review', 'regexp', $regex)
            ->orWhere('status', 'regexp', $regex)
            ->orWhere('sentiment_label', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($reviews as $item) {
            $results[] = [
                'type' => 'Ulasan',
                'title' => 'Review #' . (string) ($item->_id ?? '-'),
                'description' => \Illuminate\Support\Str::limit($item->review ?? '', 100),
                'url' => route('admin.reviews.index', ['search' => $item->review ?? '']),
                'icon' => '⭐'
            ];
        }

        // 8. Search Reports
        $reports = MongoReport::where('description', 'regexp', $regex)
            ->orWhere('reason', 'regexp', $regex)
            ->orWhere('status', 'regexp', $regex)
            ->orWhere('action_taken', 'regexp', $regex)
            ->orWhere('action_reason', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($reports as $item) {
            $this->pushResult(
                $results,
                'Laporan',
                $item->reason ?? 'Laporan',
                Str::limit($item->description ?? '', 100),
                route('admin.reports.index', ['search' => $item->reason ?? $item->description ?? '']),
                '⚠️'
            );
        }

        // 9. Search Users
        $users = User::where('name', 'regexp', $regex)
            ->orWhere('email', 'regexp', $regex)
            ->orWhere('role', 'regexp', $regex)
            ->limit(5)
            ->get();
        foreach ($users as $item) {
            $this->pushResult(
                $results,
                'User',
                $item->name,
                $item->email,
                route('admin.users.index', ['search' => $item->email]),
                '👤'
            );
        }

        $results = $this->deduplicateResults($results);
        $results = $this->sortResults($results, $query);

        return view('admin.search.results', [
            'query' => $query,
            'results' => $results,
            'totalCount' => count($results)
        ]);
    }

    /**
     * Add a search result to the accumulator.
     */
    private function pushResult(array &$results, string $type, string $title, string $description, string $url, string $icon): void
    {
        $results[] = [
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'icon' => $icon,
        ];
    }

    /**
     * Remove duplicate rows coming from overlapping field matches.
     */
    private function deduplicateResults(array $results): array
    {
        $unique = [];

        foreach ($results as $item) {
            $key = Str::lower($item['type'] . '|' . $item['title'] . '|' . $item['url']);
            $unique[$key] = $item;
        }

        return array_values($unique);
    }

    /**
     * Put exact title matches first, then keep module order stable.
     */
    private function sortResults(array $results, string $query): array
    {
        $normalizedQuery = Str::lower($query);
        $priority = [
            'Destinasi' => 1,
            'Event' => 2,
            'Budaya' => 3,
            'Berita/Promosi' => 4,
            'Fasilitas Umum' => 5,
            'Carousel' => 6,
            'Laporan' => 7,
            'Ulasan' => 8,
            'User' => 9,
        ];

        usort($results, function (array $left, array $right) use ($normalizedQuery, $priority): int {
            $leftExact = Str::lower($left['title']) === $normalizedQuery;
            $rightExact = Str::lower($right['title']) === $normalizedQuery;

            if ($leftExact !== $rightExact) {
                return $leftExact ? -1 : 1;
            }

            $leftPriority = $priority[$left['type']] ?? 99;
            $rightPriority = $priority[$right['type']] ?? 99;

            if ($leftPriority !== $rightPriority) {
                return $leftPriority <=> $rightPriority;
            }

            return Str::lower($left['title']) <=> Str::lower($right['title']);
        });

        return $results;
    }
}
