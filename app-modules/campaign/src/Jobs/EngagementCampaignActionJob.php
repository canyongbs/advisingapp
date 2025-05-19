<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\Engagement\Actions\CreateEngagement;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Support\Facades\DB;
use Throwable;

class EngagementCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
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

    public function retryUntil(): DateTimeInterface
    {
        return now()->addHours(2);
    }

    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->actionEducatable->educatable;

            throw_if(
                ! $educatable instanceof Educatable,
                new Exception('The educatable model must implement the Educatable contract.')
            );

            /** @var Educatable<Student|Prospect>&Model $educatable */
            $action = $this->actionEducatable->campaignAction;

            $channel = NotificationChannel::parse($action->data['channel']);

            throw_if(
                ! match ($channel) {
                    NotificationChannel::Email => $educatable->canReceiveEmail(),
                    NotificationChannel::Sms => $educatable->canReceiveSms(),
                    default => throw new Exception('Invalid engagement channel'),
                },
                new Exception('The educatable cannot receive notifications on this channel.')
            );

            $user = $action->campaign->createdBy;

            throw_if(
                ! $user instanceof User,
                new Exception('The user must be an instance of User.')
            );

            $engagement = app(CreateEngagement::class)
                ->execute(
                    data: new EngagementCreationData(
                        user: $user,
                        recipient: $educatable,
                        channel: $channel,
                        subject: $action->data['subject'] ?? null,
                        body: $action->data['body'] ?? null,
                    ),
                    notifyNow: true,
                );

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable->related()->associate($engagement);
            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
