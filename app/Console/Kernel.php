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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    // protected function schedule(Schedule $schedule)
    // {
    //     // $schedule->command('inspire')->hourly();
    // }

    protected function schedule(Schedule $schedule)
    {
        // Run daily at a specific time (e.g., 9:00 AM)
        // $schedule->command('notifications:send-daily')
        //         ->dailyAt('09:00')
        //         ->timezone('UTC'); // Adjust to your timezone

        // $schedule->command('notifications:send-daily')
        //  ->dailyAt('09:00')
        //  ->timezone('Asia/Kolkata');

        // For testing only
        $schedule->command('notifications:send-daily')
         ->everyMinute();

        // Safety net for abandoned/interrupted Stripe payments — see
        // ExpirePendingPaymentTransactions for why this exists. Runs
        // frequently since it only acts on attempts already older than
        // --minutes (default 45), so running every 10 minutes doesn't
        // sweep anything prematurely.
        $schedule->command('payments:expire-stale')
         ->everyTenMinutes();
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
