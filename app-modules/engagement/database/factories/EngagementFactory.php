<?php

namespace Assist\Engagement\Database\Factories;

use App\Models\User;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Engagement\Models\Engagement>
 */
class EngagementFactory extends Factory
{
    public function definition(): array
    {
        $recipient = $this->faker->randomElement([
            Student::factory()->create(),
            Prospect::factory()->create(),
        ]);

        return [
            'user_id' => User::factory(),
            'recipient_id' => $recipient->id,
            'recipient_type' => $recipient->getMorphClass(),
            'subject' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'deliver_at' => $this->faker->dateTime,
        ];
    }

    public function forStudent(): self
    {
        return $this->state([
            'recipient_id' => Student::factory(),
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
            'deliver_at' => $this->faker->dateTimeBetween('+1 day', '+1 week'),
        ]);
    }
}
