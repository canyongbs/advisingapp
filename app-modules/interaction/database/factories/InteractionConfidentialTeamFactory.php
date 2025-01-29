<?php

namespace AdvisingApp\Interaction\Database\Factories;

use AdvisingApp\Interaction\Models\InteractionConfidentialTeam;
use AdvisingApp\Team\Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InteractionConfidentialTeam>
 */
class InteractionConfidentialTeamFactory extends Factory
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
            'team_id' => TeamFactory::new()->create(),
        ];
    }
}
