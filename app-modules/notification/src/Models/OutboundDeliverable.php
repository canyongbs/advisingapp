<?php

namespace AdvisingApp\Notification\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;

class OutboundDeliverable extends BaseModel
{
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
    ];

    protected $casts = [
        'channel' => NotificationChannel::class,
        'delivered_at' => 'datetime',
        'delivery_status' => NotificationDeliveryStatus::class,
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
                'delivery_status' => NotificationDeliveryStatus::Successful,
                'delivered_at' => now(),
                'last_delivery_attempt' => now(),
            ]);
        }
    }

    public function markDeliveryFailed(string $reason): void
    {
        if (! $this->hasBeenDelivered()) {
            $this->update([
                'delivery_status' => NotificationDeliveryStatus::Failed,
                'last_delivery_attempt' => now(),
                'delivery_response' => $reason,
            ]);
        }
    }
}
