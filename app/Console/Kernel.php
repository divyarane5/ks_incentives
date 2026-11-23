<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // KeyLeads: morning follow-up summary in everyone's bell.
        $schedule->command('keyleads:daily-summary')->dailyAt('09:30');

        // Shared hosting has no always-running queue worker, so the every-minute cron
        // (schedule:run) starts one that works through waiting jobs (lead imports,
        // Facebook leads) and then exits. Only one runs at a time.
        if (config('queue.default') !== 'sync') {
            $schedule->command('queue:work --stop-when-empty --max-time=50 --timeout=600')
                ->everyMinute()
                ->withoutOverlapping(15);
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
