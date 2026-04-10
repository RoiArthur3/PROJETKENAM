<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarkExistingMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:mark-existing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark create_*_table migrations as run if the corresponding table already exists.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning migration files...');

        $migrationFiles = glob(database_path('migrations') . '/*.php');
        $migrated = DB::table('migrations')->pluck('migration')->toArray();
        $maxBatch = DB::table('migrations')->max('batch') ?: 1;

        $inserted = [];

        foreach ($migrationFiles as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            if (in_array($name, $migrated)) {
                continue;
            }

            // Handle create_*_table migrations
            if (preg_match('/create_(.+?)_table/', $name, $m)) {
                $table = $m[1];

                if (Schema::hasTable($table)) {
                    DB::table('migrations')->insert([
                        'migration' => $name,
                        'batch' => $maxBatch,
                    ]);
                    $inserted[] = $name;
                    $this->line("Marked as run: {$name} (table `{$table}` exists)");
                }

                continue;
            }

            // Handle add_*_to_*_table migrations: if target table exists and any of the columns in the migration
            // already exist, consider it applied and mark it as run to avoid duplicate column errors.
            if (preg_match('/add_.*_to_(.+?)_table/', $name, $m2)) {
                $table = $m2[1];

                if (! Schema::hasTable($table)) {
                    continue;
                }

                $contents = file_get_contents($file);
                preg_match_all('/->\w+\(["\']([a-z0-9_]+)["\']/', $contents, $colMatches);
                $columns = array_unique($colMatches[1] ?? []);

                $found = false;
                foreach ($columns as $col) {
                    if (Schema::hasColumn($table, $col)) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    DB::table('migrations')->insert([
                        'migration' => $name,
                        'batch' => $maxBatch,
                    ]);
                    $inserted[] = $name;
                    $this->line("Marked as run: {$name} (table `{$table}` exists and at least one column already present)");
                }
            }
        }

        if (empty($inserted)) {
            $this->info('No migrations to mark as run.');
        } else {
            $this->info('Marked ' . count($inserted) . ' migrations as run.');
        }

        return 0;
    }
}
