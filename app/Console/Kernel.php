<?php

namespace App\Console;

use App\Jobs\ProcessDailyActivity;
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
        Commands\DailyAttCheck::class,
        Commands\MySqlBackUp::class,
        Commands\ImportDataBase::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('attendance:check')
                 ->everyMinute()->runInBackground();

        $schedule->command('mysql:backup')->weekly()->runInBackground();


        // dispatch daily activity job
        $schedule->job((new ProcessDailyActivity())->onQueue('default'))->dailyAt('19:13');;
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
