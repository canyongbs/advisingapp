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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\StudentDataModel\Filament\Pages\RetentionCrmDashboard;
use AdvisingApp\StudentDataModel\Filament\Widgets\StudentsActionCenterWidget;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('renders all students correctly in the retention dashboard', function () {
    $allStudents = Student::factory()->has(
        Task::factory()->state(['status' => TaskStatus::Pending, 'is_confidential' => false]),
        'tasks'
    )->count(2)->create();

    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    $studentsWithSubscription = Student::factory()->has(
        Subscription::factory()->state([
            'user_id' => $user->getKey(),
        ]),
        'subscriptions'
    )->count(2)->create();

    $careTeamRole = CareTeamRole::factory()->create();

    $studentsWithCareTeam = Student::factory()
        ->hasAttached(
            $user,
            ['care_team_role_id' => $careTeamRole->getKey()],
            'careTeam'
        )
        ->count(1)
        ->create();

    livewire(StudentsActionCenterWidget::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords(
            $allStudents
                ->merge($studentsWithSubscription)
                ->merge($studentsWithCareTeam)
        );
});

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(RetentionCrmDashboard::getUrl())->assertForbidden();

    $user->grantLicense(LicenseType::RetentionCrm);

    $user->refresh();

    get(RetentionCrmDashboard::getUrl())->assertForbidden();

    $user->givePermissionTo('report-library.view-any');

    get(RetentionCrmDashboard::getUrl())->assertSuccessful();
});
