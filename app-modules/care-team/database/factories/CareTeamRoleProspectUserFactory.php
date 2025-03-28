<?php

namespace AdvisingApp\CareTeam\Database\Factories;

use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Prospect\Models\Prospect;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\CareTeam\Models\CareTeamRoleProspectUser>
 */
class CareTeamRoleProspectUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'care_team_role_id' => CareTeamRole::factory(),
            'prospect_id' => Prospect::factory(),
            'user_id' => User::factory(),
        ];
    }
}
