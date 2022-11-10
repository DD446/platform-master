<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Nova\Trix\PruneStaleAttachments;
use App\Jobs\CheckWebserverConfig;

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
/*         $schedule->command('podcaster:create-spotify')
                    ->weeklyOn(1, '8:00');*/
/*        $schedule->command('podcaster:fetch-spotify-analytics-data')
            ->daily()
            ->at('03:00');*/

/*        $schedule->command('podcaster:pre-delete-expired-trial-users')
            ->daily()
            ->at('04:00');*/

        $schedule->command('podcaster:cache-reviews')
            ->daily()
            ->at('05:00');

        $schedule->command('podcaster:welcome-week-mailer')
            ->daily()
            ->at('07:00');

        $schedule->command('podcaster:flush-dashboard-caches')
            ->hourly()
            ->between('1:00', '18:00');

        $schedule->command('podcaster:publish-posts')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('podcaster:charge-users')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        $schedule->command('podcaster:charge-user-extra')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        $schedule->command('podcaster:sync-deleted-users')
            ->everyThirtyMinutes();

        $schedule->command('podcaster:clean-up-member-queue')
            ->everyThirtyMinutes();

        $schedule->command('podcaster:cleanup-trials')
            ->mondays()
            ->at('4:00');

        $schedule->command('podcaster:send-unpaid-bill-reminder')
            ->mondays()
            ->at('8:00');

        $schedule->command('telescope:prune')
            ->daily();

        $schedule->call(function () {
            (new PruneStaleAttachments)();
        })->daily();

        $schedule->command('websockets:clean')
            ->daily();

        $schedule->job(new CheckWebserverConfig, 'superuser')
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
