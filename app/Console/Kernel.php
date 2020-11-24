<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [ Commands\GetEmails::class];

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('email:get')->everyTenMinutes();
     //   $schedule->command('payment:request')->daily();
     //   $schedule->command('recurring:create')->daily();
     //   $schedule->command('activitylog:clean')->weekly()->mondays()->at('01:00');
    }
}
