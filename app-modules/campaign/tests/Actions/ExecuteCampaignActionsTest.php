<?php

use Illuminate\Support\Facades\Queue;
use Assist\Campaign\Models\CampaignAction;
use Assist\Campaign\Actions\ExecuteCampaignAction;
use Assist\Campaign\Actions\ExecuteCampaignActions;

it('will only dispatch jobs for actions that are scheduled and have not yet been executed', function () {
    $actionToBeExecuted = CampaignAction::factory()->create([
        'execute_at' => now()->subMinute(),
    ]);

    CampaignAction::factory()
        ->successfulExecution(now()->subMinute());

    Queue::fake([ExecuteCampaignAction::class]);

    ExecuteCampaignActions::dispatch();

    Queue::assertPushed(ExecuteCampaignAction::class, 1);
    Queue::assertPushed(ExecuteCampaignAction::class, function (ExecuteCampaignAction $job) use ($actionToBeExecuted) {
        return $job->action->is($actionToBeExecuted);
    });
});
