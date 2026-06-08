<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\Driver\Exception\Exception as MongoException;

class CreateMongoIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create-mongo-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create optimized MongoDB indexes for all collections to speed up Laravel admin performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting MongoDB Index Creation...');

        $dbConnection = DB::connection('mongodb');
        
        // Ensure connection is established
        try {
            $dbConnection->getMongoDB()->listCollections();
        } catch (\Exception $e) {
            $this->error('Failed to connect to MongoDB: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $definitions = [
            'events' => [
                ['keys' => ['created_at' => -1]],
                ['keys' => ['start_date' => 1]],
                ['keys' => ['end_date' => 1]],
                ['keys' => ['is_active' => 1]],
                ['keys' => ['category' => 1]],
                ['keys' => ['slug' => 1], 'options' => ['unique' => true]],
                ['keys' => ['is_active' => 1, 'category' => 1, 'created_at' => -1]],
            ],
            'destinations' => [
                ['keys' => ['created_at' => -1]],
                ['keys' => ['category' => 1]],
                ['keys' => ['is_active' => 1]],
                ['keys' => ['name' => 1]],
            ],
            'ratings' => [
                ['keys' => ['created_at' => -1]],
                ['keys' => ['status' => 1]],
                ['keys' => ['destination_id' => 1]],
                ['keys' => ['user_id' => 1]],
                ['keys' => ['status' => 1, 'created_at' => -1]],
            ],
            'reports' => [
                ['keys' => ['created_at' => -1]],
                ['keys' => ['status' => 1]],
                ['keys' => ['destination_id' => 1]],
                ['keys' => ['user_id' => 1]],
                ['keys' => ['status' => 1, 'created_at' => -1]],
            ],
            'chat_sessions' => [
                ['keys' => ['updated_at' => -1]],
                ['keys' => ['user_id' => 1]],
            ],
            'recommendation_logs' => [
                ['keys' => ['created_at' => -1]],
                ['keys' => ['user_id' => 1]],
            ],
            'admin_activity_logs' => [
                ['keys' => ['created_at' => -1]],
                ['keys' => ['admin_id' => 1]],
            ],
            'berita_promosi' => [
                ['keys' => ['created_at' => -1]],
                ['keys' => ['is_active' => 1]],
            ],
            'users' => [
                ['keys' => ['email' => 1], 'options' => ['unique' => true]],
                ['keys' => ['is_active' => 1]],
                ['keys' => ['created_at' => -1]],
            ],
        ];

        foreach ($definitions as $collectionName => $indexes) {
            $this->comment("Creating indexes for collection: [{$collectionName}]...");
            
            try {
                $collection = $dbConnection->getCollection($collectionName);
                
                // Get existing index names
                $existingIndexNames = [];
                try {
                    foreach ($collection->listIndexes() as $indexInfo) {
                        $existingIndexNames[] = $indexInfo->getName();
                    }
                } catch (\Exception $ex) {
                    // Collection might not exist yet, we will auto-create it by running index
                }

                foreach ($indexes as $indexDef) {
                    $keys = $indexDef['keys'];
                    $options = $indexDef['options'] ?? [];
                    
                    // Build a standard index name to check if it already exists
                    $nameParts = [];
                    foreach ($keys as $k => $v) {
                        $nameParts[] = $k . '_' . ($v === -1 ? '2dsphere' === $v ? '2dsphere' : 'desc' : 'asc');
                    }
                    $tempName = implode('_', $nameParts);
                    
                    // Generate default driver name or check keys
                    $driverIndexName = '';
                    $keyStringParts = [];
                    foreach ($keys as $k => $v) {
                        $keyStringParts[] = "{$k}_{$v}";
                    }
                    $driverIndexName = implode('_', $keyStringParts);

                    // Skip if index name or driver representation already exists
                    $alreadyExists = false;
                    foreach ($existingIndexNames as $name) {
                        if ($name === $driverIndexName || str_contains($name, $tempName) || $name === ($options['name'] ?? '')) {
                            $alreadyExists = true;
                            break;
                        }
                    }

                    if ($alreadyExists) {
                        $this->line("  - Index for keys " . json_encode($keys) . " already exists. Skipping.");
                        continue;
                    }

                    $indexName = $collection->createIndex($keys, $options);
                    $this->info("  - Created index: {$indexName}");
                }
            } catch (MongoException $e) {
                $this->error("  - Failed to create indexes for [{$collectionName}]: " . $e->getMessage());
            } catch (\Exception $e) {
                $this->error("  - Error processing [{$collectionName}]: " . $e->getMessage());
            }
        }

        $this->info('MongoDB Index Creation Completed Successfully.');
        return Command::SUCCESS;
    }
}
