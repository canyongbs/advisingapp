<?php

namespace AdvisingApp\ResourceHub\Tests\Tenant\Filament\Actions\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class CreateConcernActionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->words(3, true),
        ];
    }
}
