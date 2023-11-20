<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;
use Assist\Task\Models\Task;
use Assist\Campaign\Models\Campaign;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\Collection;
use Assist\Campaign\Enums\CampaignActionType;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\AssistDataModel\Models\Contracts\Educatable;

it('will create the task records for educatables in the caseload', function (Collection $educatables) {
    expect(Task::count())->toBe(0);

    $caseload = Caseload::factory()->create([
        'type' => CaseloadType::Static,
    ]);

    $educatables->each(function (Educatable $prospect) use ($caseload) {
        $caseload->subjects()->create([
            'subject_id' => $prospect->getKey(),
            'subject_type' => $prospect->getMorphClass(),
        ]);
    });

    $campaign = Campaign::factory()->create([
        'caseload_id' => $caseload->id,
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
        'educatables' => fn () => Prospect::factory()->count(3)->create(),
    ],
    'students' => [
        'educatables' => fn () => Student::factory()->count(3)->create(),
    ],
]);
