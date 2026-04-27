<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\MongoDB\MongoBeritaPromosi;
use App\Models\MongoDB\MongoBudaya;
use App\Models\MongoDB\MongoDestination;
use App\Models\MongoDB\MongoEvent;
use App\Models\User;
use Illuminate\Http\Request;

class GlobalSearchController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return view('admin.search.results', [
                'query' => $query,
                'results' => [],
                'totalCount' => 0
            ]);
        }

        $results = [];

        // 1. Search Destinations
        $destinations = MongoDestination::where('name', 'regexp', "/{$query}/i")
            ->orWhere('description', 'regexp', "/{$query}/i")
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
        $events = MongoEvent::where('name', 'regexp', "/{$query}/i")
            ->orWhere('description', 'regexp', "/{$query}/i")
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
        $budaya = MongoBudaya::where('name', 'regexp', "/{$query}/i")
            ->orWhere('description', 'regexp', "/{$query}/i")
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
        $berita = MongoBeritaPromosi::where('judul', 'regexp', "/{$query}/i")
            ->orWhere('konten', 'regexp', "/{$query}/i")
            ->limit(5)
            ->get();
        foreach ($berita as $item) {
            $results[] = [
                'type' => 'Berita',
                'title' => $item->judul,
                'description' => \Illuminate\Support\Str::limit($item->konten, 100),
                'url' => route('admin.berita_promosi.index', ['search' => $item->judul]),
                'icon' => '📰'
            ];
        }

        // 5. Search Users (MySQL)
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(5)
            ->get();
        foreach ($users as $item) {
            $results[] = [
                'type' => 'User',
                'title' => $item->name,
                'description' => $item->email,
                'url' => route('admin.users.index', ['search' => $item->email]),
                'icon' => '👤'
            ];
        }

        return view('admin.search.results', [
            'query' => $query,
            'results' => $results,
            'totalCount' => count($results)
        ]);
    }
}
