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

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Tests\Tenant\Filament\Actions\RequestFactories\BulkCreateInteractionActionRequestFactory;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('shows the form and validation', function (BulkCreateInteractionActionRequestFactory $data, array $errors) {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = BulkCreateInteractionActionRequestFactory::new($data)->create();

    livewire(ListStudents::class)
        ->mountTableBulkAction('createInteraction', [$student->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasTableBulkActionErrors($errors);

    assertDatabaseMissing(Interaction::class, $request);
})->with([
    'interaction_initiative_id required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('interaction_initiative_id'),
        ['interaction_initiative_id' => 'required'],
    ],
    'interaction_initiative_id exists' => [
        BulkCreateInteractionActionRequestFactory::new()->state(['interaction_initiative_id' => (string) Str::uuid()]),
        ['interaction_initiative_id' => 'exists'],
    ],
    'interaction_driver_id required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('interaction_driver_id'),
        ['interaction_driver_id' => 'required'],
    ],
    'interaction_driver_id exists' => [
        BulkCreateInteractionActionRequestFactory::new()->state(['interaction_driver_id' => (string) Str::uuid()]),
        ['interaction_driver_id' => 'exists'],
    ],
    'division_id required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('division_id'),
        ['division_id' => 'required'],
    ],
    'division_id exists' => [
        BulkCreateInteractionActionRequestFactory::new()->state(['division_id' => (string) Str::uuid()]),
        ['division_id' => 'exists'],
    ],
    'interaction_outcome_id required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('interaction_outcome_id'),
        ['interaction_outcome_id' => 'required'],
    ],
    'interaction_outcome_id exists' => [
        BulkCreateInteractionActionRequestFactory::new()->state(['interaction_outcome_id' => (string) Str::uuid()]),
        ['interaction_outcome_id' => 'exists'],
    ],
    'interaction_relation_id required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('interaction_relation_id'),
        ['interaction_relation_id' => 'required'],
    ],
    'interaction_relation_id exists' => [
        BulkCreateInteractionActionRequestFactory::new()->state(['interaction_relation_id' => (string) Str::uuid()]),
        ['interaction_relation_id' => 'exists'],
    ],
    'interaction_status_id required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('interaction_status_id'),
        ['interaction_status_id' => 'required'],
    ],
    'interaction_status_id exists' => [
        BulkCreateInteractionActionRequestFactory::new()->state(['interaction_status_id' => (string) Str::uuid()]),
        ['interaction_status_id' => 'exists'],
    ],
    'interaction_type_id required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('interaction_type_id'),
        ['interaction_type_id' => 'required'],
    ],
    'interaction_type_id exists' => [
        BulkCreateInteractionActionRequestFactory::new()->state(['interaction_type_id' => (string) Str::uuid()]),
        ['interaction_type_id' => 'exists'],
    ],
    'start_datetime required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('start_datetime'),
        ['start_datetime' => 'required'],
    ],
    'end_datetime required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('end_datetime'),
        ['end_datetime' => 'required'],
    ],
    'subject required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('subject'),
        ['subject' => 'required'],
    ],
    'description required' => [
        BulkCreateInteractionActionRequestFactory::new()->without('description'),
        ['description' => 'required'],
    ],
]);

it('can successfully create bulk interaction with student', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = BulkCreateInteractionActionRequestFactory::new()->create();

    livewire(ListStudents::class)
        ->mountTableBulkAction('createInteraction', [$student->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    assertDatabaseHas(Interaction::class, $student->interactions->first()->toArray());
});

it('can successfully create bulk interaction with prospect', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();

    $request = BulkCreateInteractionActionRequestFactory::new()->create();

    livewire(ListProspects::class)
        ->mountTableBulkAction('createInteraction', [$prospect->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    assertDatabaseHas(Interaction::class, $prospect->interactions->first()->toArray());
});
