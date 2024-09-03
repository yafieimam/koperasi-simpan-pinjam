<?php

namespace App\Console;

use App\GeneralSetting;
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
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('deposit:generate')
            ->monthlyOn(GeneralSetting::getCutOffDate(),'00:00')
            // ->everyMinute()
            // ->environments(['staging', 'production']);
            ->withoutOverlapping();

        $schedule->command('loan-lunas:generate')
            // ->monthlyOn(TsLoans::getCutOffDate(),'00:00')
            // ->everyMinute()
            ->daily()
            // ->environments(['staging', 'production']);
            ->withoutOverlapping();
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
