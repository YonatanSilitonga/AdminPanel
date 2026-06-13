<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDummyData extends Command
{
    protected $signature = 'admin:clean-dummy
                            {--dry-run : Preview saja, tidak hapus data}
                            {--collection=all : Pilih collection: all, destinations, budaya, events, fasilitas_umum, users, admins}';

    protected $description = 'Hapus data dummy dari seeder/factory testing. Gunakan --dry-run untuk preview dulu.';

    /**
     * URL placeholder yang hanya dipakai Faker/seeder, bukan upload asli.
     */
    private array $dummyImagePatterns = [
        'via.placeholder.com',
        'placehold.co',
        'placehold.it',
        'picsum.photos',
        'lorempixel.com',
        'dummyimage.com',
        'placeimg.com',
        'fakeimg.pl',
    ];

    public function handle(): int
    {
        $isDryRun   = $this->option('dry-run');
        $collection = $this->option('collection');

        $this->newLine();
        $mode = $isDryRun ? '[DRY-RUN — tidak ada yang dihapus]' : '[MODE HAPUS AKTIF]';
        $this->info("🧹 Clean Dummy Data  {$mode}");
        $this->newLine();

        if (!$isDryRun) {
            if (!$this->confirm('⚠️  Data yang dihapus TIDAK BISA dikembalikan. Lanjutkan?')) {
                $this->info('Dibatalkan.');
                return 0;
            }
        }

        $targets = $collection === 'all'
            ? ['destinations', 'budaya', 'events', 'fasilitas_umum', 'users', 'admins']
            : [$collection];

        $totalFound   = 0;
        $totalDeleted = 0;

        foreach ($targets as $target) {
            [$found, $deleted] = $this->processCollection($target, $isDryRun);
            $totalFound   += $found;
            $totalDeleted += $deleted;
            $this->newLine();
        }

        $this->line('─────────────────────────────────────────────');
        if ($isDryRun) {
            $this->warn("DRY-RUN — Total ditemukan: {$totalFound} data dummy");
            $this->line('Jalankan tanpa --dry-run untuk menghapus:');
            $this->line('  php artisan admin:clean-dummy');
        } else {
            $this->info("✅ Total dihapus: {$totalDeleted} data dummy");
        }
        $this->newLine();

        return 0;
    }

    private function processCollection(string $name, bool $isDryRun): array
    {
        return match ($name) {
            'destinations'  => $this->cleanDestinations($isDryRun),
            'budaya'        => $this->cleanBudaya($isDryRun),
            'events'        => $this->cleanEvents($isDryRun),
            'fasilitas_umum'=> $this->cleanFasilitasUmum($isDryRun),
            'users'         => $this->cleanUsers($isDryRun),
            'admins'        => $this->cleanAdmins($isDryRun),
            default         => [0, 0],
        };
    }

    // ── Helper: query MongoDB, normalisasi _id ke string ─────────────────────

    private function mongoGet(string $col, callable $buildQuery): \Illuminate\Support\Collection
    {
        $query = DB::connection('mongodb')->table($col);
        $buildQuery($query);
        return collect($query->get())->map(function ($row) {
            $arr = (array) $row;
            // Raw query mengembalikan 'id', Eloquent mengembalikan '_id'
            $rawId = $arr['_id'] ?? $arr['id'] ?? null;
            $arr['_id'] = $rawId ? (string) $rawId : null;
            return $arr;
        });
    }

    private function mongoDelete(string $col, array $ids): int
    {
        if (empty($ids)) return 0;
        return (int) DB::connection('mongodb')->table($col)->whereIn('_id', $ids)->delete();
    }

    private function buildImageFilter($query, array $fields): void
    {
        $query->where(function ($q) use ($fields) {
            foreach ($this->dummyImagePatterns as $pattern) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'regexp', "/{$pattern}/i");
                }
            }
        });
    }

    private function previewRows(\Illuminate\Support\Collection $rows, int $limit = 3): void
    {
        $rows->take($limit)->each(function ($d) {
            $id   = $d['_id'] ?? '(?)';
            $name = $d['name'] ?? $d['email'] ?? $d['judul'] ?? '-';
            $extra = '';
            if (!empty($d['thumbnail_url'])) {
                $extra = '  thumb="' . substr($d['thumbnail_url'], 0, 55) . '"';
            } elseif (!empty($d['email'])) {
                $extra = '  email=' . $d['email'];
            }
            $this->line("  → {$id}  \"{$name}\"{$extra}");
        });
        if ($rows->count() > $limit) {
            $this->line('  → ... dan ' . ($rows->count() - $limit) . ' lainnya');
        }
    }

    private function deleteAndReport(string $col, \Illuminate\Support\Collection $rows, bool $isDryRun): array
    {
        $count = $rows->count();
        $this->line("  Ditemukan: {$count} data dummy");
        $this->previewRows($rows);

        if ($isDryRun || $count === 0) {
            return [$count, 0];
        }

        $ids     = $rows->pluck('_id')->filter()->values()->toArray();
        $deleted = $this->mongoDelete($col, $ids);
        $this->info("  ✓ Dihapus: {$deleted}");

        return [$count, $deleted];
    }

    // ── Destinations ──────────────────────────────────────────────────────────

    private function cleanDestinations(bool $isDryRun): array
    {
        $this->line('📍 destinations');

        $byImage = $this->mongoGet('destinations', function ($q) {
            $this->buildImageFilter($q, ['thumbnail_url', 'cover_url']);
        });

        $byName = $this->mongoGet('destinations', function ($q) {
            $q->where(function ($inner) {
                $inner->orWhere('name', 'regexp', '/^Destination Test /i')
                      ->orWhere('name', 'regexp', '/^Destination \d+$/i');
            });
        });

        $allDummy = $byImage->merge($byName)->unique('_id');
        return $this->deleteAndReport('destinations', $allDummy, $isDryRun);
    }

    // ── Budaya ────────────────────────────────────────────────────────────────

    private function cleanBudaya(bool $isDryRun): array
    {
        $this->line('🎭 budaya');

        $byImage = $this->mongoGet('budaya', function ($q) {
            $this->buildImageFilter($q, ['thumbnail_url', 'image_url']);
        });

        $byName = $this->mongoGet('budaya', function ($q) {
            $q->where('name', 'regexp', '/^Budaya Test /i');
        });

        $allDummy = $byImage->merge($byName)->unique('_id');
        return $this->deleteAndReport('budaya', $allDummy, $isDryRun);
    }

    // ── Events ────────────────────────────────────────────────────────────────

    private function cleanEvents(bool $isDryRun): array
    {
        $this->line('📅 events');

        // Pola dari PerformanceTestSeeder: "Event Test {n}"
        // Pola dari DatabaseSeeder: "Event {n} at {destination}"
        // Pola dari seeder lain: "Test Event {n}"
        $byName = $this->mongoGet('events', function ($q) {
            $q->where(function ($inner) {
                $inner->orWhere('name', 'regexp', '/^Event Test \d+/i')
                      ->orWhere('name', 'regexp', '/^Test Event \d+/i')
                      ->orWhere('name', 'regexp', '/^Event \d+ at /i')
                      ->orWhere('slug', 'regexp', '/^test-event-\d+-\d+$/i')
                      ->orWhere('description', 'regexp', '/^Event \d+:/i');
            });
        });

        $byImage = $this->mongoGet('events', function ($q) {
            $this->buildImageFilter($q, ['banner_url', 'thumbnail_url', 'images']);
        });

        // Soft-deleted dummy (deleted_at != null AND nama cocok pola)
        $allDummy = $byName->merge($byImage)->unique('_id');

        // Sertakan juga yang sudah soft-deleted tapi belum force-deleted
        $softDeleted = $this->mongoGet('events', function ($q) {
            $q->whereNotNull('deleted_at')
              ->where(function ($inner) {
                  $inner->orWhere('name', 'regexp', '/^Event Test \d+/i')
                        ->orWhere('name', 'regexp', '/^Test Event \d+/i')
                        ->orWhere('name', 'regexp', '/^Event \d+ at /i')
                        ->orWhere('slug', 'regexp', '/^test-event-\d+-\d+$/i');
              });
        });

        $allDummy = $allDummy->merge($softDeleted)->unique('_id');
        return $this->deleteAndReport('events', $allDummy, $isDryRun);
    }

    // ── Fasilitas Umum ────────────────────────────────────────────────────────

    private function cleanFasilitasUmum(bool $isDryRun): array
    {
        $this->line('🏥 fasilitas_umum');

        // Pola PerformanceTestSeeder: name="Fasilitas {n}", address="Jl. Test {n}, ..."
        // atau yang menggunakan nama "Fasilitas Umum Test {n}"
        $byName = $this->mongoGet('fasilitas_umum', function ($q) {
            $q->where(function ($inner) {
                $inner->orWhere('name', 'regexp', '/^Fasilitas \d+$/i')
                      ->orWhere('name', 'regexp', '/^Fasilitas Umum Test /i')
                      ->orWhere('address', 'regexp', '/^Jl\. Test \d+/i');
            });
        });

        // Soft-deleted fasilitas dummy
        $softDeleted = $this->mongoGet('fasilitas_umum', function ($q) {
            $q->whereNotNull('deleted_at')
              ->where(function ($inner) {
                  $inner->orWhere('name', 'regexp', '/^Fasilitas \d+$/i')
                        ->orWhere('address', 'regexp', '/^Jl\. Test \d+/i');
              });
        });

        $allDummy = $byName->merge($softDeleted)->unique('_id');
        return $this->deleteAndReport('fasilitas_umum', $allDummy, $isDryRun);
    }

    // ── Users ─────────────────────────────────────────────────────────────────

    private function cleanUsers(bool $isDryRun): array
    {
        $this->line('👤 users');

        // Pola dari PerformanceTestSeeder: testuser{n}@example.com
        $byPerf = $this->mongoGet('users', function ($q) {
            $q->where('email', 'regexp', '/^testuser\d+@example\.com$/i');
        });

        // Pola manual test: test@example.com, firstName="Test", lastName="User"
        $byManual = $this->mongoGet('users', function ($q) {
            $q->where(function ($inner) {
                $inner->orWhere('email', 'test@example.com')
                      ->orWhere(function ($i2) {
                          $i2->where('firstName', 'Test')
                             ->where('lastName', 'User');
                      });
            });
        });

        // Pola API test: api_login_*@example.com, api_*@example.com, dll
        // Semua email dengan domain @example.com adalah dummy (domain reserved untuk testing)
        $byExampleDomain = $this->mongoGet('users', function ($q) {
            $q->where('email', 'regexp', '/@example\.com$/i');
        });

        $allDummy = $byPerf->merge($byManual)->merge($byExampleDomain)->unique('_id');
        return $this->deleteAndReport('users', $allDummy, $isDryRun);
    }

    // ── Admins ────────────────────────────────────────────────────────────────

    private function cleanAdmins(bool $isDryRun): array
    {
        $this->line('🔑 admins');

        // CATATAN: superadmin@smarttourism.local adalah akun ASLI dari AdminSeeder, JANGAN DIHAPUS
        // Hanya hapus admin dari Factory/Testing yang punya password bcrypt cost 04 ($2y$04$)
        // atau email testadmin/dummyadmin
        $byFactory = $this->mongoGet('admins', function ($q) {
            $q->where(function ($inner) {
                $inner->orWhere('password', 'regexp', '/^\$2[ay]\$04\$/')
                      ->orWhere('email', 'regexp', '/^(test|dummy)admin/i');
            })->where('email', '!=', 'superadmin@smarttourism.local'); // PENTING: jangan hapus admin asli
        });

        $allDummy = $byFactory;

        $count = $allDummy->count();
        $this->line("  Ditemukan: {$count} akun admin dummy");
        $this->previewRows($allDummy);

        if ($isDryRun || $count === 0) {
            return [$count, 0];
        }

        $ids     = $allDummy->pluck('_id')->filter()->values()->toArray();
        $deleted = $this->mongoDelete('admins', $ids);
        $this->info("  ✓ Dihapus: {$deleted}");

        return [$count, $deleted];
    }
}
