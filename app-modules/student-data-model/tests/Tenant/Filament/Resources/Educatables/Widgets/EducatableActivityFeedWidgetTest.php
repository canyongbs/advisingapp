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

use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableActivityFeedWidget;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Histories\TaskHistory;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Timeline\Models\Timeline;
use App\Features\TaskArchivingFeature;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

$getTaskTimelineSubjects = function (Student $student): array {
    $component = Livewire::test(EducatableActivityFeedWidget::class, [
        'educatable' => $student,
        'viewUrl' => StudentResource::getUrl('view', ['record' => $student]),
    ]);

    $widget = $component->instance();

    assert($widget instanceof EducatableActivityFeedWidget);

    return $widget->timelineRecords
        ->map(fn (Timeline $timeline): mixed => $timeline->timelineable)
        ->filter(fn (mixed $timelineable): bool => $timelineable instanceof TaskHistory)
        ->map(fn (TaskHistory $history): string => $history->subject_id)
        ->unique()
        ->values()
        ->all();
};

it('does not show the history of archived tasks', function () use ($getTaskTimelineSubjects): void {
    asSuperAdmin();

    $student = Student::factory()->create();

    $task = Task::factory()->concerningStudent($student)->create(['is_confidential' => false]);
    $archivedTask = Task::factory()->concerningStudent($student)->create(['is_confidential' => false]);

    expect($getTaskTimelineSubjects($student))->toEqualCanonicalizing([$task->getKey(), $archivedTask->getKey()]);

    $archivedTask->archive();

    expect($getTaskTimelineSubjects($student))->toBe([$task->getKey()]);
});

it('shows the history of archived tasks when task archiving is inactive', function () use ($getTaskTimelineSubjects): void {
    asSuperAdmin();

    $student = Student::factory()->create();

    $archivedTask = Task::factory()->concerningStudent($student)->create(['is_confidential' => false, 'archived_at' => now()]);

    TaskArchivingFeature::deactivate();

    expect($getTaskTimelineSubjects($student))->toBe([$archivedTask->getKey()]);
});
