<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\QnaAdvisorReport;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $user = User::factory()->create();

    $settings->data->addons->qnaAdvisor = false;
    $settings->save();

    actingAs($user);

    get(QnaAdvisorReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::ConversationalAi);

    $user->refresh();

    get(QnaAdvisorReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(QnaAdvisorReport::getUrl())->assertForbidden();

    $settings->data->addons->qnaAdvisor = true;
    $settings->save();

    get(QnaAdvisorReport::getUrl())->assertSuccessful();
});