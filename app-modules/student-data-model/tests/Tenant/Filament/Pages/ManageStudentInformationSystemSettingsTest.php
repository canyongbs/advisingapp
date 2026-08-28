<?php

use AdvisingApp\StudentDataModel\Filament\Pages\ManageStudentInformationSystemSettings;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ManageStudentInformationSystemSettings::getUrl())->assertForbidden();

    asSuperAdmin();

    get(ManageStudentInformationSystemSettings::getUrl())->assertOk();
});
