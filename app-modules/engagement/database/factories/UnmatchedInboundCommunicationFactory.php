<?php

namespace AdvisingApp\Engagement\Database\Factories;

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\UnmatchedInboundCommunication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnmatchedInboundCommunication>
 */
class UnmatchedInboundCommunicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(EngagementResponseType::cases()),
            'sender' => function ($attributes) {
                return match ($attributes['type']) {
                    EngagementResponseType::Email => $this->faker->email(),
                    EngagementResponseType::Sms => $this->faker->phoneNumber(),
                    default => $this->faker->phoneNumber(),
                };
            },
            'occurred_at' => now(),
            'subject' => function ($attributes) {
                return match ($attributes['type']) {
                    EngagementResponseType::Email => $this->faker->sentence(),
                    EngagementResponseType::Sms => null,
                    default => null,
                };
            },
            'body' => $this->faker->sentence(),
        ];
    }
}
