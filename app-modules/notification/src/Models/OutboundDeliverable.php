<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Notification\Models;

use Carbon\Carbon;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\Notification\Drivers\SmsDriver;
use AdvisingApp\Timeline\Models\CustomTimeline;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Notification\Drivers\EmailDriver;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use AdvisingApp\Notification\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use AdvisingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Timeline\Timelines\OutboundDeliverableTimeline;
use AdvisingApp\Notification\Observers\OutboundDeliverableObserver;
use AdvisingApp\Notification\Drivers\Contracts\OutboundDeliverableDriver;

/**
 * @mixin IdeHelperOutboundDeliverable
 */
#[ObservedBy([OutboundDeliverableObserver::class])]
class OutboundDeliverable extends BaseModel implements ProvidesATimeline
{
    use SoftDeletes;

    protected $fillable = [
        'channel',
        'content',
        'delivered_at',
        'delivery_response',
        'delivery_status',
        'external_reference_id',
        'external_status',
        'last_delivery_attempt',
        'notification_class',
        'recipient_id',
        'recipient_type',
        'related_id',
        'related_type',
        'quota_usage',
    ];

    protected $casts = [
        'channel' => NotificationChannel::class,
        'delivered_at' => 'datetime',
        'delivery_status' => NotificationDeliveryStatus::class,
        'last_delivery_attempt' => 'datetime',
        'content' => 'array',
    ];

    public array $timelineables = [
        CaseModel::class,
    ];

    public function related(): MorphTo
    {
        return $this->morphTo(
            name: 'related',
            type: 'related_type',
            id: 'related_id',
        );
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo(
            name: 'recipient',
            type: 'recipient_type',
            id: 'recipient_id',
        );
    }

    public function hasBeenDelivered(): bool
    {
        return ! is_null($this->delivered_at);
    }

    public function markDeliverySuccessful(?Carbon $at = null): void
    {
        if (! $this->hasBeenDelivered()) {
            $this->update([
                'delivery_status' => NotificationDeliveryStatus::Successful,
                'delivered_at' => $at ?? now(),
                'last_delivery_attempt' => $at ?? now(),
            ]);
        }
    }

    public function markDeliveryFailed(?string $reason): void
    {
        if (! $this->hasBeenDelivered()) {
            $this->update([
                'delivery_status' => NotificationDeliveryStatus::Failed,
                'last_delivery_attempt' => now(),
                'delivery_response' => $reason,
            ]);
        }
    }

    public function driver(): OutboundDeliverableDriver
    {
        return match ($this->channel) {
            NotificationChannel::Email => new EmailDriver($this),
            NotificationChannel::Sms => new SmsDriver($this),
        };
    }

    public function timeline(): CustomTimeline
    {
        return new OutboundDeliverableTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->deliverables()->get();
    }
}
