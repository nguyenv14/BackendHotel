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
        // $schedule->command('statistical_run:cron')->hourly();
        $schedule->command('statistical_run:cron')->everyMinute();
        
        // Kiểm tra đơn hàng no show mỗi ngày lúc 00:00
        $schedule->command('orders:check-no-show')->daily();

        // Cập nhật document với ChatBot mỗi ngày lúc 01:00
        $schedule->command('document:update-with-chatbot')->dailyAt('01:00');
        // Cập nhật document với Recommendation mỗi ngày lúc 02:00
        $schedule->command('document:update-with-recommendation')->dailyAt('02:00');
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
