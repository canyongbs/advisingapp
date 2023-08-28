<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use StubModuleNamespace\StubClassNamePrefix\StubClassNamePrefixPlugin;

class StubClassNamePrefixServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new StubClassNamePrefixPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([]);
    }
}
