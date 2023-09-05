<?php

namespace Assist\ServiceManagement\Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;

class CreateServiceRequestUpdateRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'service_request_id' => ServiceRequest::factory()->create()->id,
            'update' => $this->faker->sentence(),
            'direction' => $this->faker->randomElement(ServiceRequestUpdateDirection::cases())->value,
            'internal' => $this->faker->boolean(),
        ];
    }
}
