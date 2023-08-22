<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Assist\Engagement\Actions\EngagementSmsChannel;
use Assist\Engagement\Actions\EngagementEmailChannel;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Engagement\Exceptions\UnknownDeliveryMethodException;

class EngagementDeliverable extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'sent',
        'response',
    ];

    protected $casts = [
        'channel' => EngagementDeliveryMethod::class,
        'sent' => 'boolean',
    ];

    public function engagement(): BelongsTo
    {
        return $this->belongsTo(Engagement::class);
    }

    public function hasBeenSent(): bool
    {
        return ! is_null($this->sent_at);
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
