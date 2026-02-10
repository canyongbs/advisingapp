<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\ProjectReport;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $user = User::factory()->create();

    $settings->data->addons->projectManagement = false;
    $settings->save();

    actingAs($user);

    get(ProjectReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(ProjectReport::getUrl())->assertForbidden();

    $settings->data->addons->projectManagement = true;
    $settings->save();

    get(ProjectReport::getUrl())->assertSuccessful();
});