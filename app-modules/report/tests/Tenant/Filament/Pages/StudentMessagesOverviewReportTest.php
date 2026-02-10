<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\StudentMessagesOverviewReport;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(StudentMessagesOverviewReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RetentionCrm);

    $user->refresh();

    get(StudentMessagesOverviewReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(StudentMessagesOverviewReport::getUrl())->assertSuccessful();
});