<?php

namespace Assist\Engagement\Providers;

use Filament\Panel;
use Assist\Engagement\EngagementPlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Engagement\Models\EngagementFile;
use Illuminate\Database\Eloquent\Relations\Relation;

class EngagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new EngagementPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'engagement_file' => EngagementFile::class,
        ]);
    }
}
