<?php

use AdvisingApp\Prospect\Filament\Widgets\ProspectsActionCenterWidget;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Widgets\StudentsActionCenterWidget;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Settings\LicenseSettings;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('returns data', function () {})->todo();

it('only shows the cases count column when the case management feature is active', function () {
    asSuperAdmin();

    Prospect::factory(5)->create();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->caseManagement = false;
    $settings->save();

    livewire(ProspectsActionCenterWidget::class)->assertTableColumnHidden('cases_count');

    $settings->data->addons->caseManagement = true;
    $settings->save();

    livewire(ProspectsActionCenterWidget::class)->assertTableColumnVisible('cases_count');
});

it('can filter for messages', function () {})->todo();

it('can filter for cases', function () {})->todo();

it('can filter for concerns', function () {})->todo();

it('can filter for tasks', function () {})->todo();