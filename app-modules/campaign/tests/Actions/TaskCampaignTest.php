<?php

use App\Models\User;
use Assist\Task\Models\Task;
use Assist\Campaign\Models\Campaign;
use Assist\Prospect\Models\Prospect;
use Assist\Campaign\Models\CampaignAction;
use Assist\Campaign\Enums\CampaignActionType;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadType;

it('will create the task records for educatables in the caseload', function () {
    expect(Task::count())->toBe(0);

    $prospects = Prospect::factory()->count(3)->create([
        'first_name' => 'TestTest',
    ]);

    $caseload = Caseload::factory()->create([
        'type' => CaseloadType::Static,
    ]);

    $prospects->each(function (Prospect $prospect) use ($caseload) {
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

    expect(Task::count())->toBe($prospects->count());

    $task = Task::first();
    $prospect = $prospects->first();

    expect($task->title)->toBe($data['title']);
    expect($task->description)->toBe($data['description']);
    expect($task->due->toString())->toBe($data['due']->toString());
    expect($task->assigned_to)->toBe($data['assigned_to']);
    expect($task->concern_id)->toBe($prospect->getKey());
    expect($task->concern_type)->toBe($prospect->getMorphClass());
});
