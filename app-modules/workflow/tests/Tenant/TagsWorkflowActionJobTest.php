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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Workflow\Jobs\TagsWorkflowActionJob;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowTagsDetails;
use App\Enums\TagType;
use App\Models\Tag;
use Illuminate\Support\Facades\Bus;

it('will execute appropriately on each educatable in the group', function (Educatable $educatable, bool $removePrior) {
    Bus::fake();

    $workflowRun = WorkflowRun::factory()->create([
        'related_type' => $educatable->getMorphClass(),
        'related_id' => $educatable->getKey(),
    ]);

    $priorTags = $educatable->tags()->pluck('tag_id');

    $tags = Tag::factory(3)->create([
        'type' => match ($educatable->getMorphClass()) {
            'student' => TagType::Student,
            'prospect' => TagType::Prospect,
            default => null,
        },
    ]);

    $tagsDetails = WorkflowTagsDetails::factory()->create([
        'tag_ids' => $tags->pluck('id')->toArray(),
        'remove_prior' => $removePrior,
    ]);

    $workflowRunStep = WorkflowRunStep::factory()->withDetails($tagsDetails)->create([
        'workflow_run_id' => $workflowRun->id,
        'execute_at' => now(),
    ]);

    expect($workflowRunStep->succeeded_at)->toBeNull()
        ->and($workflowRunStep->last_failed_at)->toBeNull();

    [$job] = (new TagsWorkflowActionJob($workflowRunStep))->withFakeBatch();

    $job->handle();

    expect($educatable->tags()->pluck('tag_id')->toArray())
        ->toEqualCanonicalizing(
            $removePrior
                ? $tags->pluck('id')->toArray()
                : [...$priorTags, ...$tags->pluck('id')->toArray()]
        );

    expect($workflowRunStep->succeeded_at)->not()->toBeNull()
        ->and($workflowRunStep->last_failed_at)->toBeNull();

    $relatedModel = $workflowRunStep->workflowRun->related;
    assert($relatedModel instanceof Educatable);
    expect($relatedModel->tags)->toHaveCount(
        $removePrior
            ? $tags->count()
            : count($priorTags) + $tags->count()
    );

    $expectedTagIds = $removePrior
        ? $tags->pluck('id')->toArray()
        : [...$priorTags, ...$tags->pluck('id')->toArray()];

    $relatedModel->tags()
        ->each(function (Tag $tag) use ($expectedTagIds) {
            expect($tag)->toBeInstanceOf(Tag::class);

            expect($tag->getKey())->toBeIn($expectedTagIds);
        });
})->with([
    'no prior tags | prospect | remove prior false' => [
        fn () => Prospect::factory()->create(),
        false,
    ],
    'no prior tags | prospect | remove prior true' => [
        fn () => Prospect::factory()->create(),
        true,
    ],
    'prior tags | prospect | remove prior false' => [
        fn () => Prospect::factory()->hasAttached(Tag::factory(3)->create(['type' => TagType::Prospect]))->create(),
        false,
    ],
    'prior tags | prospect | remove prior true' => [
        fn () => Prospect::factory()->hasAttached(Tag::factory(3)->create(['type' => TagType::Prospect]))->create(),
        true,
    ],
    'no prior tags | student | remove prior false' => [
        fn () => Student::factory()->create(),
        false,
    ],
    'no prior tags | student | remove prior true' => [
        fn () => Student::factory()->create(),
        true,
    ],
    'prior tags | student | remove prior false' => [
        fn () => Student::factory()->hasAttached(Tag::factory(3)->create(['type' => TagType::Student]))->create(),
        false,
    ],
    'prior tags | student | remove prior true' => [
        fn () => Student::factory()->hasAttached(Tag::factory(3)->create(['type' => TagType::Student]))->create(),
        true,
    ],
]);

it('will only apply the correct tags to an educatable', function () {
    Bus::fake();

    $student = Student::factory()->create();
    $prospect = Prospect::factory()->create();

    $studentWorkflowRun = WorkflowRun::factory()->create([
        'related_type' => $student->getMorphClass(),
        'related_id' => $student->getKey(),
    ]);

    $prospectWorkflowRun = WorkflowRun::factory()->create([
        'related_type' => $prospect->getMorphClass(),
        'related_id' => $prospect->getKey(),
    ]);

    $studentTag = Tag::factory()->create(['type' => TagType::Student]);
    $prospectTag = Tag::factory()->create(['type' => TagType::Prospect]);

    $tagsDetails = WorkflowTagsDetails::factory()->create(['tag_ids' => [$studentTag->id, $prospectTag->id]]);

    $studentWorkflowRunStep = WorkflowRunStep::factory()->withDetails($tagsDetails)->create([
        'workflow_run_id' => $studentWorkflowRun->id,
        'execute_at' => now(),
    ]);
    $prospectWorkflowRunStep = WorkflowRunStep::factory()->withDetails($tagsDetails)->create([
        'workflow_run_id' => $prospectWorkflowRun->id,
        'execute_at' => now(),
    ]);

    expect($studentWorkflowRunStep->succeeded_at)->toBeNull()
        ->and($studentWorkflowRunStep->last_failed_at)->toBeNull()
        ->and($prospectWorkflowRunStep->succeeded_at)->toBeNull()
        ->and($prospectWorkflowRunStep->last_failed_at)->toBeNull();

    [$studentJob] = (new TagsWorkflowActionJob($studentWorkflowRunStep))->withFakeBatch();
    [$prospectJob] = (new TagsWorkflowActionJob($prospectWorkflowRunStep))->withFakeBatch();

    $studentJob->handle();
    $prospectJob->handle();

    expect($student->tags()->pluck('tag_id')->toArray())->toEqual([$studentTag->id]);
    expect($prospect->tags()->pluck('tag_id')->toArray())->toEqual([$prospectTag->id]);

    expect($studentWorkflowRunStep->succeeded_at)->not()->toBeNull()
        ->and($studentWorkflowRunStep->last_failed_at)->toBeNull()
        ->and($prospectWorkflowRunStep->succeeded_at)->not()->toBeNull()
        ->and($prospectWorkflowRunStep->last_failed_at)->toBeNull();

    $relatedStudent = $studentWorkflowRunStep->workflowRun->related;
    assert($relatedStudent instanceof Student);
    expect($relatedStudent->tags)->toHaveCount(1);
    
    $relatedProspect = $prospectWorkflowRunStep->workflowRun->related;
    assert($relatedProspect instanceof Prospect);
    expect($relatedProspect->tags)->toHaveCount(1);
});
