<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\ProspectTaskManagement;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ProspectTaskManagement::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    get(ProspectTaskManagement::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(ProspectTaskManagement::getUrl())->assertSuccessful();
});