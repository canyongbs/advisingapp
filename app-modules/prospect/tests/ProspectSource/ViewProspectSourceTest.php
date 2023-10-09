<?php

use App\Models\User;

use Illuminate\Support\Benchmark;
use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Filament\Resources\ProspectSourceResource;

test('The correct details are displayed on the ViewProspectSource page', function () {
    Benchmark::dd(function () {
        $prospectSource = ProspectSource::factory()->create();

        asSuperAdmin()
            ->get(
                ProspectSourceResource::getUrl('view', [
                    'record' => $prospectSource,
                ])
            )
            ->assertSuccessful()
            ->assertSeeTextInOrder(
                [
                    'Name',
                    $prospectSource->name,
                ]
            );
    });
});

// Permission Tests

test('ViewProspectSource is gated with proper access control', function () {
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
