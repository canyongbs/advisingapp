<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Engagement\Actions\CreateEngagement;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowEngagementSmsDetails;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use App\Models\User;
use Exception;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Support\Facades\DB;
use Throwable;

class EngagementSmsWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    public function middleware(): array
    {
        /**
         * @return array<object>
         */
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

            $details = WorkflowEngagementSmsDetails::whereId($this->workflowRunStep->details_id)->first();

            throw_if(
                NotificationChannel::parse($details->channel) !== NotificationChannel::Sms,
                new Exception('The notification channel is not SMS.')
            );

            $user = $this->workflowRunStep->workflowRun->workflowTrigger->createdBy;

            assert($user instanceof User);

            $engagement = app(CreateEngagement::class)
                ->execute(
                    data: new EngagementCreationData(
                        user: $user,
                        recipient: $educatable,
                        channel: NotificationChannel::Sms,
                        body: $details->body,
                    ),
                    notifyNow: true,
                );
            
            $engagement->refresh();

            WorkflowRunStepRelated::create([
                'workflow_run_step_id' => $this->workflowRunStep->id,
                'related' => $engagement,
            ]);

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}
