<?php

namespace Assist\KnowledgeBase\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\KnowledgeBase\KnowledgeBasePlugin;

class KnowledgeBaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new KnowledgeBasePlugin()));
    }

    public function boot()
    {
    }
}
