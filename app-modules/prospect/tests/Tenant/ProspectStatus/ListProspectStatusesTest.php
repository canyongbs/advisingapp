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

use AdvisingApp\Prospect\Filament\Resources\ProspectStatuses\Pages\ListProspectStatuses;
use AdvisingApp\Prospect\Filament\Resources\ProspectStatuses\ProspectStatusResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ListProspectStatuses page', function () {
    $prospectStatuses = ProspectStatus::factory()
        // TODO: Fix this once Prospect factory is created
        //->has(CaseModel::factory()->count(fake()->randomNumber(1)), 'cases')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ListProspectStatuses::class)
        ->set('tableRecordsPerPage', 10);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($prospectStatuses)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('prospects_count');

    $prospectStatuses->each(
        fn (ProspectStatus $prospectStatus) => $component
            ->assertTableColumnStateSet(
                'id',
                $prospectStatus->id,
                $prospectStatus
            )
            ->assertTableColumnStateSet(
                'name',
                $prospectStatus->name,
                $prospectStatus
            )
            ->assertTableColumnFormattedStateSet(
                'classification',
                $prospectStatus->classification->getLabel(),
                $prospectStatus
            )
            ->assertTableColumnFormattedStateSet(
                'color',
                $prospectStatus->color->value,
                $prospectStatus
            )
        // Currently setting not test for cases_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListProspectStatuses is gated with proper access control', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('index')
        )->assertSuccessful();
});
