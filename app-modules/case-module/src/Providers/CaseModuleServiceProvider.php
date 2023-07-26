<?php

namespace Assist\CaseModule\Providers;

use Filament\Panel;
use Assist\CaseModule\CasePlugin;
use Illuminate\Support\ServiceProvider;

class CaseModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // TODO: use once we upgrade to Filament beta 10 or higher
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new CasePlugin()));
    }

    public function boot()
    {
        // TODO: Remove once we upgrade to Filament beta 10 or higher
        filament()->getPanel('admin')
            ->plugin(new CasePlugin());
    }
}
