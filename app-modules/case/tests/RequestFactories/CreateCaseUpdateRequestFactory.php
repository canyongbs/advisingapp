<?php

namespace Assist\Case\Tests\RequestFactories;

use Assist\Case\Models\ServiceRequest;
use Worksome\RequestFactories\RequestFactory;
use Assist\Case\Enums\ServiceRequestUpdateDirection;

class CreateCaseUpdateRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'case_id' => ServiceRequest::factory()->create()->id,
            'update' => $this->faker->sentence(),
            'direction' => $this->faker->randomElement(ServiceRequestUpdateDirection::cases())->value,
            'internal' => $this->faker->boolean(),
        ];
    }
}
