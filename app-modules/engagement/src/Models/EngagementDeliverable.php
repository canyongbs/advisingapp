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

namespace AdvisingApp\Engagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Engagement\Drivers\EngagementSmsDriver;
use AdvisingApp\Engagement\Drivers\EngagementEmailDriver;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Enums\EngagementDeliveryStatus;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Engagement\Drivers\Contracts\EngagementDeliverableDriver;

/**
 * @mixin IdeHelperEngagementDeliverable
 */
class EngagementDeliverable extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

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

    public function markDeliveryFailed(?string $reason): void
    {
        if (! $this->hasBeenDelivered()) {
            $this->update([
                'delivery_status' => EngagementDeliveryStatus::Failed,
                'last_delivery_attempt' => now(),
                'delivery_response' => $reason,
            ]);
        }
    }

    public function driver(): EngagementDeliverableDriver
    {
        return match ($this->channel) {
            EngagementDeliveryMethod::Email => new EngagementEmailDriver($this),
            EngagementDeliveryMethod::Sms => new EngagementSmsDriver($this),
        };
    }
}
