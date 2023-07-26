<?php

namespace Assist\AssistDataModel\Providers;

use Illuminate\Support\ServiceProvider;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\Relation;

class AssistDataModelServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Relation::morphMap([
            'student' => Student::class,
        ]);
    }
}
