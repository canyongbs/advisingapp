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

use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

it('will create the task records for educatables in the segment', function (Collection $educatables) {
    expect(Task::count())->toBe(0);

    $segment = Segment::factory()->create([
        'type' => SegmentType::Static,
    ]);

    $educatables->each(function (Educatable $prospect) use ($segment) {
        $segment->subjects()->create([
            'subject_id' => $prospect->getKey(),
            'subject_type' => $prospect->getMorphClass(),
        ]);
    });

    $campaign = Campaign::factory()->create([
        'segment_id' => $segment->id,
    ]);

    $data = [
        'title' => 'Title',
        'description' => 'This is a description.',
        'due' => now()->addDay(),
        'assigned_to' => User::factory()->create()->id,
        'created_by' => User::factory()->create()->id,
    ];

    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Task,
            'data' => $data,
        ]);

    $action->execute();

    expect(Task::count())->toBe($educatables->count());

    $educatables->each(function (Educatable $educatable) use ($data) {
        expect($educatable->tasks()->count())->toBe(1);

        $task = $educatable->tasks()->first();
        expect($task->title)->toBe($data['title']);
        expect($task->description)->toBe($data['description']);
        expect($task->due->toString())->toBe($data['due']->toString());
        expect($task->assigned_to)->toBe($data['assigned_to']);
        expect($task->created_by)->toBe($data['created_by']);
        expect($task->concern_id)->toBe($educatable->getKey());
        expect($task->concern_type)->toBe($educatable->getMorphClass());
    });
})->with([
    'prospects' => [
        fn () => Prospect::factory()->count(3)->create(),
    ],
    'students' => [
        fn () => Student::factory()->count(3)->create(),
    ],
]);
