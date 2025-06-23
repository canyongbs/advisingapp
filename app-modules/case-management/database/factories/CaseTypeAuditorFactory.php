<?php

namespace AdvisingApp\CaseManagement\Database\Factories;

use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Models\CaseTypeAuditor;
use AdvisingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseTypeAuditor>
 */
class CaseTypeAuditorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_type_id' => CaseType::factory(),
            'team_id' => Team::factory(),
        ];
    }
}
