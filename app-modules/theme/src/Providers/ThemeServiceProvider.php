<?php

namespace Assist\Theme\Providers;

use Filament\Panel;
use Assist\Theme\ThemePlugin;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new ThemePlugin()));
    }
}
