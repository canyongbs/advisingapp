<?php

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
