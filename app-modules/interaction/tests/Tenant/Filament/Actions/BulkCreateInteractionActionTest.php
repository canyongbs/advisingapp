<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Interaction\Database\Factories\InteractionFactory;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('shows the form and validation', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = collect(InteractionFactory::new()->create());

    livewire(ListStudents::class)
        ->mountTableBulkAction('createInteraction', [$student->getKey()])
        ->setTableBulkActionData([
            ...$request,
        ])
        ->callMountedTableBulkAction();
})->with([
    'initiative required' => [
        InteractionFactory::new()->state(['interaction_initiative_id' => null]),
        ['interaction_initiative_id' => 'required'],
    ],
    'driver required' => [
        InteractionFactory::new()->state(['interaction_driver_id' => null]),
        ['interaction_driver_id' => 'required'],
    ],
    'division required' => [
        InteractionFactory::new()->state(['division_id' => null]),
        ['interaction_initiative_id' => 'required'],
    ],
    'outcome required' => [
        InteractionFactory::new()->state(['interaction_outcome_id' => null]),
        ['interaction_outcome_id' => 'required'],
    ],
    'relation required' => [
        InteractionFactory::new()->state(['interaction_relation_id' => null]),
        ['interaction_relation_id' => 'required'],
    ],
    'status required' => [
        InteractionFactory::new()->state(['interaction_status_id' => null]),
        ['interaction_status_id' => 'required'],
    ],
    'type required' => [
        InteractionFactory::new()->state(['interaction_type_id' => null]),
        ['interaction_type_id' => 'required'],
    ],
    'start date required' => [
        InteractionFactory::new()->state(['start_datetime' => null]),
        ['start_datetime' => 'required'],
    ],
    'end time required' => [
        InteractionFactory::new()->state(['end_datetime' => null]),
        ['end_datetime' => 'required'],
    ],
    'subject required' => [
        InteractionFactory::new()->state(['subject' => null]),
        ['subject' => 'required'],
    ],
    'description required' => [
        InteractionFactory::new()->state(['description' => null]),
        ['description' => 'required'],
    ],
]);

it('can successfully create bulk interaction with student', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = collect(InteractionFactory::new()->create([
        'interactable_id' => $student->getKey(),
        'interactable_type' => $student->getMorphClass(),
    ]));

    livewire(ListStudents::class)
        ->mountTableBulkAction('createInteraction', [$student->getKey()])
        ->setTableBulkActionData([
            ...$request,
        ])
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();
});

it('can successfully create bulk interaction with porspect', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();

    $request = collect(InteractionFactory::new()->create([
        'interactable_id' => $prospect->getKey(),
        'interactable_type' => $prospect->getMorphClass(),
    ]));

    livewire(ListStudents::class)
        ->mountTableBulkAction('createInteraction', [$prospect->getKey()])
        ->setTableBulkActionData([
            ...$request,
        ])
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();
});
