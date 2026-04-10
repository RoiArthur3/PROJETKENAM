<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all migration files
        $migrationFiles = File::glob(database_path('migrations/*.php'));

        // Insert all migrations into the migrations table
        $batch = DB::table('migrations')->max('batch') + 1;

        foreach ($migrationFiles as $file) {
            $migrationName = basename($file, '.php');

            // Skip if already in migrations table
            if (!DB::table('migrations')->where('migration', $migrationName)->exists()) {
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => $batch,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be rolled back as it's just marking migrations as complete
    }
};
