<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Form\Models\Form;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowTrigger>
 */
class WorkflowTriggerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => WorkflowTriggerType::EventBased,
            'related_type' => (new Form())->getMorphClass(),
            'related_id' => Form::factory(),
            'created_by_type' => (new User())->getMorphClass(),
            'created_by_id' => User::factory(),
        ];
    }
}
