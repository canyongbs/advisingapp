<?php

namespace AdvisingApp\CaseManagement\Database\Factories;

use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Models\CaseTypeManager;
use AdvisingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseTypeManager>
 */
class CaseTypeManagerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'case_type_id' => CaseType::factory(),
            'team_id' => Team::factory(),
        ];
    }
}
