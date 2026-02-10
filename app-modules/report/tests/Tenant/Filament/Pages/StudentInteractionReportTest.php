<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Filament\Pages\StudentInteractionReport;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(StudentInteractionReport::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RetentionCrm);

    $user->refresh();

    get(StudentInteractionReport::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(StudentInteractionReport::getUrl())->assertSuccessful();
});