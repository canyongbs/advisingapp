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
use AdvisingApp\Authorization\Models\UserGroup;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ManageStudentTasks;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Task\Notifications\TaskAssignedToUserNotification;
use App\Features\TaskArchivingFeature;
use App\Models\Authenticatable;
use App\Models\User;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use CanyonGBS\Common\Filament\Actions\ArchiveBulkAction;
use CanyonGBS\Common\Filament\Actions\UnarchiveAction;
use CanyonGBS\Common\Filament\Actions\UnarchiveBulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

$createTaskUser = function (): User {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo([
        'student.view-any',
        'student.*.view',
        'task.view-any',
        'task.create',
        'task.*.view',
        'task.*.update',
        'task.*.delete',
    ]);

    return $user;
};

it('can render the manage student tasks page', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $student = Student::factory()->create();

    get(StudentResource::getUrl('tasks', ['record' => $student]))
        ->assertSuccessful();
});

it('lists the tasks of the student only', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $student = Student::factory()->create();

    $studentTask = Task::factory()->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);
    $otherTask = Task::factory()->concerningStudent()->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

    Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
        ->assertCanSeeTableRecords([$studentTask])
        ->assertCanNotSeeTableRecords([$otherTask]);
});

it('can create a task related to the student and assigned to the current user by default', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $student = Student::factory()->create();

    expect(Task::query()->count())->toBe(0);

    Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
        ->mountAction(TestAction::make(CreateAction::class)->table())
        ->assertSchemaStateSet([
            'related_to' => $student->getKey(),
            'assigned_to' => $user->getKey(),
            'status' => TaskStatus::Pending,
        ])
        ->assertFormFieldDisabled('related_to')
        ->fillForm([
            'title' => 'Follow up',
            'description' => 'Call the student.',
            'status' => TaskStatus::InProgress->value,
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $task = Task::query()->sole();

    expect($task->title)->toBe('Follow up')
        ->and($task->description)->toBe('Call the student.')
        ->and($task->status)->toBe(TaskStatus::InProgress)
        ->and($task->assigned_to)->toBe($user->getKey())
        ->and($task->concern->is($student))->toBeTrue();
});

it('validates the inputs', function (array $data, array $errors) use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $student = Student::factory()->create();

    Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'title' => 'Follow up',
            'description' => 'Call the student.',
            ...$data,
        ])
        ->assertHasFormErrors($errors);

    expect(Task::query()->count())->toBe(0);
})->with([
    'title required' => [['title' => null], ['title' => 'required']],
    'title max' => [['title' => str_repeat('a', 101)], ['title' => 'max']],
    'description required' => [['description' => null], ['description' => 'required']],
]);

it('does not allow assigning a user without the student license', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $student = Student::factory()->create();

    $unlicensedUser = User::factory()->create();

    Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'title' => 'Follow up',
            'description' => 'Call the student.',
            'assigned_to' => $unlicensedUser->getKey(),
        ])
        ->assertHasFormErrors(['assigned_to']);

    expect(Task::query()->count())->toBe(0);
});

describe('assignment', function () use ($createTaskUser): void {
    $createLicensedAdmin = function (): User {
        $admin = User::factory()->licensed(LicenseType::cases())->create();

        $admin->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

        return $admin;
    };

    it('does not allow assigning an admin on create', function () use ($createTaskUser, $createLicensedAdmin): void {
        actingAs($createTaskUser());

        $student = Student::factory()->create();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'title' => 'Follow up',
                'description' => 'Call the student.',
                'assigned_to' => $createLicensedAdmin()->getKey(),
            ])
            ->assertHasFormErrors(['assigned_to']);

        expect(Task::query()->count())->toBe(0);
    });

    it('does not allow reassigning a task to an admin on edit', function () use ($createTaskUser, $createLicensedAdmin): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction(TestAction::make(EditAction::class)->table($task), [
                'assigned_to' => $createLicensedAdmin()->getKey(),
            ])
            ->assertHasFormErrors(['assigned_to']);

        expect($task->refresh()->assigned_to)->toBe($user->getKey());
    });

    it('keeps an admin who is already the assignee when editing', function () use ($createTaskUser, $createLicensedAdmin): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $admin = $createLicensedAdmin();

        $task = Task::factory()->concerningStudent($student)->assigned($admin)->create(['status' => TaskStatus::Pending, 'is_confidential' => false, 'created_by' => $user->getKey()]);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction(TestAction::make(EditAction::class)->table($task), [
                'title' => 'Updated title',
            ])
            ->assertHasNoFormErrors();

        expect($task->refresh())
            ->title->toBe('Updated title')
            ->assigned_to->toBe($admin->getKey());
    });

    it('allows an admin to assign a task to themselves', function () use ($createLicensedAdmin): void {
        $admin = $createLicensedAdmin();

        actingAs($admin);

        $student = Student::factory()->create();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'title' => 'Follow up',
                'description' => 'Call the student.',
            ])
            ->assertHasNoFormErrors();

        expect(Task::query()->sole()->assigned_to)->toBe($admin->getKey());
    });

    it('allows assigning an admin when admins are not filtered from selection', function () use ($createTaskUser, $createLicensedAdmin): void {
        config(['app.filter_admins_from_selection' => false]);

        actingAs($createTaskUser());

        $student = Student::factory()->create();

        $admin = $createLicensedAdmin();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'title' => 'Follow up',
                'description' => 'Call the student.',
                'assigned_to' => $admin->getKey(),
            ])
            ->assertHasNoFormErrors();

        expect(Task::query()->sole()->assigned_to)->toBe($admin->getKey());
    });

    it('reassigns a task while changing its status and notifies only the new assignee', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $newAssignee = User::factory()->licensed(LicenseType::cases())->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        Notification::fake();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction(TestAction::make(EditAction::class)->table($task), [
                'assigned_to' => $newAssignee->getKey(),
                'status' => TaskStatus::InProgress->value,
            ])
            ->assertHasNoFormErrors();

        expect($task->refresh())
            ->assigned_to->toBe($newAssignee->getKey())
            ->status->toBe(TaskStatus::InProgress);

        Notification::assertSentTo($newAssignee, TaskAssignedToUserNotification::class);
        Notification::assertNotSentTo($user, TaskAssignedToUserNotification::class);
    });

    it('can clear the assignee of a task', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        Notification::fake();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction(TestAction::make(EditAction::class)->table($task), [
                'assigned_to' => null,
            ])
            ->assertHasNoFormErrors();

        expect($task->refresh()->assigned_to)->toBeNull();

        Notification::assertNothingSent();
    });
});

describe('editing', function () use ($createTaskUser): void {
    it('displays the task data with the related to field disabled', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->mountAction(TestAction::make(EditAction::class)->table($task))
            ->assertSchemaStateSet([
                'title' => $task->title,
                'description' => $task->description,
                'related_to' => $student->getKey(),
                'status' => TaskStatus::Pending,
                'assigned_to' => $user->getKey(),
            ])
            ->assertFormFieldDisabled('related_to');
    });

    it('can update a task without changing its status', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction(TestAction::make(EditAction::class)->table($task), [
                'title' => 'Updated title',
            ])
            ->assertHasNoFormErrors();

        $task->refresh();

        expect($task->title)->toBe('Updated title')
            ->and($task->status)->toBe(TaskStatus::Pending)
            ->and($task->histories()->where('event', 'status_changed')->exists())->toBeFalse();
    });

    it('can update a task and change its status in a single save', function (TaskStatus $from, TaskStatus $to) use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => $from, 'is_confidential' => false]);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->removeTableFilter('status')
            ->callAction(TestAction::make(EditAction::class)->table($task), [
                'title' => 'Updated title',
                'status' => $to->value,
            ])
            ->assertHasNoFormErrors();

        $task->refresh();

        expect($task->title)->toBe('Updated title')
            ->and($task->status)->toBe($to)
            ->and($task->histories()->where('event', 'status_changed')->count())->toBe(1);
    })->with([
        'completed to pending' => [TaskStatus::Completed, TaskStatus::Pending],
        'pending to completed' => [TaskStatus::Pending, TaskStatus::Completed],
        'canceled to completed' => [TaskStatus::Canceled, TaskStatus::Completed],
    ]);
    it('keeps an archived group that is already assigned visible and removable', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $archivedGroup = UserGroup::factory()->archived()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => true, 'created_by' => $user->getKey()]);
        $task->confidentialAccessUserGroups()->attach($archivedGroup);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->mountAction(TestAction::make(EditAction::class)->table($task))
            ->assertSchemaStateSet(['confidential_task_user_groups' => [$archivedGroup->getKey()]])
            ->fillForm(['confidential_task_user_groups' => []])
            ->callMountedAction()
            ->assertHasNoFormErrors();

        expect($task->confidentialAccessUserGroups()->exists())->toBeFalse();
    });
});

describe('row click', function () use ($createTaskUser): void {
    it('opens the edit slide-over when the user can update the task', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        $component = Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()]);

        $page = $component->instance();

        assert($page instanceof ManageStudentTasks);

        expect($page->getTable()->getRecordAction($task))->toBe('edit');

        $component->mountAction(TestAction::make('edit')->table($task))
            ->assertActionMounted(TestAction::make('edit')->table($task));
    });

    it('opens the view slide-over when the user cannot update the task', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned()->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        $component = Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()]);

        $page = $component->instance();

        assert($page instanceof ManageStudentTasks);

        expect($page->getTable()->getRecordAction($task))->toBe('view');

        $component->mountAction(TestAction::make('view')->table($task))
            ->assertActionMounted(TestAction::make('view')->table($task));
    });
});

describe('archiving', function () use ($createTaskUser): void {
    it('can archive a task from the edit slide-over', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        expect($task->isArchived())->toBeFalse();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->callAction([
                TestAction::make(EditAction::class)->table($task),
                TestAction::make(ArchiveAction::class),
            ]);

        expect($task->refresh()->isArchived())->toBeTrue();
    });

    it('can unarchive a task from the edit slide-over', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false, 'archived_at' => now()]);

        expect($task->isArchived())->toBeTrue();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->removeTableFilter('withoutArchived')
            ->callAction([
                TestAction::make(EditAction::class)->table($task),
                TestAction::make(UnarchiveAction::class),
            ])
            ->assertActionNotMounted(TestAction::make(EditAction::class)->table($task));

        expect($task->refresh()->isArchived())->toBeFalse();
    });

    it('bulk archives tasks, skipping those the user cannot delete', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $tasks = Task::factory()->count(2)->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);
        $deniedTask = Task::factory()->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        Gate::before(fn (User $user, string $ability, array $arguments): ?bool => (($ability === 'delete') && ($arguments[0] ?? null)?->is($deniedTask)) ? false : null);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->selectTableRecords([...$tasks, $deniedTask])
            ->callAction(TestAction::make(ArchiveBulkAction::class)->table()->bulk());

        expect($tasks->every(fn (Task $task): bool => $task->refresh()->isArchived()))->toBeTrue()
            ->and($deniedTask->refresh()->isArchived())->toBeFalse();
    });

    it('bulk unarchives tasks, skipping those the user cannot delete', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $tasks = Task::factory()->count(2)->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false, 'archived_at' => now()]);
        $deniedTask = Task::factory()->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false, 'archived_at' => now()]);

        Gate::before(fn (User $user, string $ability, array $arguments): ?bool => (($ability === 'delete') && ($arguments[0] ?? null)?->is($deniedTask)) ? false : null);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->removeTableFilter('withoutArchived')
            ->selectTableRecords([...$tasks, $deniedTask])
            ->callAction(TestAction::make(UnarchiveBulkAction::class)->table()->bulk());

        expect($tasks->every(fn (Task $task): bool => ! $task->refresh()->isArchived()))->toBeTrue()
            ->and($deniedTask->refresh()->isArchived())->toBeTrue();
    });

    it('hides the archive bulk actions when task archiving is inactive', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        TaskArchivingFeature::deactivate();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->assertActionHidden(TestAction::make(ArchiveBulkAction::class)->table()->bulk())
            ->assertActionHidden(TestAction::make(UnarchiveBulkAction::class)->table()->bulk());
    });
});

describe('filters', function () use ($createTaskUser): void {
    it('hides archived tasks by default and shows them when the filter is removed', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);
        $archivedTask = Task::factory()->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false, 'archived_at' => now()]);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->assertCanSeeTableRecords([$task])
            ->assertCanNotSeeTableRecords([$archivedTask])
            ->assertTableColumnHidden('archived')
            ->removeTableFilter('withoutArchived')
            ->assertCanSeeTableRecords([$task, $archivedTask])
            ->assertTableColumnVisible('archived')
            ->assertTableColumnStateSet('archived', true, $archivedTask)
            ->assertTableColumnStateSet('archived', false, $task);
    });

    it('shows archived tasks without an archived column when task archiving is inactive', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $archivedTask = Task::factory()->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false, 'archived_at' => now()]);

        TaskArchivingFeature::deactivate();

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->assertCanSeeTableRecords([$archivedTask])
            ->assertTableColumnHidden('archived');
    });
});

describe('authorization', function () use ($createTaskUser): void {
    it('denies access to the manage student tasks page without the `task.view-any` permission', function (): void {
        $student = Student::factory()->create();

        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo(['student.view-any', 'student.*.view']);

        actingAs($user);

        get(StudentResource::getUrl('tasks', ['record' => $student]))
            ->assertForbidden();
    });

    it('allows access to the manage student tasks page with the `task.view-any` permission', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        actingAs($createTaskUser());

        get(StudentResource::getUrl('tasks', ['record' => $student]))
            ->assertSuccessful();
    });

    it('hides the create action without the `task.create` permission', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $student = Student::factory()->create();

        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo(['student.view-any', 'student.*.view', 'task.view-any']);

        actingAs($user);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->assertActionHidden(TestAction::make(CreateAction::class)->table());
    });

    it('allows a super admin to access the manage student tasks page', function (): void {
        asSuperAdmin();

        $student = Student::factory()->create();

        get(StudentResource::getUrl('tasks', ['record' => $student]))
            ->assertSuccessful();
    });

    it('does not allow access to the manage student tasks page without the student license', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['student.view-any', 'student.*.view', 'task.view-any']);

        actingAs($user);

        $student = Student::factory()->create();

        get(StudentResource::getUrl('tasks', ['record' => $student]))
            ->assertNotFound();
    });

    it('hides the edit action for a task the user cannot update', function () use ($createTaskUser): void {
        actingAs($createTaskUser());

        $student = Student::factory()->create();

        $task = Task::factory()->concerningStudent($student)->assigned()->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

        Livewire::test(ManageStudentTasks::class, ['record' => $student->getKey()])
            ->assertActionHidden(TestAction::make('edit')->table($task))
            ->assertActionVisible(TestAction::make('view')->table($task));
    });
});
