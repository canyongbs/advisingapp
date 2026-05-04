<?php

namespace AdvisingApp\Engagement\Database\Factories;

use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class EngagementResponseActionedNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by_id' => User::factory(),
            'engagement_response_id' => EngagementResponse::factory(),
            'note' => $this->faker->paragraph(),
        ];
    }
}
