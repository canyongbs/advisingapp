<?php

use AdvisingApp\Ai\Filament\Resources\LegacyAiMessageLogs\Pages\ManageLegacyAiMessageLogs;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Resources\Reports\Pages\ListReports;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ListReports::getUrl())->assertForbidden();

    $user->givePermissionTo('report.view-any');

    get(ListReports::getUrl())->assertForbidden();

    $user->givePermissionTo('user.view-any');

    get(ListReports::getUrl())->assertSuccessful();

    $user->revokePermissionTo('user.view-any');

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    get(ListReports::getUrl())->assertSuccessful();

    $user->revokeLicense(LicenseType::RecruitmentCrm);
    $user->grantLicense(LicenseType::RetentionCrm);

    $user->refresh();

    get(ListReports::getUrl())->assertSuccessful();
});
