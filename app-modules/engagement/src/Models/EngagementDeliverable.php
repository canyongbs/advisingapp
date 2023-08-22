<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Assist\Engagement\Actions\EngagementSmsChannel;
use Assist\Engagement\Actions\EngagementEmailChannel;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Enums\EngagementDeliveryStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Engagement\Exceptions\UnknownDeliveryMethodException;

class EngagementDeliverable extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'delivery_status',
        'delivered_at',
        'delivery_response',
    ];

    protected $casts = [
        'channel' => EngagementDeliveryMethod::class,
        'delivery_status' => EngagementDeliveryStatus::class,
        'delivered_at' => 'datetime',
    ];

    public function engagement(): BelongsTo
    {
        return $this->belongsTo(Engagement::class);
    }

    public function hasBeenDelivered(): bool
    {
        return ! is_null($this->delivered_at);
    }

    public function send(): void
    {
        match ($this->channel) {
            EngagementDeliveryMethod::EMAIL => EngagementEmailChannel::dispatch($this),
            EngagementDeliveryMethod::SMS => EngagementSmsChannel::dispatch($this),
            default => throw new UnknownDeliveryMethodException("Delivery channel '{$this->channel}' is not supported."),
        };
    }
}
