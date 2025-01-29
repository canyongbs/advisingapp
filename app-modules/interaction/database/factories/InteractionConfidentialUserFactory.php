<?php

namespace AdvisingApp\Interaction\Database\Factories;

use AdvisingApp\Interaction\Models\InteractionConfidentialUser;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InteractionConfidentialUser>
 */
class InteractionConfidentialUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'interaction_id' => InteractionFactory::new()->create(),
            'user_id' => UserFactory::new()->create(),
        ];
    }
}
