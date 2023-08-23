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
        // TODO Add some better handling of the connection between status, delivered_at, and delivery_response

        return [
            'engagement_id' => Engagement::factory(),
            'channel' => $this->faker->randomElement(EngagementDeliveryMethod::cases()),
            'delivery_status' => EngagementDeliveryStatus::AWAITING,
            'delivered_at' => null,
            'delivery_response' => $this->faker->paragraph,
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
}
