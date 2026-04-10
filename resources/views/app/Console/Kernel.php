<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\MarkExistingMigrations::class,
        \App\Console\Commands\DiagnoseSystem::class,
        \App\Console\Commands\SystemHealthCheck::class,
        \App\Console\Commands\FixCommonIssues::class,
        \App\Console\Commands\PromoteUserToSuperadmin::class,
        \App\Console\Commands\SyncUserPermissions::class,
        \App\Console\Commands\DeleteTestOperations::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Sur un hébergement mutualisé, on planifie le worker pour qu'il s'exécute chaque minute
        // et traite la file d'attente. "withoutOverlapping" évite d'avoir plusieurs workers en même temps.
        $schedule->command('queue:work --stop-when-empty')
                 ->everyMinute()
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
