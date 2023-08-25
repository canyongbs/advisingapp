<?php

namespace Assist\Engagement\Providers;

use Filament\Panel;
use Assist\Engagement\EngagementPlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Engagement\Models\Engagement;
use Illuminate\Console\Scheduling\Schedule;
use Assist\Engagement\Models\EngagementFile;
use Assist\Engagement\Actions\DeliverEngagements;
use Assist\Engagement\Models\EngagementDeliverable;
use Assist\Engagement\Observers\EngagementObserver;
use Assist\Engagement\Models\EngagementFileEntities;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Engagement\Observers\EngagementFileEntitiesObserver;

class EngagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new EngagementPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'engagement' => Engagement::class,
            'engagement_deliverable' => EngagementDeliverable::class,
            'engagement_file' => EngagementFile::class,
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->job(DeliverEngagements::class)->everyMinute();
        });

        $this->observers();
    }

    public function observers(): void
    {
        EngagementFileEntities::observe(EngagementFileEntitiesObserver::class);
        Engagement::observe(EngagementObserver::class);
    }
}
