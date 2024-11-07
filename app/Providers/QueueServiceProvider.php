<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Overrides\LaravelSqsExtended\SqsDiskConnector;

class QueueServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $manager = $this->app->make('queue');
        $manager->addConnector('canyongbs-sqs-disk', fn () => new SqsDiskConnector());
    }
}
