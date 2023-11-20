<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
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
        $this->update([
            'delivery_status' => EngagementDeliveryStatus::Successful,
            'delivered_at' => now(),
            'last_delivery_attempt' => now(),
        ]);
    }

    public function markDeliveryFailed(string $reason): void
    {
        $this->update([
            'delivery_status' => EngagementDeliveryStatus::Failed,
            'last_delivery_attempt' => now(),
            'delivery_response' => $reason,
        ]);
    }

    public function jobForDelivery(): QueuedEngagementDelivery
    {
        return match ($this->channel) {
            EngagementDeliveryMethod::Email => new EngagementEmailChannelDelivery($this),
            EngagementDeliveryMethod::Sms => new EngagementSmsChannelDelivery($this),
            default => throw new UnknownDeliveryMethodException("Delivery channel '{$this->channel}' is not supported."),
        };
    }

    public function deliver(): void
    {
        match ($this->channel) {
            EngagementDeliveryMethod::Email => EngagementEmailChannelDelivery::dispatch($this),
            EngagementDeliveryMethod::Sms => EngagementSmsChannelDelivery::dispatch($this),
            default => throw new UnknownDeliveryMethodException("Delivery channel '{$this->channel}' is not supported."),
        };
    }
}
