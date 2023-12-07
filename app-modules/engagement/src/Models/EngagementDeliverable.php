<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Assist\Engagement\Drivers\SmsDriver;
use Assist\Engagement\Drivers\EmailDriver;
use Assist\LaravelAuditing\Contracts\Auditable;
use Assist\Engagement\Drivers\DeliverableDriver;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Enums\EngagementDeliveryStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Engagement\Actions\QueuedEngagementDelivery;
use Assist\Engagement\Actions\EngagementSmsChannelDelivery;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Engagement\Actions\EngagementEmailChannelDelivery;
use Assist\Engagement\Exceptions\UnknownDeliveryMethodException;

/**
 * @mixin IdeHelperEngagementDeliverable
 */
class EngagementDeliverable extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'external_reference_id',
        'external_status',
        'channel',
        'delivery_status',
        'delivered_at',
        'last_delivery_attempt',
        'delivery_response',
    ];

    protected $casts = [
        'channel' => EngagementDeliveryMethod::class,
        'delivery_status' => EngagementDeliveryStatus::class,
        'delivered_at' => 'datetime',
        'last_delivery_attempt' => 'datetime',
    ];

    public function engagement(): BelongsTo
    {
        return $this->belongsTo(Engagement::class);
    }

    public function hasBeenDelivered(): bool
    {
        return ! is_null($this->delivered_at);
    }

    public function markDeliverySuccessful(): void
    {
        if (! $this->hasBeenDelivered()) {
            $this->update([
                'delivery_status' => EngagementDeliveryStatus::Successful,
                'delivered_at' => now(),
                'last_delivery_attempt' => now(),
            ]);
        }
    }

    public function markDeliveryFailed(string $reason): void
    {
        if (! $this->hasBeenDelivered()) {
            $this->update([
                'delivery_status' => EngagementDeliveryStatus::Failed,
                'last_delivery_attempt' => now(),
                'delivery_response' => $reason,
            ]);
        }
    }

    public function driver(): DeliverableDriver
    {
        return match ($this->channel) {
            EngagementDeliveryMethod::Email => new EmailDriver($this),
            EngagementDeliveryMethod::Sms => new SmsDriver($this),
        };
    }

    // TODO We can move this to the "driver"
    public function jobForDelivery(): QueuedEngagementDelivery
    {
        return match ($this->channel) {
            EngagementDeliveryMethod::Email => new EngagementEmailChannelDelivery($this),
            EngagementDeliveryMethod::Sms => new EngagementSmsChannelDelivery($this),
            default => throw new UnknownDeliveryMethodException("Delivery channel '{$this->channel}' is not supported."),
        };
    }

    // TODO We can move this to the "driver"
    public function deliver(): void
    {
        match ($this->channel) {
            EngagementDeliveryMethod::Email => EngagementEmailChannelDelivery::dispatch($this),
            EngagementDeliveryMethod::Sms => EngagementSmsChannelDelivery::dispatch($this),
            default => throw new UnknownDeliveryMethodException("Delivery channel '{$this->channel}' is not supported."),
        };
    }
}
