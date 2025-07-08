<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\Engagement\Actions\CreateEngagement;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use App\Models\User;
use DateTimeInterface;
use Exception;
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

            assert($educatable instanceof Educatable);

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
                        campaignAction: $action,
                    ),
                    notifyNow: true,
                );

            $engagement->refresh();

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable
                ->related()
                ->make()
                ->related()
                ->associate($engagement)
                ->save();

            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
