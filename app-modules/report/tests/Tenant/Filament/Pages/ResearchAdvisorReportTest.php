<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\ResearchAdvisorReport;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $user = User::factory()->create();

    $settings->data->addons->researchAdvisor = false;
    $settings->save();

    actingAs($user);

    get(ResearchAdvisorReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::ConversationalAi);

    $user->refresh();

    get(ResearchAdvisorReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(ResearchAdvisorReport::getUrl())->assertForbidden();

    $settings->data->addons->researchAdvisor = true;
    $settings->save();

    get(ResearchAdvisorReport::getUrl())->assertSuccessful();
});