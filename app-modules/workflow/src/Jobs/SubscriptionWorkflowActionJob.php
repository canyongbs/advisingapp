<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Notification\Actions\SubscriptionCreate;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use AdvisingApp\Workflow\Models\WorkflowSubscriptionDetails;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubscriptionWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Subscribable);

            $details = WorkflowSubscriptionDetails::whereId($this->workflowRunStep->details_id)->first();

            if($details->remove_prior) {
                $educatable->subscriptions()->delete();
            }

            /** @var array<Subscription> $subscriptions */
            $subscriptions = [];

            foreach($details->user_ids as $userId) {
                $subscriptions[] = resolve(SubscriptionCreate::class)
                    ->handle(User::find($userId), $educatable);
            }

            foreach($subscriptions as $subscription) {
                WorkflowRunStepRelated::create([
                'workflow_run_step_id' => $this->workflowRunStep->id,
                'related' => $subscription,
            ]);
            }

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}
