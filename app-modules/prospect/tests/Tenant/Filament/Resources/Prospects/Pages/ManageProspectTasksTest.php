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
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ManageProspectTasks;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Models\User;
use CanyonGBS\Common\Filament\Actions\ArchiveBulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

$createTaskUser = function (): User {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo([
        'prospect.view-any',
        'prospect.*.view',
        'task.view-any',
        'task.create',
        'task.*.view',
        'task.*.update',
        'task.*.delete',
    ]);

    return $user;
};

it('can render the manage prospect tasks page', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $prospect = Prospect::factory()->create();

    get(ProspectResource::getUrl('tasks', ['record' => $prospect]))
        ->assertSuccessful();
});

it('lists the tasks of the prospect only', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $prospect = Prospect::factory()->create();

    $prospectTask = Task::factory()->concerningProspect($prospect)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);
    $otherTask = Task::factory()->concerningProspect()->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

    Livewire::test(ManageProspectTasks::class, ['record' => $prospect->getKey()])
        ->assertCanSeeTableRecords([$prospectTask])
        ->assertCanNotSeeTableRecords([$otherTask]);
});

it('can create a task related to the prospect and assigned to the current user by default', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $prospect = Prospect::factory()->create();

    expect(Task::query()->count())->toBe(0);

    Livewire::test(ManageProspectTasks::class, ['record' => $prospect->getKey()])
        ->mountAction(TestAction::make(CreateAction::class)->table())
        ->assertSchemaStateSet([
            'related_to' => $prospect->getKey(),
            'assigned_to' => $user->getKey(),
        ])
        ->assertFormFieldDisabled('related_to')
        ->fillForm([
            'title' => 'Follow up',
            'description' => 'Call the prospect.',
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $task = Task::query()->sole();

    expect($task->status)->toBe(TaskStatus::Pending)
        ->and($task->assigned_to)->toBe($user->getKey())
        ->and($task->concern->is($prospect))->toBeTrue();
});

it('opens the edit or view slide-over on row click depending on whether the user can update the task', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $prospect = Prospect::factory()->create();

    $ownTask = Task::factory()->concerningProspect($prospect)->assigned($user)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);
    $otherTask = Task::factory()->concerningProspect($prospect)->assigned()->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

    $page = Livewire::test(ManageProspectTasks::class, ['record' => $prospect->getKey()])->instance();

    assert($page instanceof ManageProspectTasks);

    expect($page->getTable()->getRecordAction($ownTask))->toBe('edit')
        ->and($page->getTable()->getRecordAction($otherTask))->toBe('view');
});

it('can update a task and change its status with the related to field disabled', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $prospect = Prospect::factory()->create();

    $task = Task::factory()->concerningProspect($prospect)->assigned($user)->create(['status' => TaskStatus::Completed, 'is_confidential' => false]);

    Livewire::test(ManageProspectTasks::class, ['record' => $prospect->getKey()])
        ->removeTableFilter('status')
        ->mountAction(TestAction::make(EditAction::class)->table($task))
        ->assertSchemaStateSet([
            'related_to' => $prospect->getKey(),
            'status' => TaskStatus::Completed,
        ])
        ->assertFormFieldDisabled('related_to')
        ->fillForm([
            'title' => 'Updated title',
            'status' => TaskStatus::Pending->value,
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $task->refresh();

    expect($task->title)->toBe('Updated title')
        ->and($task->status)->toBe(TaskStatus::Pending)
        ->and($task->concern->is($prospect))->toBeTrue()
        ->and($task->histories()->where('event', 'status_changed')->count())->toBe(1);
});

it('allows assigning a user with the prospect license only', function () use ($createTaskUser): void {
    actingAs($createTaskUser());

    $prospect = Prospect::factory()->create();

    $recruitmentUser = User::factory()->licensed([LicenseType::RecruitmentCrm])->create();

    Livewire::test(ManageProspectTasks::class, ['record' => $prospect->getKey()])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'title' => 'Follow up',
            'description' => 'Call the prospect.',
            'assigned_to' => $recruitmentUser->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect(Task::query()->sole()->assigned_to)->toBe($recruitmentUser->getKey());
});

it('does not allow assigning a user without the prospect license', function () use ($createTaskUser): void {
    actingAs($createTaskUser());

    $prospect = Prospect::factory()->create();

    $retentionUser = User::factory()->licensed([LicenseType::RetentionCrm])->create();

    Livewire::test(ManageProspectTasks::class, ['record' => $prospect->getKey()])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'title' => 'Follow up',
            'description' => 'Call the prospect.',
            'assigned_to' => $retentionUser->getKey(),
        ])
        ->assertHasFormErrors(['assigned_to']);

    expect(Task::query()->count())->toBe(0);
});

it('bulk archives tasks', function () use ($createTaskUser): void {
    $user = $createTaskUser();

    actingAs($user);

    $prospect = Prospect::factory()->create();

    $tasks = Task::factory()->count(2)->concerningProspect($prospect)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);

    expect($tasks->contains(fn (Task $task): bool => $task->isArchived()))->toBeFalse();

    Livewire::test(ManageProspectTasks::class, ['record' => $prospect->getKey()])
        ->selectTableRecords($tasks)
        ->callAction(TestAction::make(ArchiveBulkAction::class)->table()->bulk())
        ->assertCanNotSeeTableRecords($tasks);

    expect($tasks->every(fn (Task $task): bool => $task->refresh()->isArchived()))->toBeTrue();
});

describe('authorization', function () use ($createTaskUser): void {
    it('denies access to the manage prospect tasks page without the `task.view-any` permission', function (): void {
        $prospect = Prospect::factory()->create();

        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo(['prospect.view-any', 'prospect.*.view']);

        actingAs($user);

        get(ProspectResource::getUrl('tasks', ['record' => $prospect]))
            ->assertForbidden();
    });

    it('hides the create action for a prospect converted to a student', function () use ($createTaskUser): void {
        $user = $createTaskUser();

        actingAs($user);

        $prospect = Prospect::factory()->create();

        $prospect->student()->associate(Student::factory()->create())->save();

        Livewire::test(ManageProspectTasks::class, ['record' => $prospect->getKey()])
            ->assertActionHidden(TestAction::make(CreateAction::class)->table());
    });
});
