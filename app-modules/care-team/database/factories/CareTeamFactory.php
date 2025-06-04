<?php

namespace AdvisingApp\CareTeam\Database\Factories;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareTeam>
 */
class CareTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'educatable_id' => null,
            'educatable_type' => null,
            'care_team_role_id' => CareTeamRole::factory(),
        ];
    }
}
