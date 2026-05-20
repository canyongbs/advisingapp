<?php

use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Workflow\Jobs\TagsWorkflowActionJob;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowTagsDetails;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Enums\TagType;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('will execute appropriateonly on each educatable in the group', function (Educatable $educatable, bool $removePrior) {
    Bus::fake();

    $workflowRun = WorkflowRun::factory()->create([
        'related_type' => $educatable->getMorphClass(),
        'related_id' => $educatable->getKey(),
    ]);
    
    $priorTags = $educatable->tags()->pluck('tag_id');

    $tags = Tag::factory(3)->create([
        'type' => match($educatable->getMorphClass()) {
            'student' => TagType::Student,
            'prospect' => TagType::Prospect,
            default => null,
        }
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