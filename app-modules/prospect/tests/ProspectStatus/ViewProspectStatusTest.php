<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Filament\Resources\ProspectStatusResource;

test('The correct details are displayed on the ViewProspectStatus page', function () {
    $prospectStatus = ProspectStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ProspectStatusResource::getUrl('view', [
                'record' => $prospectStatus,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'ID',
                $prospectStatus->id,
                'Name',
                $prospectStatus->name,
                'Color',
                $prospectStatus->color,
            ]
        );
});

// Permission Tests

test('ViewProspectStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectStatus = ProspectStatus::factory()->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('view', [
                'record' => $prospectStatus,
            ])
        )->assertForbidden();

    $user->givePermissionTo('prospect_status.view-any');
    $user->givePermissionTo('prospect_status.*.view');

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('view', [
                'record' => $prospectStatus,
            ])
        )->assertSuccessful();
});
