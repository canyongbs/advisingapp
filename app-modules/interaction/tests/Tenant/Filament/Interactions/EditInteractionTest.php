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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Filament\Resources\Interactions\InteractionResource;
use AdvisingApp\Interaction\Filament\Resources\Interactions\Pages\EditInteraction;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Filament\Forms\Components\Select;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditInteraction is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('prospect.*.view');

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

describe('archived students', function () {
    it('does not offer archived students in the related to select', function () {
        asSuperAdmin();

        $student = Student::factory()->create();
        $interaction = Interaction::factory()->for($student, 'interactable')->create();

        $archived = Student::factory()->create();
        $archived->archive();

        livewire(EditInteraction::class, ['record' => $interaction->getRouteKey()])
            ->assertSchemaComponentExists(
                'interactable_id',
                checkComponentUsing: function (Select $field) use ($student, $archived): bool {
                    $sisids = array_map(strval(...), array_keys($field->getSearchResults('')));

                    expect($sisids)->toContain($student->getKey())
                        ->and($sisids)->not->toContain($archived->getKey());

                    return true;
                },
            );
    });

    it('still shows the archived student the interaction is already attached to', function () {
        asSuperAdmin();

        $archived = Student::factory()->state(['full_name' => 'Already Selected'])->create();
        $interaction = Interaction::factory()->for($archived, 'interactable')->create();

        $archived->archive();

        livewire(EditInteraction::class, ['record' => $interaction->getRouteKey()])
            ->assertSchemaComponentExists(
                'interactable_id',
                checkComponentUsing: function (Select $field): bool {
                    expect($field->getOptionLabel())->toBe('Already Selected');

                    return true;
                },
            );
    });
});
