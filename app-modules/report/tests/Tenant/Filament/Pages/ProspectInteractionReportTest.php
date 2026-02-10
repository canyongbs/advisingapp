<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\ProspectInteractionReport;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ProspectInteractionReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    get(ProspectInteractionReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(ProspectInteractionReport::getUrl())->assertSuccessful();
});