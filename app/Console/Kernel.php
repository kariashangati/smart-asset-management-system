<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process location updates every 5 minutes
        $schedule->command('location:process --minutes=5')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        // Cleanup old location logs daily at 2 AM
        $schedule->command('location:cleanup --days=30')
            ->dailyAt('02:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
