<?php

namespace Assist\AssistDataModel\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\AssistDataModel\Models\Student;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\AssistDataModel\AssistDataModelPlugin;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\CaseloadManagement\Observers\CaseloadObserver;

class AssistDataModelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AssistDataModelPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'student' => Student::class,
        ]);
    }
}
