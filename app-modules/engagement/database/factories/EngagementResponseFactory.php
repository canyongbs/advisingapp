<?php

namespace Assist\Engagement\Database\Factories;

use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Factory<EngagementResponse>
 */
class EngagementResponseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sender_type' => fake()->randomElement([
                (new Student())->getMorphClass(),
                (new Prospect())->getMorphClass(),
            ]),
            'sender_id' => function (array $attributes) {
                $senderClass = Relation::getMorphedModel($attributes['sender_type']);

                /** @var Student|Prospect $senderModel */
                $senderModel = new $senderClass();

                $sender = $senderClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $senderModel::factory()->create();

                return $sender->getKey();
            },
            'content' => fake()->sentence(),
            'sent_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
        ];
    }
}
