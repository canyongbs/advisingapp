<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\CustomAdvisorReport;
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

    get(CustomAdvisorReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::ConversationalAi);

    $user->refresh();

    get(CustomAdvisorReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(CustomAdvisorReport::getUrl())->assertForbidden();

    $settings->data->addons->customAiAssistants = true;
    $settings->save();

    get(CustomAdvisorReport::getUrl())->assertSuccessful();
});