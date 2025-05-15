<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class EngagementCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->actionEducatable->educatable;

            throw_if(
                ! $educatable instanceof Educatable,
                new Exception('The educatable model must implement the Educatable contract.')
            );

            $action = $this->actionEducatable->campaignAction;

            // TODO: Change this to send individual Engagements
            // Add the notifications middleware to this job
            // and work with Dan to figure out temporaryBodyImages

            $channel = NotificationChannel::parse($action->data['channel']);
            $records = $action->campaign->segment->retrieveRecords();

            app(CreateEngagementBatch::class)->execute(new EngagementCreationData(
                user: $action->campaign->createdBy,
                recipient: ($channel === NotificationChannel::Sms) ? $records->filter(fn (CanBeNotified $record) => $record->canReceiveSms()) : $records,
                channel: $channel,
                subject: $action->data['subject'] ?? null,
                body: $action->data['body'] ?? null,
            ));

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable->related()->associate($case);
            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
