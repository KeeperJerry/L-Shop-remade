<?php
declare(strict_types = 1);

namespace app\Console;

use app\Console\Commands\DB\Transfer;
use app\Console\Commands\Purchase\Complete as CompletePurchase;
use app\Console\Commands\Rcon;
use app\Console\Commands\User\Create as CreateUser;
use app\Console\Commands\User\Delete as DeleteUser;
use app\Console\Commands\User\Roles\Attach as UserAttachRoles;
use app\Console\Commands\User\Roles\Detach as UserDetachRoles;
use app\Console\Commands\User\Roles\RolesList as UserRolesList;
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
        Transfer::class,
        CreateUser::class,
        DeleteUser::class,
        UserAttachRoles::class,
        UserDetachRoles::class,
        UserRolesList::class,
        CompletePurchase::class,
        Rcon::class
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
