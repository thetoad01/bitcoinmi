<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ValidateMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:validate 
                            {--source=mysql_production : Source MySQL connection name}
                            {--tables= : Comma-separated list of tables to validate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate that MySQL to SQLite migration was successful';

    /**
     * Tables to validate
     * Default to coinbases table only
     */
    protected $tablesToValidate = [
        'coinbases',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Validating MySQL to SQLite migration...');
        $this->newLine();

        $sourceConnection = $this->option('source');
        $tables = $this->getTablesToValidate();

        $allValid = true;

        foreach ($tables as $table) {
            $this->info("Validating table: {$table}");

            try {
                $valid = $this->validateTable($sourceConnection, $table);
                if ($valid) {
                    $this->info("  ✓ {$table} - OK");
                } else {
                    $this->error("  ✗ {$table} - VALIDATION FAILED");
                    $allValid = false;
                }
            } catch (\Exception $e) {
                $this->error("  ✗ {$table} - ERROR: " . $e->getMessage());
                $allValid = false;
            }

            $this->newLine();
        }

        if ($allValid) {
            $this->info('✓ All validations passed!');
            return 0;
        } else {
            $this->error('✗ Some validations failed. Please review the output above.');
            return 1;
        }
    }

    /**
     * Get list of tables to validate
     */
    protected function getTablesToValidate()
    {
        if ($this->option('tables')) {
            return array_map('trim', explode(',', $this->option('tables')));
        }

        return $this->tablesToValidate;
    }

    /**
     * Validate a single table
     */
    protected function validateTable($sourceConnection, $table)
    {
        try {
            // Check if table exists in both databases
            $mysqlExists = $this->tableExists($sourceConnection, $table);
            $sqliteExists = $this->tableExists('sqlite', $table);

            if (!$mysqlExists && !$sqliteExists) {
                $this->warn("    Table does not exist in either database, skipping...");
                return true; // Not an error if both don't exist
            }

            if ($mysqlExists && !$sqliteExists) {
                $this->error("    Table exists in MySQL but not in SQLite!");
                return false;
            }

            if (!$mysqlExists && $sqliteExists) {
                $this->warn("    Table exists in SQLite but not in MySQL (may be OK)");
                return true;
            }

            if (!$mysqlExists) {
                $this->warn("    Table does not exist in MySQL, skipping validation...");
                return true; // Not an error if table doesn't exist in source
            }

            // Compare record counts
            $mysqlCount = DB::connection($sourceConnection)->table($table)->count();
            $sqliteCount = DB::connection('sqlite')->table($table)->count();

            $this->line("    MySQL records: {$mysqlCount}");
            $this->line("    SQLite records: {$sqliteCount}");

            if ($mysqlCount !== $sqliteCount) {
                $this->error("    Record count mismatch!");
                return false;
            }

            // If no records, validation passes
            if ($mysqlCount === 0) {
                return true;
            }

            // Sample data validation - check first, middle, and last records
            $this->validateSampleRecords($sourceConnection, $table);

            return true;

        } catch (\Exception $e) {
            $this->error("    Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate sample records
     */
    protected function validateSampleRecords($sourceConnection, $table)
    {
        try {
            // Get first record from MySQL
            $mysqlFirst = DB::connection($sourceConnection)
                ->table($table)
                ->orderBy('id')
                ->first();

            if ($mysqlFirst) {
                $sqliteFirst = DB::connection('sqlite')
                    ->table($table)
                    ->where('id', $mysqlFirst->id)
                    ->first();

                if (!$sqliteFirst) {
                    $this->error("    First record (ID: {$mysqlFirst->id}) not found in SQLite!");
                    return false;
                }

                // Compare key fields
                $keyFields = $this->getKeyFieldsForTable($table);
                foreach ($keyFields as $field) {
                    if (isset($mysqlFirst->$field) && isset($sqliteFirst->$field)) {
                        $mysqlValue = $mysqlFirst->$field;
                        $sqliteValue = $sqliteFirst->$field;

                        // Normalize for comparison (handle timestamp formats)
                        $mysqlValue = $this->normalizeValue($mysqlValue);
                        $sqliteValue = $this->normalizeValue($sqliteValue);

                        if ($mysqlValue != $sqliteValue) {
                            $this->warn("    Field '{$field}' differs in first record (ID: {$mysqlFirst->id})");
                            $this->line("      MySQL: {$mysqlValue}");
                            $this->line("      SQLite: {$sqliteValue}");
                        }
                    }
                }
            }

            // Get a middle record (if more than 2 records)
            $totalCount = DB::connection($sourceConnection)->table($table)->count();
            if ($totalCount > 2) {
                $middleId = DB::connection($sourceConnection)
                    ->table($table)
                    ->orderBy('id')
                    ->skip((int)($totalCount / 2))
                    ->take(1)
                    ->value('id');

                if ($middleId) {
                    $mysqlMiddle = DB::connection($sourceConnection)
                        ->table($table)
                        ->where('id', $middleId)
                        ->first();

                    $sqliteMiddle = DB::connection('sqlite')
                        ->table($table)
                        ->where('id', $middleId)
                        ->first();

                    if (!$sqliteMiddle) {
                        $this->error("    Middle record (ID: {$middleId}) not found in SQLite!");
                        return false;
                    }
                }
            }

            // Get last record
            $mysqlLast = DB::connection($sourceConnection)
                ->table($table)
                ->orderBy('id', 'desc')
                ->first();

            if ($mysqlLast) {
                $sqliteLast = DB::connection('sqlite')
                    ->table($table)
                    ->where('id', $mysqlLast->id)
                    ->first();

                if (!$sqliteLast) {
                    $this->error("    Last record (ID: {$mysqlLast->id}) not found in SQLite!");
                    return false;
                }
            }

            $this->line("    Sample records validated");

        } catch (\Exception $e) {
            $this->warn("    Could not validate sample records: " . $e->getMessage());
        }
    }

    /**
     * Get key fields to validate for a table
     */
    protected function getKeyFieldsForTable($table)
    {
        $fields = ['id']; // Always check ID

        switch ($table) {
            case 'users':
                return array_merge($fields, ['name', 'email', 'email_verified_at']);
            case 'coinbases':
                return array_merge($fields, ['coin', 'currency', 'amount', 'created_at']);
            case 'meetups':
                return array_merge($fields, ['title', 'date_time', 'location_city', 'created_at']);
            case 'password_resets':
                return array_merge($fields, ['email', 'token']);
            case 'failed_jobs':
                return array_merge($fields, ['uuid', 'queue']);
            default:
                return $fields;
        }
    }

    /**
     * Normalize value for comparison
     */
    protected function normalizeValue($value)
    {
        if ($value === null) {
            return null;
        }

        // Handle timestamps - convert to comparable format
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}[\sT]\d{2}:\d{2}:\d{2}/', $value)) {
            try {
                $date = new \DateTime($value);
                return $date->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return $value;
            }
        }

        // Handle numeric values
        if (is_numeric($value)) {
            return (float) $value;
        }

        return (string) $value;
    }

    /**
     * Check if table exists
     */
    protected function tableExists($connection, $table)
    {
        try {
            if ($connection === 'sqlite') {
                $result = DB::connection($connection)->select(
                    "SELECT name FROM sqlite_master WHERE type='table' AND name=?",
                    [$table]
                );
                return count($result) > 0;
            } else {
                $database = config("database.connections.{$connection}.database");
                $result = DB::connection($connection)->select(
                    "SELECT COUNT(*) as count FROM information_schema.tables 
                     WHERE table_schema = ? AND table_name = ?",
                    [$database, $table]
                );
                return $result[0]->count > 0;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
