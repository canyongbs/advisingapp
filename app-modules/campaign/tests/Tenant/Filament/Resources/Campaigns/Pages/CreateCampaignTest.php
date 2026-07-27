<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Tests\Tenant\Filament\Resources\Campaigns\Pages;

use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\CreateCampaign;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use App\Models\User;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('clears journey steps when population group is changed', function () {
    asSuperAdmin();

    // Create groups owned by the current user
    $studentGroup = Group::factory()
        ->for(User::factory(), 'user')
        ->state(['model' => GroupModel::Student])
        ->create();

    $prospectGroup = Group::factory()
        ->for(User::factory(), 'user')
        ->state(['model' => GroupModel::Prospect])
        ->create();

    // Make groups accessible by making the current user owner
    $studentGroup->update(['user_id' => auth()->id()]);
    $prospectGroup->update(['user_id' => auth()->id()]);

    livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Test Campaign',
            'segment_id' => $studentGroup->id,
        ])
        ->assertFormSet([
            'segment_id' => $studentGroup->id,
        ])
        // Change to prospect group — actions should be cleared
        ->fillForm([
            'segment_id' => $prospectGroup->id,
        ])
        ->assertFormSet([
            'segment_id' => $prospectGroup->id,
            'actions' => [],
        ]);
});

it('requires population group on campaign creation', function () {
    asSuperAdmin();

    livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Test Campaign',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'segment_id' => 'required',
        ]);
});
