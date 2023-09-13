<?php

namespace App\Providers;

use Spatie\Health\Facades\Health;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

class HealthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Health::checks([
            CacheCheck::new(),
            OptimizedAppCheck::new(),
            DatabaseCheck::new()
                ->name('pgsql')
                ->label('PostgreSQL Database'),
            DatabaseCheck::new()
                ->name('sis')
                ->label('SIS Database')
                ->connectionName('sis'),
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            // cloudflare dns
            PingCheck::new()
                ->url('1.1.1.1')
                ->timeout(2),
            QueueCheck::new(),
            // RedisCheck::new(),
            ScheduleCheck::new(),
            UsedDiskSpaceCheck::new(),
        ]);
    }
}
