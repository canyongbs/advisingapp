<?php

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
