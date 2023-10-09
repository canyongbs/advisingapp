<?php

namespace Assist\Engagement\Database\Factories;

use App\Models\User;
use Assist\Prospect\Models\Prospect;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Factory<Engagement>
 */
class EngagementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipient_type' => fake()->randomElement([
                (new Student())->getMorphClass(),
                (new Prospect())->getMorphClass(),
            ]),
            'recipient_id' => function (array $attributes) {
                $senderClass = Relation::getMorphedModel($attributes['sender_type']);

                /** @var Student|Prospect $senderModel */
                $senderModel = new $senderClass();

                $sender = $senderClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $senderModel::factory()->create();

                return $sender->getKey();
            },
            'subject' => fake()->sentence,
            'body' => fake()->paragraph,
            'deliver_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
        ];
    }

    public function forStudent(): self
    {
        return $this->state([
            'recipient_id' => Student::inRandomOrder()->first()->sisid ?? Student::factory(),
            'recipient_type' => (new Student())->getMorphClass(),
        ]);
    }

    public function forProspect(): self
    {
        return $this->state([
            'recipient_id' => Prospect::factory(),
            'recipient_type' => (new Prospect())->getMorphClass(),
        ]);
    }

    public function deliverNow(): self
    {
        return $this->state([
            'deliver_at' => now(),
        ]);
    }

    public function deliverLater(): self
    {
        return $this->state([
            'deliver_at' => fake()->dateTimeBetween('+1 day', '+1 week'),
        ]);
    }

    public function ofBatch(): self
    {
        return $this->state([
            'engagement_batch_id' => EngagementBatch::factory(),
        ]);
    }
}
