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
use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\CaseManagement\Filament\Resources\CaseResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseResource\Pages\CreateCase;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Tests\Tenant\RequestFactories\CreateCaseRequestFactory;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('A successful action on the CreateCase page', function () {
    asSuperAdmin()
        ->get(
            CaseResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = collect(CreateCaseRequestFactory::new()->create());

    livewire(CreateCase::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Prospect::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseModel::all());

    assertDatabaseHas(
        CaseModel::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority_id',
                'respondent_id',
                'type_id',
            ]
        )->toArray()
    );

    $case = CaseModel::first();

    expect($case->division->id)
        ->toEqual($request->get('division_id'))
        ->and($case->status->id)
        ->toEqual($request->get('status_id'))
        ->and($case->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('CreateCase requires valid data', function (CreateCaseRequestFactory $data, array $errors, ?Closure $setup = null) {
    if ($setup) {
        $setup();
    }

    asSuperAdmin();

    $request = collect(CreateCaseRequestFactory::new($data)->create());

    livewire(CreateCase::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(CaseModel::class, $request->except(['division_id', 'status_id', 'priority_id', 'type_id'])->toArray());
})->with(
    [
        'status_id missing' => [CreateCaseRequestFactory::new()->without('status_id'), ['status_id' => 'required']],
        'status_id does not exist' => [
            CreateCaseRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id'],
        ],
        'priority_id missing' => [CreateCaseRequestFactory::new()->without('priority_id'), ['priority_id' => 'required']],
        'priority_id does not exist' => [
            CreateCaseRequestFactory::new()->state(['priority_id' => fake()->uuid()]),
            ['priority_id'],
        ],
        'close_details is not a string' => [CreateCaseRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [CreateCaseRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

// Permission Tests

test('CreateCase is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            CaseResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateCase::class)
        ->assertForbidden();

    $user->givePermissionTo('case.view-any');
    $user->givePermissionTo('case.create');

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $user->refresh();

    $caseTypesWithManager = CaseType::factory()->create();

    $caseTypesWithManager->managers()->attach($team);

    $caseTypesWithManager->save();

    actingAs($user)
        ->get(
            CaseResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseRequestFactory::new()->create([
        'priority_id' => CasePriority::factory()->create([
            'type_id' => $caseTypesWithManager->getKey(),
        ])->getKey(),
    ]));

    livewire(CreateCase::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Prospect::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseModel::all());

    assertDatabaseHas(
        CaseModel::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority_id',
                'respondent_id',
                'type_id',
            ]
        )->toArray()
    );

    $case = CaseModel::first();

    expect($case->division->id)
        ->toEqual($request->get('division_id'))
        ->and($case->status->id)
        ->toEqual($request->get('status_id'))
        ->and($case->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('CreateCase is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->caseManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $user->refresh();

    actingAs($user)
        ->get(
            CaseResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('case.view-any');
    $user->givePermissionTo('case.create');

    livewire(CreateCase::class)
        ->assertForbidden();

    $settings->data->addons->caseManagement = true;

    $settings->save();

    $caseType = CaseType::factory()->create();

    $caseType->managers()->attach($team);

    actingAs($user)
        ->get(
            CaseResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseRequestFactory::new()->create([
        'priority_id' => CasePriority::factory()->create([
            'type_id' => $caseType->getKey(),
        ])->getKey(),
    ]));

    livewire(CreateCase::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Prospect::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CaseModel::all());

    assertDatabaseHas(CaseModel::class, $request->except(['division_id', 'respondent_id', 'type_id'])->toArray());

    $case = CaseModel::first();

    expect($case->division->getKey())->toEqual($request['division_id']);
});

test('assignment type individual manager will auto assign to new cases', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('case.view-any');
    $user->givePermissionTo('case.create');
    $user->givePermissionTo('case.*.update');

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $user->refresh();

    actingAs($user);

    $caseTypesWithManager = CaseType::factory()
        ->hasAttached(
            factory: $team,
            relationship: 'managers'
        )
        ->state([
            'assignment_type' => CaseTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $user->getKey(),
        ])
        ->create();

    $request = collect(CreateCaseRequestFactory::new()->create([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Open,
        ])->getKey(),
        'priority_id' => CasePriority::factory()->create([
            'type_id' => $caseTypesWithManager->getKey(),
        ])->getKey(),
    ]));

    livewire(CreateCase::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Prospect::factory()->create()->getKey(),
        ])
        ->call('create');

    $case = CaseModel::first();

    expect($case->assignments()->first())->user->getKey()->toBe($user->getKey()); /** @phpstan-ignore method.nonObject */
});

test('assignment type round robin will auto-assign to new cases', function () {
    asSuperAdmin();

    $team = Team::factory()
        ->has(User::factory()->licensed(LicenseType::cases())->count(3), 'users')->create();

    $caseTypeWithManager = CaseType::factory()
        ->hasAttached(
            factory: $team,
            relationship: 'managers'
        )
        ->state([
            'assignment_type' => CaseTypeAssignmentTypes::RoundRobin,
        ])
        ->create();

    $request = collect(CreateCaseRequestFactory::new()->create([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Open,
        ])->getKey(),
        'priority_id' => CasePriority::factory()->create([
            'type_id' => $caseTypeWithManager->getKey(),
        ])->getKey(),
    ]));

    $users = $team->users()->orderBy('name')->orderBy('id')->get();

    travelTo(now()->subSeconds(count($users)));

    foreach ($users as $user) {
        livewire(CreateCase::class)
            ->fillForm($request->toArray())
            ->fillForm([
                'respondent_id' => Prospect::factory()->create()->getKey(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $latestCase = CaseModel::query()->latest('id')->first();

        $getCaseType = CaseType::where('assignment_type', CaseTypeAssignmentTypes::RoundRobin->value)->first();
        expect($getCaseType->assignment_type)->toBe(CaseTypeAssignmentTypes::RoundRobin);
        expect($getCaseType->last_assigned_id)->ToBe($user->getKey());
        expect($latestCase->assignedTo->user_id)->ToBe($user->getKey());

        // This needs to be added due to a bug in Laravel
        // Laravel's uuidv4 orderedUuids is NOT same second safe
        // So until we update to UUID v7 we need to sleep for 1 second
        travelTo(now()->addSecond());
    }

    travelBack();

    livewire(CreateCase::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Prospect::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $latestCase = CaseModel::latest()->first();
    $getCaseType = CaseType::where('assignment_type', CaseTypeAssignmentTypes::RoundRobin->value)->first();
    expect($getCaseType->last_assigned_id)->toBe($team->users()->orderBy('name')->orderBy('id')->first()->getKey());
    expect($latestCase->assignedTo->user_id)->toBe($team->users()->orderBy('name')->orderBy('id')->first()->getKey());
});

test('assignment type workload will auto-assign to new cases', function () {
    asSuperAdmin();

    $team = Team::factory()
        ->has(User::factory()->licensed(LicenseType::cases())->count(5), 'users')->create();

    $factoryUsers = $team->users;
    $factoryUsers->each(fn ($user) => $user->givePermissionTo('case.*.update'));

    $caseTypeWithManager = CaseType::factory()
        ->hasAttached(
            factory: $team,
            relationship: 'managers'
        )
        ->state([
            'assignment_type' => CaseTypeAssignmentTypes::Workload,
        ])
        ->create();

    foreach ($factoryUsers->take(-2) as $factoryUser) {
        $case = CaseModel::factory()->state([
            'priority_id' => CasePriority::factory()->create([
                'type_id' => $caseTypeWithManager->getKey(),
            ])->getKey(),
            'status_id' => CaseStatus::factory()->create([
                'classification' => SystemCaseClassification::Open,
            ])->getKey(),
        ])->create();
        $case->assignments()->create([
            'user_id' => $factoryUser->getKey(),
            'assigned_at' => now(),
            'status' => CaseAssignmentStatus::Active,
        ]);

        $case = CaseModel::factory()->state([
            'priority_id' => CasePriority::factory()->create([
                'type_id' => $caseTypeWithManager->getKey(),
            ])->getKey(),
            'status_id' => CaseStatus::factory()->create([
                'classification' => SystemCaseClassification::Open,
            ])->getKey(),
        ])->create();
        $case->assignments()->create([
            'user_id' => $factoryUser->getKey(),
            'assigned_at' => now(),
            'status' => CaseAssignmentStatus::Active,
        ]);
    }

    $request = collect(CreateCaseRequestFactory::new()->create([
        'priority_id' => CasePriority::factory()->create([
            'type_id' => $caseTypeWithManager->getKey(),
        ])->getKey(),
    ]));

    travelTo(now()->subSeconds(3));

    foreach ($factoryUsers->take(3)->sortBy('id')->sortBy('name') as $user) {
        livewire(CreateCase::class)
            ->fillForm($request->toArray())
            ->fillForm([
                'respondent_id' => Prospect::factory()->create()->getKey(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $latestCase = CaseModel::query()->latest('id')->first();
        $getCaseType = CaseType::where('assignment_type', CaseTypeAssignmentTypes::Workload->value)->first();
        expect($getCaseType->assignment_type)->toBe(CaseTypeAssignmentTypes::Workload);
        expect($getCaseType->last_assigned_id)->ToBe($user->getKey());
        expect($latestCase->assignedTo->user_id)->ToBe($user->getKey());

        // This needs to be added due to a bug in Laravel
        // Laravel's uuidv4 orderedUuids is NOT same second safe
        // So until we update to UUID v7 we need to sleep for 1 second
        travelTo(now()->addSecond());
    }

    travelBack();

    livewire(CreateCase::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Prospect::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $latestCase = CaseModel::latest()->first();
    $getCaseType = CaseType::where('assignment_type', CaseTypeAssignmentTypes::Workload->value)->first();
    expect($getCaseType->last_assigned_id)->toBe($factoryUsers->take(3)->sortBy('id')->sortBy('name')->first()->getKey());
    expect($latestCase->assignedTo->user_id)->toBe($factoryUsers->take(3)->sortBy('id')->sortBy('name')->first()->getKey());
});

test('for a specific case type, which have notifications on for managers, the case is created and the managers are notified')->todo();

test('for a specific case type, which have notifications on for auditors, the case is created and the auditors are notified')->todo();

test('for a specific case type, which have notifications on for customers, the case is created and the customers are notified')->todo();
