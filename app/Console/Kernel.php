<?php

namespace App\Console;

use App\Console\Commands\ProcessIncomingTelegramUpdates;
use Override;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    #[Override]
    protected function schedule(Schedule $schedule): void
    {
        if ($this->app->environment('local')) {
            $schedule->command('telescope:prune')->daily();
        }

        $schedule->command(ProcessIncomingTelegramUpdates::class)->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    #[Override]
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
