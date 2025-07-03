<?php

namespace AdvisingApp\CaseManagement\Tests\Tenant\RequestFactories;

use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\Team\Models\Team;
use App\Models\User;
use Worksome\RequestFactories\RequestFactory;

class EditCaseTypeAssignmentsRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'assignment_type' => fake()->randomElement(ServiceRequestTypeAssignmentTypes::cases())->value,
        ];
    }

    public function withRandomTypeNotIncludingIndividual(): static
    {
        return $this->state([
            'assignment_type' => fake()->randomElement(array_filter(ServiceRequestTypeAssignmentTypes::cases(), fn (ServiceRequestTypeAssignmentTypes $type) => $type !== ServiceRequestTypeAssignmentTypes::Individual))->value,
        ]);
    }

    public function withIndividualType(): static
    {
        return $this->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual->value,
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
