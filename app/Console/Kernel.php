<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Cron;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // This command works fine on my local database
        // $schedule->command('advance:odd')->timezone('Europe/London')->weekly()->wednesdays()->at('13:20');
        // This is the revised version to check that a condition has been met before running the cron (command).
        $schedule->command('advance:odd')->everyMinute()->when(function() {
            // The first parameter is the command to check in the database table, the second is the number of weeks to advance the next scheduled run by, if the shouldIrun returns true.
            return Cron::shouldIRun('advance:odd', 1);
            //returns true once every week
        });
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
