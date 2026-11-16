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
use AdvisingApp\Report\Enums\ReportAccessKey;
use AdvisingApp\Report\Filament\Pages\Students;
use AdvisingApp\Report\Models\ReportDepartmentAccess;
use AdvisingApp\Report\Models\ReportUserAccess;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Term;
use AdvisingApp\Team\Models\Department;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('is gated with proper access control', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    get(Students::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RetentionCrm);

    $user->refresh();

    get(Students::getUrl())->assertForbidden();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::Students->value,
        'user_id' => $user->getKey(),
    ]);

    get(Students::getUrl())->assertSuccessful();
});

it('grants access to a user belonging to a department that has been granted access', function (): void {
    $department = Department::factory()->create();

    $user = User::factory()->create(['team_id' => $department->getKey()]);

    $user->grantLicense(LicenseType::RetentionCrm);

    $user->refresh();

    actingAs($user);

    get(Students::getUrl())->assertForbidden();

    ReportDepartmentAccess::factory()->create([
        'report_key' => ReportAccessKey::Students->value,
        'team_id' => $department->getKey(),
    ]);

    get(Students::getUrl())->assertSuccessful();
});

describe('live filter from the URL', function (): void {
    /**
     * @param array<string, mixed> $rule
     *
     * @return array<string, mixed>
     */
    $livePopulation = fn (array $rule): array => [
        'type' => 'live',
        'liveFilters' => ['queryBuilder' => ['rules' => ['aBcD' => $rule]]],
    ];

    it('converts legacy enrolled term rules to the rules that compare matched term names', function () use ($livePopulation): void {
        asSuperAdmin();

        $term = Term::factory()->create(['name' => 'FA-20']);
        Enrollment::factory()->create(['sis_term_id' => $term->sis_term_id, 'semester_name' => 'Fall 2020']);

        Livewire::withQueryParams(['population' => $livePopulation([
            'type' => 'firstEnrollmentTerm.semester_name',
            'data' => ['operator' => 'is', 'settings' => ['values' => ['fall 2020', 'never enrolled']]],
        ])])
            ->test(Students::class)
            ->assertSet('population', $livePopulation([
                'type' => 'firstEnrolledTerm',
                'data' => ['operator' => 'is', 'settings' => ['values' => ['fa-20', 'never enrolled']]],
            ]));
    });

    it('removes a live filter that uses a filter that is no longer available', function () use ($livePopulation): void {
        asSuperAdmin();

        Livewire::withQueryParams(['population' => $livePopulation([
            'type' => 'removedFilter',
            'data' => ['operator' => 'is', 'settings' => ['values' => ['x']]],
        ])])
            ->test(Students::class)
            ->assertSet('population', null)
            ->assertNotified('Live filter removed');
    });

    it('keeps a live filter that only uses available filters', function () use ($livePopulation): void {
        asSuperAdmin();

        $population = $livePopulation([
            'type' => 'firstEnrolledTerm',
            'data' => ['operator' => 'is', 'settings' => ['values' => ['fa-20']]],
        ]);

        Livewire::withQueryParams(['population' => $population])
            ->test(Students::class)
            ->assertSet('population', $population)
            ->assertNotNotified();
    });
});
