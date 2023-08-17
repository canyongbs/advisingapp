<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Filament\Resources\ProspectSourceResource;

// TODO: Write tests for ViewAudit page
test('The correct details are displayed on the ViewAudit page', function () {});

// Permission Tests

test('ViewAudit is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectSource = ProspectSource::factory()->create();

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertForbidden();

    $user->givePermissionTo('prospect_source.view-any');
    $user->givePermissionTo('prospect_source.*.view');

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('view', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();
});
