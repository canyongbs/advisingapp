<?php

namespace Assist\Engagement\Database\Factories;

use Assist\Engagement\Models\Engagement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Enums\EngagementDeliveryStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Engagement\Models\EngagementDeliverable>
 */
class EngagementDeliverableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'engagement_id' => Engagement::factory(),
            'channel' => fake()->randomElement(EngagementDeliveryMethod::cases()),
            'delivery_status' => EngagementDeliveryStatus::Awaiting,
            'delivered_at' => null,
            'delivery_response' => null,
        ];
    }

    public function email(): self
    {
        return $this->state([
            'channel' => EngagementDeliveryMethod::EMAIL,
        ]);
    }

    public function sms(): self
    {
        return $this->state([
            'channel' => EngagementDeliveryMethod::SMS,
        ]);
    }

    public function deliverySuccessful(): self
    {
        return $this->state([
            'delivery_status' => EngagementDeliveryStatus::Successful,
            'delivered_at' => now(),
        ]);
    }

    public function deliveryFailed(): self
    {
        return $this->state([
            'delivery_status' => EngagementDeliveryStatus::Failed,
            'delivered_at' => null,
            'delivery_response' => 'The deliverable was not successfully delivered.',
        ]);
    }
}
