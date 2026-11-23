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
use AdvisingApp\Group\Filament\Resources\Groups\GroupResource;
use AdvisingApp\Group\Filament\Resources\Groups\Pages\ListGroups;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

$loadPopulationMetrics = fn (Group $group): array => [
    'email_issues' => ['column' => 'email_issues', 'record' => $group->getKey()],
    'sms_issues' => ['column' => 'sms_issues', 'record' => $group->getKey()],
    'population_count' => ['column' => 'population_count', 'record' => $group->getKey()],
];

$assertPopulationMetrics = fn (string $emailIssues, string $smsIssues, string $populationCount): Closure => function (array $html) use ($emailIssues, $smsIssues, $populationCount): bool {
    expect(trim(strip_tags($html['email_issues'])))->toBe($emailIssues)
        ->and(trim(strip_tags($html['sms_issues'])))->toBe($smsIssues)
        ->and(trim(strip_tags($html['population_count'])))->toBe($populationCount);

    return true;
};

test('ListGroups is gated with proper access control', function (): void {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            GroupResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('group.view-any');

    actingAs($user)
        ->get(
            GroupResource::getUrl('index')
        )->assertSuccessful();
});

test('ListGroups shows how many members each group has, and how many cannot be emailed or texted', function () use ($loadPopulationMetrics, $assertPopulationMetrics): void {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    asSuperAdmin($user);

    $group = Group::factory()->student()->dynamic()->for($user)->create();

    Student::factory()->create();

    $studentWithoutEmail = Student::factory()->create();
    $studentWithoutEmail->primaryEmailAddress()->dissociate()->save();

    $studentWithoutPhone = Student::factory()->create();
    $studentWithoutPhone->primaryPhoneNumber()->dissociate()->save();

    Livewire::test(ListGroups::class)
        ->call('loadTableColumnStates', $loadPopulationMetrics($group))
        ->assertReturned($assertPopulationMetrics('33.33%', '33.33%', '3'));
});

it('lazy loads the population metrics of each group', function () use ($loadPopulationMetrics, $assertPopulationMetrics): void {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    asSuperAdmin($user);

    $students = Student::factory()->count(4)->create();
    $students[0]->update(['primary_email_id' => null]);
    $students[1]->update(['primary_phone_id' => null]);
    $students[2]->update(['primary_phone_id' => null]);

    $group = Group::factory()->student()->static()->for($user, 'user')->create();

    foreach ($students as $student) {
        $group->subjects()->create([
            'subject_id' => $student->getKey(),
            'subject_type' => $student->getMorphClass(),
        ]);
    }

    $emptyGroup = Group::factory()->student()->static()->for($user, 'user')->create();

    Livewire::test(ListGroups::class)
        ->call('loadTableColumnStates', $loadPopulationMetrics($group))
        ->assertReturned($assertPopulationMetrics('25.00%', '50.00%', '4'))
        ->call('loadTableColumnStates', $loadPopulationMetrics($emptyGroup))
        ->assertReturned($assertPopulationMetrics('0.00%', '0.00%', '0'));
});

it('keeps the `Owner` column visibility consistent between requests', function (array $queryParams, bool $isOwnerColumnHidden): void {
    asSuperAdmin();

    $component = Livewire::withQueryParams($queryParams)->test(ListGroups::class);

    $initialTableColumns = $component->get('tableColumns');

    $component->call('$refresh');

    expect($component->get('tableColumns'))->toBe($initialTableColumns);

    $isOwnerColumnHidden
        ? $component->assertTableColumnHidden('user.name')
        : $component->assertTableColumnVisible('user.name');
})->with([
    'only my groups by default' => [[], true],
    'only my groups from the query string' => [['filters' => ['my_groups' => ['isActive' => 'true']]], true],
    'all groups from the query string' => [['filters' => ['my_groups' => ['isActive' => 'false']]], false],
]);
