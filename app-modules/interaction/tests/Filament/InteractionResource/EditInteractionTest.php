<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;

use function Pest\Laravel\actingAs;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Filament\Resources\InteractionResource;

test('EditInteraction is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('prospect.*.view');
    $user->givePermissionTo('case.*.view');

    $interaction = Interaction::factory()->create();

    actingAs($user)
        ->get(
            InteractionResource::getUrl('edit', ['record' => $interaction])
        )
        ->assertForbidden();

    $user->givePermissionTo('interaction.view-any');
    $user->givePermissionTo('interaction.*.update');

    actingAs($user)
        ->get(
            InteractionResource::getUrl('edit', ['record' => $interaction])
        )
        ->assertSuccessful();
});
