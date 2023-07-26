<?php

namespace Assist\Case\Providers;

use Filament\Panel;
use Assist\Case\CasePlugin;
use Illuminate\Support\ServiceProvider;

class CaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new CasePlugin()));
    }

    public function boot()
    {
    }
}
