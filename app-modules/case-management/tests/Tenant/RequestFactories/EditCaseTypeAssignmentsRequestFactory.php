<?php

namespace AdvisingApp\CaseManagement\Tests\Tenant\RequestFactories;

use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Worksome\RequestFactories\RequestFactory;

class EditCaseTypeAssignmentsRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'assignment_type' => fake()->randomElement(CaseTypeAssignmentTypes::cases())->value,
        ];
    }

    public function withRandomTypeNotIncludingIndividual(): static
    {
        return $this->state([
            'assignment_type' => fake()->randomElement(array_filter(CaseTypeAssignmentTypes::cases(), fn (CaseTypeAssignmentTypes $type) => $type !== CaseTypeAssignmentTypes::Individual))->value,
        ]);
    }

    public function withIndividualType(): static
    {
        return $this->state([
            'assignment_type' => CaseTypeAssignmentTypes::Individual->value,
        ]);
    }

    public function withIndividualId(?Team $team = null): static
    {
        $userFactory = User::factory();

        if ($team) {
            $userFactory = $userFactory->for(
                factory: $team,
                relationship: 'team'
            );
        }

        return $this->state([
            'assignment_type_individual_id' => $userFactory,
        ]);
    }
}
