<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule): void
    {
        // scheduled tasks can only be run on production environment
        if (App::environment('production')) {
            $schedule->command('app:coins-update-command')->everyMinute()->withoutOverlapping();
        }
    }
}
