<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
