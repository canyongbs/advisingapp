<?php

namespace Assist\Engagement\Database\Factories;

use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Engagement\Models\EngagementResponse>
 */
class EngagementResponseFactory extends Factory
{
    public function definition(): array
    {
        $sender = fake()->randomElement([
            Student::class,
            Prospect::class,
        ]);

        $sender = $sender::factory()->create();

        return [
            'sender_id' => $sender->id ?? $sender->sisid,
            'sender_type' => $sender->getMorphClass(),
            'content' => fake()->sentence(),
            'sent_at' => fake()->dateTimeBetween('+1 day', '+1 week'),
        ];
    }
}
