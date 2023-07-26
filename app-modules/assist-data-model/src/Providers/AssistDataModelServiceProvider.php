<?php

namespace Assist\AssistDataModel\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\AssistDataModelPlugin;
use Illuminate\Database\Eloquent\Relations\Relation;

class AssistDataModelServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AssistDataModelPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'student' => Student::class,
        ]);
    }
}
