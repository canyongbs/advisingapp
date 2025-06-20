<?php

namespace AdvisingApp\Ai\Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class QnAAdvisorCategoryRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
