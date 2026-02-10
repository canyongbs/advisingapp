<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\InstitutionalAdvisorReport;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $user = User::factory()->create();

    $settings->data->addons->customAiAssistants = false;
    $settings->save();

    actingAs($user);

    get(InstitutionalAdvisorReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::ConversationalAi);

    $user->refresh();

    get(InstitutionalAdvisorReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(InstitutionalAdvisorReport::getUrl())->assertSuccessful();
});