<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Team\Models\Team;
use AdvisingApp\Workflow\Models\WorkflowCaseDetails;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowCaseDetails>
 */
class WorkflowCaseDetailsFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $priority = CasePriority::inRandomOrder()->first() ?? CasePriority::factory()->create();

        return [
            'division_id' => Division::inRandomOrder()->first()->id ?? Division::factory(),
            'status_id' => CaseStatus::inRandomOrder()->first()->id ?? CaseStatus::factory(),
            'priority_id' => $priority->getKey(),
            'assigned_to_id' => User::factory()->for(Team::factory()->hasAttached($priority->type, relationship: 'manageableCaseTypes')),
            'close_details' => $this->faker->sentence(),
            'res_details' => $this->faker->sentence(),
        ];
    }
}
