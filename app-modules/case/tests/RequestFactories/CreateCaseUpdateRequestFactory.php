<?php

namespace Assist\Case\Tests\RequestFactories;

use Assist\Case\Models\ServiceRequest;
use Assist\Case\Enums\CaseUpdateDirection;
use Worksome\RequestFactories\RequestFactory;

class CreateCaseUpdateRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'case_id' => ServiceRequest::factory()->create()->id,
            'update' => $this->faker->sentence(),
            'direction' => $this->faker->randomElement(CaseUpdateDirection::cases())->value,
            'internal' => $this->faker->boolean(),
        ];
    }
}
