<?php

namespace AdvisingApp\Notification\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;

class OutboundDeliverable extends BaseModel
{
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
        'channel' => NotificationChannel::class,
        'delivery_status' => NotificationDeliveryStatus::class,
        'delivered_at' => 'datetime',
        'last_delivery_attempt' => 'datetime',
    ];

    // The "related" relationship is whatever entity we might need to tie this back to
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

    public function markDeliverySuccessful(): void
    {
        if (! $this->hasBeenDelivered()) {
            $this->update([
                // TODO Utilize global delivery statuses
                'delivery_status' => 'success',
                'delivered_at' => now(),
                'last_delivery_attempt' => now(),
            ]);
        }
    }

    public function markDeliveryFailed(string $reason): void
    {
        if (! $this->hasBeenDelivered()) {
            $this->update([
                // TODO Utilize global delivery statuses
                'delivery_status' => 'failed',
                'last_delivery_attempt' => now(),
                'delivery_response' => $reason,
            ]);
        }
    }
}
