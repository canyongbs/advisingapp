<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Pipeline\Jobs\PruneEducatablePipelineStagesForPipeline;
use AdvisingApp\Pipeline\Models\EducatablePipelineStage;
use AdvisingApp\Pipeline\Models\Pipeline;
use AdvisingApp\Pipeline\Models\PipelineStage;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('deletes educatable pipeline stages for prospects outside a segment', function () {
    $prospect = Prospect::factory()->create();

    /** @var Segment $segment */
    $segment = Segment::factory()->create([
        'model' => SegmentModel::Prospect,
        'type' => SegmentType::Dynamic,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    [
                        'type' => 'first_name',
                        'data' => [
                            'operator' => 'contains',
                            'settings' => [
                                'text' => 'invalid',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    /** @var Pipeline $pipeline */
    $pipeline = Pipeline::factory()
        ->for($segment)
        ->create();

    /** @var PipelineStage $pipelineStage */
    $pipelineStage = PipelineStage::factory()
        ->for($pipeline)
        ->create();

    $educatablePipelineStage = new EducatablePipelineStage();
    $educatablePipelineStage->pipeline()->associate($pipeline);
    $educatablePipelineStage->stage()->associate($pipelineStage);
    $educatablePipelineStage->educatable()->associate($prospect);
    $educatablePipelineStage->save();

    dispatch(new PruneEducatablePipelineStagesForPipeline($pipeline));

    assertDatabaseMissing(EducatablePipelineStage::class, [
        'pipeline_id' => $pipeline->getKey(),
        'pipeline_stage_id' => $pipelineStage->getKey(),
        'educatable_type' => 'prospect',
        'educatable_id' => $prospect->getKey(),
    ]);
});

it('does not delete educatable pipeline stages for prospects inside a segment', function () {
    $prospect = Prospect::factory()->create();

    /** @var Segment $segment */
    $segment = Segment::factory()->create([
        'model' => SegmentModel::Prospect,
        'type' => SegmentType::Dynamic,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    [
                        'type' => 'first_name',
                        'data' => [
                            'operator' => 'contains',
                            'settings' => [
                                'text' => $prospect->first_name,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    /** @var Pipeline $pipeline */
    $pipeline = Pipeline::factory()
        ->for($segment)
        ->create();

    /** @var PipelineStage $pipelineStage */
    $pipelineStage = PipelineStage::factory()
        ->for($pipeline)
        ->create();

    $educatablePipelineStage = new EducatablePipelineStage();
    $educatablePipelineStage->pipeline()->associate($pipeline);
    $educatablePipelineStage->stage()->associate($pipelineStage);
    $educatablePipelineStage->educatable()->associate($prospect);
    $educatablePipelineStage->save();

    dispatch(new PruneEducatablePipelineStagesForPipeline($pipeline));

    assertDatabaseHas(EducatablePipelineStage::class, [
        'pipeline_id' => $pipeline->getKey(),
        'pipeline_stage_id' => $pipelineStage->getKey(),
        'educatable_type' => 'prospect',
        'educatable_id' => $prospect->getKey(),
    ]);
});
