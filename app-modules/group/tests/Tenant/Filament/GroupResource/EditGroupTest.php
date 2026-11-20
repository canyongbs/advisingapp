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
use AdvisingApp\Group\Actions\TranslateGroupFilters;
use AdvisingApp\Group\Filament\Resources\Groups\GroupResource;
use AdvisingApp\Group\Filament\Resources\Groups\Pages\EditGroup;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\Term;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('EditGroup is gated with proper access control', function (): void {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $group = Group::factory()->create();

    actingAs($user)
        ->get(
            GroupResource::getUrl('edit', ['record' => $group])
        )->assertForbidden();

    $user->givePermissionTo('group.view-any');
    $user->givePermissionTo('group.*.update');

    actingAs($user)
        ->get(
            GroupResource::getUrl('edit', ['record' => $group])
        )->assertSuccessful();
});

test('EditGroup does not allow duplicate group names case insensitively, ignoring soft-deleted records', function (): void {
    asSuperAdmin();

    $deletedGroup = Group::factory()->create(['name' => 'Group Name']);
    $group = Group::factory()->create(['name' => 'Test Group']);
    Group::factory()->create(['name' => 'Other Group']);

    $deletedGroup->delete();

    Livewire::test(EditGroup::class, ['record' => $group->getRouteKey()])
        ->fillForm(['name' => 'group name'])
        ->call('save')
        ->assertHasNoFormErrors(['name']);

    Livewire::test(EditGroup::class, ['record' => $group->getRouteKey()])
        ->fillForm(['name' => 'OTHER group'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

// The group builder renders the shared `StudentsTable`, which scopes its own query
// independently of `GroupModel::query()`.
test('the group builder does not list archived students', function (): void {
    asSuperAdmin();

    $group = Group::factory()->student()->dynamic()->create();

    $active = Student::factory()->count(2)->create();
    $archived = Student::factory()->create();
    $archived->archive();

    Livewire::test(EditGroup::class, ['record' => $group->getRouteKey()])
        ->assertOk()
        ->assertCanSeeTableRecords($active)
        ->assertCanNotSeeTableRecords([$archived]);
});

test('the group builder applies enrollment rules with a count of 0, like the group does', function (): void {
    asSuperAdmin();

    $term = Term::factory()->create(['name' => 'FA-20']);

    $enrolledStudent = Student::factory()->create();
    Enrollment::factory()->create([
        'sisid' => $enrolledStudent->sisid,
        'sis_term_id' => $term->sis_term_id,
    ]);

    $unenrolledStudent = Student::factory()->create();

    $group = Group::factory()->student()->dynamic()->create([
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'rule' => [
                        'type' => 'enrollments',
                        'data' => [
                            'operator' => 'equals',
                            'settings' => ['count' => '0', 'semesters' => ['FA-20']],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(app(TranslateGroupFilters::class)->execute($group)->pluck('sisid')->all())->toBe([$unenrolledStudent->sisid]);

    Livewire::test(EditGroup::class, ['record' => $group->getRouteKey()])
        ->assertCanSeeTableRecords([$unenrolledStudent])
        ->assertCanNotSeeTableRecords([$enrolledStudent]);
});

test('the group builder applies an enrollment rule that matches every student when it is alone in an OR group, like the group does', function (): void {
    asSuperAdmin();

    $smith = Student::factory()->create(['last' => 'Smith']);
    $jones = Student::factory()->create(['last' => 'Jones']);

    $group = Group::factory()->student()->dynamic()->create([
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'or' => [
                        'type' => 'or',
                        'data' => [
                            'groups' => [
                                ['rules' => [
                                    'enrollments' => [
                                        'type' => 'enrollments',
                                        'data' => ['operator' => 'hasMin', 'settings' => ['count' => '0']],
                                    ],
                                ]],
                                ['rules' => [
                                    'name' => [
                                        'type' => 'name',
                                        'data' => ['operator' => 'lastName', 'settings' => ['operator' => 'contains', 'text' => 'Smith']],
                                    ],
                                ]],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect(app(TranslateGroupFilters::class)->execute($group)->pluck('sisid')->all())
        ->toEqualCanonicalizing([$smith->sisid, $jones->sisid]);

    Livewire::test(EditGroup::class, ['record' => $group->getRouteKey()])
        ->assertCanSeeTableRecords([$smith, $jones]);
});

test('EditGroup warns how many members cannot be emailed or texted', function (): void {
    asSuperAdmin();

    $group = Group::factory()->student()->dynamic()->create();

    Student::factory()->create();

    $studentWithoutEmail = Student::factory()->create();
    $studentWithoutEmail->primaryEmailAddress()->dissociate()->save();

    $studentWithoutPhone = Student::factory()->create();
    $studentWithoutPhone->primaryPhoneNumber()->dissociate()->save();

    Livewire::test(EditGroup::class, ['record' => $group->getRouteKey()])
        ->assertSee('Of the 3 students who will be a member of this group, 33.33% are unable to receive emails to their institutional email address and 33.33% are unable to receive SMS to their primary phone on file.');
});

test('EditGroup does not warn when every member can be emailed and texted', function (): void {
    asSuperAdmin();

    $group = Group::factory()->student()->dynamic()->create();

    Student::factory()->count(2)->create();

    Livewire::test(EditGroup::class, ['record' => $group->getRouteKey()])
        ->assertDontSee('who will be a member of this group');
});
