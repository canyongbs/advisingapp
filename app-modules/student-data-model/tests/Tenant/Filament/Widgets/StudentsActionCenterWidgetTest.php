<?php

use AdvisingApp\StudentDataModel\Filament\Widgets\StudentsActionCenterWidget;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Settings\LicenseSettings;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('returns data', function () {})->todo();

it('only shows the alerts count column when the early alert feature is active', function () {})->todo();

it('only shows the cases count column when the case management feature is active', function () {
    asSuperAdmin();

    Student::factory(5)->create();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->caseManagement = false;
    $settings->save();

    livewire(StudentsActionCenterWidget::class)->assertTableColumnHidden('cases_count');

    $settings->data->addons->caseManagement = true;
    $settings->save();

    livewire(StudentsActionCenterWidget::class)->assertTableColumnVisible('cases_count');
});

it('can filter for messages', function () {})->todo();

it('can filter for cases', function () {})->todo();

it('can filter for concerns', function () {})->todo();

it('can filter for tasks', function () {})->todo();