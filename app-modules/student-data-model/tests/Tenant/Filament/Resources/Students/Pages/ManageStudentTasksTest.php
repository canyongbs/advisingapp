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
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ManageStudentTasks;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

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
});
