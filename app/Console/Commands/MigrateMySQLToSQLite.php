<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateMySQLToSQLite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:mysql-to-sqlite 
                            {--connection=mysql_production : MySQL connection name}
                            {--tables=coinbases : Comma-separated list of tables to migrate (default: coinbases)}
                            {--chunk-size=1000 : Number of records to fetch per chunk from MySQL}
                            {--insert-batch=500 : Number of records to insert per batch into SQLite}
                            {--from-date= : Migrate from specific date (format: YYYY-MM-DD)}
                            {--to-date= : Migrate to specific date (format: YYYY-MM-DD)}
                            {--append : Append data instead of truncating existing data}
                            {--validate : Run validation after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate coinbases table data from MySQL production database to SQLite. Automatically converts UTC timestamps to America/Detroit timezone (handles DST).';

    /**
     * Tables to migrate (in order)
     * Default to coinbases table only
     */
    protected $tablesToMigrate = [
        'coinbases',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting MySQL to SQLite migration...');

        // Check if SQLite database exists
        $sqlitePath = database_path('database.sqlite');
        if (!file_exists($sqlitePath)) {
            $this->error('SQLite database not found at: ' . $sqlitePath);
            $this->info('Creating SQLite database...');
            touch($sqlitePath);
            $this->info('SQLite database created. Please run migrations first: php artisan migrate');
            return 1;
        }

        // Backup existing SQLite database
        $this->info('Creating backup of existing SQLite database...');
        $backupPath = database_path('database.sqlite.backup.' . date('Y-m-d_His'));
        if (!copy($sqlitePath, $backupPath)) {
            $this->error('Failed to create backup!');
            return 1;
        }
        $this->info('Backup created: ' . $backupPath);

        // Migrate from MySQL connection
        return $this->migrateFromConnection($this->option('connection'));
    }

    /**
     * Migrate from MySQL connection
     */
    protected function migrateFromConnection($connectionName)
    {
        try {
            // Test connection
            $this->info("Testing MySQL connection: {$connectionName}");
            DB::connection($connectionName)->getPdo();
            $this->info('✓ MySQL connection successful');
        } catch (\Exception $e) {
            $this->error('Failed to connect to MySQL: ' . $e->getMessage());
            $this->info('Tip: Use SSH tunnel: ssh -L 3307:127.0.0.1:3306 user@droplet-ip');
            $this->info('Then set PRODUCTION_DB_HOST=127.0.0.1 and PRODUCTION_DB_PORT=3307');
            return 1;
        }

        // Get tables to migrate
        $tables = $this->getTablesToMigrate();

        // Migrate each table
        foreach ($tables as $table) {
            if (!$this->tableExists($connectionName, $table)) {
                $this->warn("Table '{$table}' does not exist in MySQL, skipping...");
                continue;
            }

            $this->info("\nMigrating table: {$table}");
            $this->migrateTable($connectionName, $table);
        }

        $this->info("\n✓ Migration completed successfully!");

        if ($this->option('validate')) {
            $this->call('migrate:validate', ['--source' => $connectionName]);
        }

        return 0;
    }

    /**
     * Get list of tables to migrate
     */
    protected function getTablesToMigrate()
    {
        if ($this->option('tables')) {
            return array_map('trim', explode(',', $this->option('tables')));
        }

        return $this->tablesToMigrate;
    }

    /**
     * Check if table exists in MySQL
     */
    protected function tableExists($connection, $table)
    {
        try {
            $database = config("database.connections.{$connection}.database");
            $result = DB::connection($connection)->select(
                "SELECT COUNT(*) as count FROM information_schema.tables 
                 WHERE table_schema = ? AND table_name = ?",
                [$database, $table]
            );
            return $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if table has a specific column
     */
    protected function hasColumn($connection, $table, $columnName)
    {
        try {
            $database = config("database.connections.{$connection}.database");
            $result = DB::connection($connection)->select(
                "SELECT COUNT(*) as count FROM information_schema.columns 
                 WHERE table_schema = ? AND table_name = ? AND column_name = ?",
                [$database, $table, $columnName]
            );
            return $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Migrate a single table
     */
    protected function migrateTable($sourceConnection, $table)
    {
        try {
            // Build base query with date filters if specified
            $baseQuery = DB::connection($sourceConnection)->table($table);
            $dateFilter = $this->buildDateFilter($baseQuery, $table);
            
            // Get total count with date filters applied
            $totalCount = (clone $baseQuery)->count();
            $this->info("  Total records: {$totalCount}");
            
            // Show date filter info if applied
            if ($dateFilter) {
                $this->info("  Date filter: {$dateFilter}");
            }
            
            // Timezone conversion is always applied for coinbases
            if ($table === 'coinbases') {
                $this->info("  Converting UTC timestamps to America/Detroit (handles DST)");
            }
            
            // For coinbases table, show additional info about data distribution
            if ($table === 'coinbases') {
                try {
                    $dateRange = (clone $baseQuery)
                        ->selectRaw('MIN(created_at) as earliest, MAX(created_at) as latest')
                        ->first();
                    if ($dateRange && $dateRange->earliest) {
                        $this->info("  Date range: {$dateRange->earliest} to {$dateRange->latest}");
                    }
                } catch (\Exception $e) {
                    // Ignore if query fails
                }
            }

            if ($totalCount === 0) {
                $this->info("  No records to migrate, skipping...");
                return;
            }

            // Check if table exists in SQLite
            if (!Schema::hasTable($table)) {
                $this->warn("  Table '{$table}' does not exist in SQLite. Please run migrations first.");
                return;
            }

            // Clear existing data unless appending
            if (!$this->option('append')) {
                DB::table($table)->truncate();
                $this->info("  Cleared existing data in SQLite table");
            } else {
                $this->info("  Appending to existing data in SQLite table");
            }

            // Get chunk sizes
            $chunkSize = (int) $this->option('chunk-size');
            $insertBatchSize = (int) $this->option('insert-batch');

            // Create progress bar
            $bar = $this->output->createProgressBar($totalCount);
            $bar->setFormat('  %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
            $bar->start();

            // Use cursor-based chunking for better performance
            $migrated = 0;

            // For coinbases table, use created_at for chronological ordering
            // For other tables, use id if available
            $useDateCursor = ($table === 'coinbases' && $this->hasColumn($sourceConnection, $table, 'created_at'));
            $hasIdColumn = $this->hasColumn($sourceConnection, $table, 'id');

            if ($useDateCursor) {
                // Use date-based cursor chunking for coinbases (chronological order)
                $lastDate = null;
                $lastId = 0;
                while (true) {
                    $query = DB::connection($sourceConnection)
                        ->table($table)
                        ->orderBy('created_at')
                        ->orderBy('id'); // Secondary sort for consistency

                    // Apply date filters if specified
                    $this->applyDateFilters($query, $table);

                    if ($lastDate !== null) {
                        $query->where(function($q) use ($lastDate, $lastId) {
                            $q->where('created_at', '>', $lastDate)
                              ->orWhere(function($subQ) use ($lastDate, $lastId) {
                                  $subQ->where('created_at', '=', $lastDate)
                                       ->where('id', '>', $lastId);
                              });
                        });
                    }

                    $records = $query->limit($chunkSize)->get();

                    if ($records->isEmpty()) {
                        break;
                    }

                    // Convert and insert in batches
                    $convertedRecords = [];
                    foreach ($records as $record) {
                        $convertedRecords[] = $this->convertRecordForSQLite((array) $record, $table);
                        $lastDate = $record->created_at;
                        $lastId = $record->id;
                    }

                    $migrated += $this->insertRecords($table, $convertedRecords, $insertBatchSize, $bar);

                    // Free memory
                    unset($records, $convertedRecords);
                }
            } elseif ($hasIdColumn) {
                // Use ID-based cursor chunking for other tables
                $lastId = 0;
                while (true) {
                    $query = DB::connection($sourceConnection)
                        ->table($table)
                        ->where('id', '>', $lastId)
                        ->orderBy('id')
                        ->limit($chunkSize);

                    // Apply date filters if specified
                    $this->applyDateFilters($query, $table);

                    $records = $query->get();

                    if ($records->isEmpty()) {
                        break;
                    }

                    // Convert and insert in batches
                    $convertedRecords = [];
                    foreach ($records as $record) {
                        $convertedRecords[] = $this->convertRecordForSQLite((array) $record, $table);
                        $lastId = $record->id;
                    }

                    $migrated += $this->insertRecords($table, $convertedRecords, $insertBatchSize, $bar);

                    // Free memory
                    unset($records, $convertedRecords);
                }
            } else {
                // Fallback to offset-based chunking for tables without ID column
                $offset = 0;
                while ($offset < $totalCount) {
                    $query = DB::connection($sourceConnection)
                        ->table($table)
                        ->offset($offset)
                        ->limit($chunkSize);

                    // Apply date filters if specified
                    $this->applyDateFilters($query, $table);

                    $records = $query->get();

                    if ($records->isEmpty()) {
                        break;
                    }

                    // Convert records for SQLite
                    $convertedRecords = [];
                    foreach ($records as $record) {
                        $convertedRecords[] = $this->convertRecordForSQLite((array) $record, $table);
                    }

                    $migrated += $this->insertRecords($table, $convertedRecords, $insertBatchSize, $bar);

                    $offset += $chunkSize;
                    
                    // Free memory
                    unset($records, $convertedRecords);
                }
            }

            $bar->finish();
            $this->newLine();
            $this->info("  ✓ Migrated {$migrated} records");

        } catch (\Exception $e) {
            $this->newLine();
            $this->error("  ✗ Failed to migrate table '{$table}': " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Convert a record for SQLite compatibility
     * Always converts UTC timestamps to America/Detroit for coinbases table
     */
    protected function convertRecordForSQLite($record, $table)
    {
        $converted = (array) $record;

        // Handle timestamps - convert to ISO 8601 format for SQLite
        foreach (['created_at', 'updated_at', 'deleted_at', 'email_verified_at', 'failed_at'] as $field) {
            if (isset($converted[$field]) && $converted[$field] !== null) {
                // If it's already a string, try to parse and reformat
                if (is_string($converted[$field])) {
                    try {
                        // Parse as UTC (production server stores in UTC)
                        $date = new \DateTime($converted[$field], new \DateTimeZone('UTC'));
                        
                        // Always convert UTC to America/Detroit for coinbases created_at (handles DST automatically)
                        if ($field === 'created_at' && $table === 'coinbases') {
                            $date->setTimezone(new \DateTimeZone('America/Detroit'));
                        }
                        
                        $converted[$field] = $date->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        // Keep original if parsing fails
                    }
                } elseif ($converted[$field] instanceof \DateTime) {
                    $date = clone $converted[$field];
                    
                    // Ensure it's in UTC first
                    if ($date->getTimezone()->getName() !== 'UTC') {
                        $date->setTimezone(new \DateTimeZone('UTC'));
                    }
                    
                    // Always convert UTC to America/Detroit for coinbases created_at (handles DST automatically)
                    if ($field === 'created_at' && $table === 'coinbases') {
                        $date->setTimezone(new \DateTimeZone('America/Detroit'));
                    }
                    
                    $converted[$field] = $date->format('Y-m-d H:i:s');
                }
            }
        }

        // Ensure float values are properly formatted
        if (isset($converted['amount']) && is_numeric($converted['amount'])) {
            $converted['amount'] = (float) $converted['amount'];
        }

        // Remove any null bytes or problematic characters
        foreach ($converted as $key => $value) {
            if (is_string($value)) {
                $converted[$key] = str_replace("\0", '', $value);
            }
        }

        return $converted;
    }

    /**
     * Build date filter description and apply to query
     */
    protected function buildDateFilter($query, $table)
    {
        if ($table !== 'coinbases') {
            return null;
        }

        $fromDate = $this->option('from-date');
        $toDate = $this->option('to-date');

        if ($fromDate || $toDate) {
            if ($fromDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) {
                $query->where('created_at', '>=', "{$fromDate} 00:00:00");
            }
            if ($toDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate)) {
                $query->where('created_at', '<=', "{$toDate} 23:59:59");
            }
            
            $filterParts = [];
            if ($fromDate) $filterParts[] = "From: {$fromDate}";
            if ($toDate) $filterParts[] = "To: {$toDate}";
            return implode(', ', $filterParts);
        }

        return null;
    }

    /**
     * Apply date filters to query (used in chunking loops)
     */
    protected function applyDateFilters($query, $table)
    {
        if ($table !== 'coinbases') {
            return;
        }

        $fromDate = $this->option('from-date');
        $toDate = $this->option('to-date');

        if ($fromDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) {
            $query->where('created_at', '>=', "{$fromDate} 00:00:00");
        }

        if ($toDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate)) {
            $query->where('created_at', '<=', "{$toDate} 23:59:59");
        }
    }

    /**
     * Insert records in batches with progress tracking and duplicate checking
     */
    protected function insertRecords($table, $convertedRecords, $insertBatchSize, $progressBar)
    {
        $inserted = 0;
        $insertChunks = array_chunk($convertedRecords, $insertBatchSize);
        
        foreach ($insertChunks as $chunk) {
            try {
                // Filter out duplicates before inserting
                $uniqueRecords = $this->filterDuplicates($table, $chunk);
                
                if (empty($uniqueRecords)) {
                    // All records in this chunk are duplicates, skip
                    $progressBar->advance(count($chunk));
                    continue;
                }
                
                // Use insertOrIgnore as a safety net in case of race conditions
                // or if duplicates slip through
                try {
                    DB::table($table)->insert($uniqueRecords);
                } catch (\Illuminate\Database\QueryException $e) {
                    // If unique constraint violation, try inserting one by one
                    // This handles edge cases where duplicates exist
                    $actuallyInserted = 0;
                    foreach ($uniqueRecords as $record) {
                        try {
                            DB::table($table)->insert($record);
                            $actuallyInserted++;
                        } catch (\Illuminate\Database\QueryException $insertException) {
                            // Skip duplicates that slip through
                            if (!$this->isUniqueConstraintError($insertException)) {
                                throw $insertException;
                            }
                        }
                    }
                    $inserted += $actuallyInserted;
                    $progressBar->advance(count($chunk));
                    continue;
                }
                
                $inserted += count($uniqueRecords);
                $progressBar->advance(count($chunk)); // Advance by chunk size for progress accuracy
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Error inserting batch: " . $e->getMessage());
                $this->error("  Record: " . json_encode($chunk[0] ?? []));
                throw $e;
            }
        }
        
        return $inserted;
    }

    /**
     * Filter out records that already exist in SQLite
     * Also removes duplicates within the batch itself
     */
    protected function filterDuplicates($table, $records)
    {
        if ($table === 'coinbases') {
            // First, deduplicate within the batch itself by created_at
            $seen = [];
            $deduplicated = [];
            foreach ($records as $record) {
                $key = $record['created_at'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $deduplicated[] = $record;
                }
            }
            
            // Then check against database
            $uniqueRecords = [];
            foreach ($deduplicated as $record) {
                $exists = DB::table($table)
                    ->where('created_at', $record['created_at'])
                    ->exists();
                
                if (!$exists) {
                    $uniqueRecords[] = $record;
                }
            }
            return $uniqueRecords;
        }
        
        // For other tables, check by id
        // First, deduplicate within the batch itself by id
        $seen = [];
        $deduplicated = [];
        foreach ($records as $record) {
            if (!isset($record['id'])) {
                // No id field, include it (can't deduplicate)
                $deduplicated[] = $record;
                continue;
            }
            
            $key = $record['id'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $deduplicated[] = $record;
            }
        }
        
        // Then check against database
        $uniqueRecords = [];
        foreach ($deduplicated as $record) {
            if (!isset($record['id'])) {
                // No id field, can't check for duplicates in DB
                $uniqueRecords[] = $record;
                continue;
            }
            
            $exists = DB::table($table)->where('id', $record['id'])->exists();
            if (!$exists) {
                $uniqueRecords[] = $record;
            }
        }
        
        return $uniqueRecords;
    }

    /**
     * Check if exception is a unique constraint violation
     */
    protected function isUniqueConstraintError(\Exception $e)
    {
        if ($e instanceof \Illuminate\Database\QueryException) {
            $message = $e->getMessage();
            // SQLite unique constraint error messages
            return str_contains($message, 'UNIQUE constraint failed') || 
                   str_contains($message, 'unique constraint') ||
                   str_contains($message, 'UNIQUE');
        }
        return false;
    }
}
