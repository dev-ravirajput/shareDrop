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
        // Register your custom commands here
        \App\Console\Commands\CleanupExpiredShares::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Cleanup expired shares daily at 3:00 AM
        $schedule->command('shares:cleanup')
            ->dailyAt('03:00')
            ->appendOutputTo(storage_path('logs/share-cleanup.log'));

        // Queue worker restart (recommended for production)
        $schedule->command('queue:restart')
            ->hourly();

        // Prune stale cache items
        $schedule->command('cache:prune-stale-tags')
            ->hourly();

        // Database backup (if using backup package)
        // $schedule->command('backup:run --only-db')
        //     ->dailyAt('02:00');

        // Application health checks
        $schedule->command('app:health-check')
            ->everyThirtyMinutes()
            ->onOneServer();

        // Horizon snapshot (if using Horizon)
        if (class_exists(\Laravel\Horizon\Console\SnapshotCommand::class)) {
            $schedule->command('horizon:snapshot')
                ->everyFiveMinutes();
        }

        // Telescope pruning (if using Telescope)
        if (class_exists(\Laravel\Telescope\Console\PruneCommand::class)) {
            $schedule->command('telescope:prune')
                ->daily();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}