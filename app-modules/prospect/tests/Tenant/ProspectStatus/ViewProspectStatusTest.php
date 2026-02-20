<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Prospect\Filament\Resources\ProspectStatuses\Pages\ViewProspectStatus;
use AdvisingApp\Prospect\Filament\Resources\ProspectStatuses\ProspectStatusResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Features\ProspectStatusFeature;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewProspectStatus page', function () {
    $prospectStatus = ProspectStatus::factory()->create();

    $assertSeeText = [
        'Name',
        $prospectStatus->name,
        'Classification',
        $prospectStatus->classification->getLabel(),
        'Color',
    ];

    if (! ProspectStatusFeature::active()) {
        $assertSeeText[] = $prospectStatus->color->value;
    }

    asSuperAdmin()
        ->get(
            ProspectStatusResource::getUrl('view', [
                'record' => $prospectStatus,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder($assertSeeText);
});

// Permission Tests

test('ViewProspectStatus is gated with proper access control', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $prospectStatus = ProspectStatus::factory()->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('view', [
                'record' => $prospectStatus,
            ])
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.view');

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('view', [
                'record' => $prospectStatus,
            ])
        )->assertSuccessful();
});

it('displays the edit action and not the lock when the prospect status is not system protected', function () {
    $prospectStatus = ProspectStatus::factory()->create();

    asSuperAdmin();

    livewire(ViewProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertDontSeeHtml('data-identifier="prospect_status_system_protected"')
        ->assertActionVisible('edit')
        ->assertActionEnabled('edit');
});

it('displays the lock icon rather than the edit action when the prospect status is system protected', function () {
    $prospectStatus = ProspectStatus::factory()->create([
        'is_system_protected' => true,
    ]);

    asSuperAdmin();

    livewire(ViewProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertSeeHtml('data-identifier="prospect_status_system_protected"')
        ->assertActionHidden('edit')
        ->assertActionDisabled('edit');
});
