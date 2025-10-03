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

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Alert\Tests\Tenant\Filament\Actions\RequestFactories\BulkCreateAlertActionRequestFactory;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('shows the form and validation', function (BulkCreateAlertActionRequestFactory $data, array $errors) {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = BulkCreateAlertActionRequestFactory::new($data)->create();

    livewire(ListStudents::class)
        ->mountTableBulkAction('createAlert', [$student->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasTableBulkActionErrors($errors);

    assertDatabaseMissing(Alert::class, $request);
})->with([
    'description required' => [
        BulkCreateAlertActionRequestFactory::new()->without('description'),
        ['description' => 'required'],
    ],
    'description max 65535 characters only' => [
        BulkCreateAlertActionRequestFactory::new([
            'description' => str_repeat('a', 65536),
        ]),
        ['description' => 'max:65535'],
    ],
    'status_id required' => [
        BulkCreateAlertActionRequestFactory::new()->without('status_id'),
        ['status_id' => 'required'],
    ],
    'status_id exists' => [
        BulkCreateAlertActionRequestFactory::new()->state(['status_id' => (string) Str::uuid()]),
        ['status_id'],
    ],
    'suggested_intervention required' => [
        BulkCreateAlertActionRequestFactory::new()->without('suggested_intervention'),
        ['suggested_intervention' => 'required'],
    ],
    'suggested_intervention max 65535 characters only' => [
        BulkCreateAlertActionRequestFactory::new([
            'suggested_intervention' => str_repeat('a', 65536),
        ]),
        ['suggested_intervention' => 'max:65535'],
    ],
]);

it('can successfully create bulk alert with student', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    $request = BulkCreateAlertActionRequestFactory::new()->create();

    livewire(ListStudents::class)
        ->mountTableBulkAction('createAlert', [$student->getKey()])
        ->setTableBulkActionData([
            ...$request,
        ])
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    assertDatabaseHas(Alert::class, [
        'description' => $request['description'],
        'status_id' => $request['status_id'],
        'suggested_intervention' => $request['suggested_intervention'],
        'concern_id' => $student->getKey(),
        'concern_type' => $student->getMorphClass(),
    ]);
});

it('can successfully create bulk alert with prospect', function () {
    asSuperAdmin();

    $prospect = Prospect::factory()->create();

    $request = BulkCreateAlertActionRequestFactory::new()->create();

    livewire(ListProspects::class)
        ->mountTableBulkAction('createAlert', [$prospect->getKey()])
        ->setTableBulkActionData($request)
        ->callMountedTableBulkAction()
        ->assertHasNoTableBulkActionErrors()
        ->assertSuccessful()
        ->assertNotified();

    assertDatabaseHas(Alert::class, [
        'description' => $request['description'],
        'status_id' => $request['status_id'],
        'suggested_intervention' => $request['suggested_intervention'],
        'concern_id' => $prospect->getKey(),
        'concern_type' => $prospect->getMorphClass(),
    ]);
});
