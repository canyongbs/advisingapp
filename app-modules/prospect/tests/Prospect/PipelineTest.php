<?php

use function Tests\asSuperAdmin;

use Illuminate\Support\Facades\Queue;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\Prospect\Models\Pipeline;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Prospect\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Sequence;
use AdvisingApp\Prospect\Jobs\SyncPipelineEducatableJob;
use AdvisingApp\Prospect\Jobs\DeletePipelineEducatableJob;
use AdvisingApp\Prospect\Jobs\PipelineEducatablesMoveIntoStages;

it('can attach educatables to the pipeline', function () {
    Queue::fake();

    asSuperAdmin();

    $educatables = Prospect::factory()->count(5)->create();

    $pipeline = Pipeline::factory()
        ->has(
            PipelineStage::factory()
                ->count(2)
                ->state(function (array $attributes, Pipeline $pipeline) {
                    return ['is_default' => $attributes['is_default'] ?? false];
                })
                ->state(new Sequence(
                    ['is_default' => true, 'order' => 1],
                    ['is_default' => false, 'order' => 2]
                )),
            'stages'
        )
        ->for(Segment::factory()->state([
            'model' => SegmentModel::Prospect,
        ]), 'segment')
        ->create();

    $job = new PipelineEducatablesMoveIntoStages($pipeline);

    dispatch($job);

    Queue::assertPushed(PipelineEducatablesMoveIntoStages::class, 1);

    $job->handle();

    expect($pipeline->educatables)
        ->toHaveCount($educatables->count());

    $defaultStage = $pipeline->stages()->where('is_default', true)->first()->getKey();

    $pipeline->educatables->each(function ($educatable) use ($defaultStage) {
        expect($educatable->pivot->pipeline_stage_id)->toBe($defaultStage);
    });
});

it('can sync educatables to the pipeline', function () {
    Queue::fake();

    asSuperAdmin();

    $educatables = Prospect::factory()->count(3)->create();

    $pipeline = Pipeline::factory()
        ->has(
            PipelineStage::factory()
                ->count(2)
                ->state(function (array $attributes, Pipeline $pipeline) {
                    return ['is_default' => $attributes['is_default'] ?? false];
                })
                ->state(new Sequence(
                    ['is_default' => true, 'order' => 1],
                    ['is_default' => false, 'order' => 2]
                )),
            'stages'
        )
        ->for(Segment::factory()->state([
            'model' => SegmentModel::Prospect,
        ]), 'segment')
        ->create();

    $job = new PipelineEducatablesMoveIntoStages($pipeline);

    dispatch($job);

    Queue::assertPushed(PipelineEducatablesMoveIntoStages::class, 1);

    $addMoreEducatables = Prospect::factory()->count(3)->create();

    $job = new SyncPipelineEducatableJob($pipeline);

    dispatch($job);

    Queue::assertPushed(SyncPipelineEducatableJob::class, 1);

    $job->handle();

    expect($pipeline->educatables)
        ->toHaveCount($educatables->toBase()->merge($addMoreEducatables)->count());
});

it('can detach educatables from pipeline if they removed from segment', function () {
    Queue::fake();

    asSuperAdmin();

    $educatables = Prospect::factory()->count(5)->create();

    $pipeline = Pipeline::factory()
        ->has(
            PipelineStage::factory()
                ->count(2)
                ->state(function (array $attributes, Pipeline $pipeline) {
                    return ['is_default' => $attributes['is_default'] ?? false];
                })
                ->state(new Sequence(
                    ['is_default' => true, 'order' => 1],
                    ['is_default' => false, 'order' => 2]
                )),
            'stages'
        )
        ->for(Segment::factory()->state([
            'model' => SegmentModel::Prospect,
        ]), 'segment')
        ->create();

    $job = new PipelineEducatablesMoveIntoStages($pipeline);

    dispatch($job);

    Queue::assertPushed(PipelineEducatablesMoveIntoStages::class, 1);

    $job->handle();

    $oldeducatables = $educatables;

    $educatablesToRemove = $educatables->take(2);

    Prospect::whereIn('id', $educatablesToRemove->pluck('id'))->delete();

    $educatables = Prospect::all();

    $deleteJob = new DeletePipelineEducatableJob($pipeline);

    dispatch($deleteJob);

    Queue::assertPushed(DeletePipelineEducatableJob::class, 1);

    $deleteJob->handle();

    expect($pipeline->educatables)
        ->toHaveCount($educatables->count())
        ->not->toHaveCount($oldeducatables->count());
});
