<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use Exception;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Support\Facades\DB;
use Throwable;

class EngagementEmailWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    /**
     * @return array<object>
     */
    public function middleware(): array
    {
        return [
            ...parent::middleware(),
            new RateLimitedWithRedis('notification'),
        ];
    }

    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);

            $details = WorkflowEngagementEmailDetails::whereId($this->workflowRunStep->details_id)->first();

            throw_if(NotificationChannel::parse($details->channel) !== NotificationChannel::Email,
            new Exception('The notification channel is not email.'));

            //get and assert user
            
            //create engagement
            
            //make WorkflowRunStepRelated model

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        } 
    }
}
