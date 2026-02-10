<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\ProspectMessagesDetailReport;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ProspectMessagesDetailReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    get(ProspectMessagesDetailReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(ProspectMessagesDetailReport::getUrl())->assertSuccessful();
});