<?php

namespace AdvisingApp\CareTeam\Database\Factories;

use App\Enums\CareTeamRoleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\CareTeam\Models\CareTeamRole>
 */
class CareTeamRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //name, type, is_default
            'name' => fake()->word(),
            'type' => fake()->randomElement(CareTeamRoleType::cases())->value,
            'is_default' => false,
        ];
    }
}
