<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\StudentCaseReport;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $user = User::factory()->create();

    $settings->data->addons->caseManagement = false;
    $settings->save();

    actingAs($user);

    get(StudentCaseReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RetentionCrm);

    $user->refresh();

    get(StudentCaseReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(StudentCaseReport::getUrl())->assertForbidden();

    $settings->data->addons->caseManagement = true;
    $settings->save();

    get(StudentCaseReport::getUrl())->assertSuccessful();
});