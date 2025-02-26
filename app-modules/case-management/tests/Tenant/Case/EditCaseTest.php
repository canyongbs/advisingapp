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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\CaseManagement\Filament\Resources\CaseResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseResource\Pages\EditCase;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Notifications\SendClosedCaseFeedbackNotification;
use AdvisingApp\CaseManagement\Tests\Tenant\RequestFactories\EditCaseRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('A successful action on the EditCase page', function () {
    $case = CaseModel::factory()->create();

    asSuperAdmin()
        ->get(
            CaseResource::getUrl('edit', [
                'record' => $case->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditCaseRequestFactory::new()->create());

    livewire(EditCase::class, [
        'record' => $case->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        CaseModel::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority_id',
            ]
        )->toArray()
    );

    $case->refresh();

    expect($case->division->id)
        ->toEqual($request->get('division_id'))
        ->and($case->status->id)
        ->toEqual($request->get('status_id'))
        ->and($case->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('EditCase requires valid data', function ($data, $errors) {
    $case = CaseModel::factory()->create();

    asSuperAdmin();

    $request = collect(EditCaseRequestFactory::new($data)->create());

    livewire(EditCase::class, [
        'record' => $case->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(CaseModel::class, $case->withoutRelations()->toArray());

    expect($case->fresh()->division->id)
        ->toEqual($case->division->id)
        ->and($case->fresh()->status->id)
        ->toEqual($case->status->id)
        ->and($case->fresh()->priority->id)
        ->toEqual($case->priority->id);
})->with(
    [
        'division_id missing' => [EditCaseRequestFactory::new()->state(['division_id' => null]), ['division_id' => 'required']],
        'division_id does not exist' => [
            EditCaseRequestFactory::new()->state(['division_id' => fake()->uuid()]),
            ['division_id' => 'exists'],
        ],
        'status_id missing' => [EditCaseRequestFactory::new()->state(['status_id' => null]), ['status_id' => 'required']],
        'status_id does not exist' => [
            EditCaseRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'exists'],
        ],
        'priority_id missing' => [EditCaseRequestFactory::new()->state(['priority_id' => null]), ['priority_id' => 'required']],
        'priority_id does not exist' => [
            EditCaseRequestFactory::new()->state(['priority_id' => fake()->uuid()]),
            ['priority_id' => 'exists'],
        ],
        'close_details is not a string' => [EditCaseRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [EditCaseRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

// Permission Tests

test('EditCase is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $case = CaseModel::factory()->create();

    actingAs($user)
        ->get(
            CaseResource::getUrl('edit', [
                'record' => $case,
            ])
        )->assertForbidden();

    livewire(EditCase::class, [
        'record' => $case->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('case.view-any');
    $user->givePermissionTo('case.*.update');

    actingAs($user)
        ->get(
            CaseResource::getUrl('edit', [
                'record' => $case,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseRequestFactory::new()->create());

    livewire(EditCase::class, [
        'record' => $case->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        CaseModel::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority',
            ]
        )->toArray()
    );

    $case->refresh();

    expect($case->division->id)
        ->toEqual($request->get('division_id'))
        ->and($case->status->id)
        ->toEqual($request->get('status_id'))
        ->and($case->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('EditCase is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->caseManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('case.view-any');
    $user->givePermissionTo('case.*.update');

    $case = CaseModel::factory()->create();

    actingAs($user)
        ->get(
            CaseResource::getUrl('edit', [
                'record' => $case,
            ])
        )->assertForbidden();

    livewire(EditCase::class, [
        'record' => $case->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->caseManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            CaseResource::getUrl('edit', [
                'record' => $case,
            ])
        )->assertSuccessful();

    $request = collect(EditCaseRequestFactory::new()->create());

    livewire(EditCase::class, [
        'record' => $case->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($case->fresh()->only($request->except('division_id')->keys()->toArray()))
        ->toEqual($request->except('division_id')->toArray())
        ->and($case->fresh()->division->id)->toEqual($request['division_id']);
});

test('send feedback email if case is closed', function () {
    Notification::fake();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->caseManagement = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $case = CaseModel::factory()->create();

    $user->givePermissionTo('case.view-any');
    $user->givePermissionTo('case.*.update');

    actingAs($user);

    $request = collect(EditCaseRequestFactory::new([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Closed,
        ])->getKey(),
        'priority_id' => CasePriority::factory()->create([
            'type_id' => CaseType::factory()->create([
                'has_enabled_feedback_collection' => true,
                'has_enabled_csat' => true,
                'has_enabled_nps' => true,
            ])->getKey(),
        ])->getKey(),
    ])->create());

    livewire(EditCase::class, [
        'record' => $case->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        CaseModel::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority',
            ]
        )->toArray()
    );

    $case->refresh();

    Notification::assertSentTo(
        $case->respondent,
        SendClosedCaseFeedbackNotification::class
    );

    Notification::assertNotSentTo(
        [$user],
        SendClosedCaseFeedbackNotification::class
    );
});
