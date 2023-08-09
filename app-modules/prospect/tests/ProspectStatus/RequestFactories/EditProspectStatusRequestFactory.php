<?php

namespace Assist\Prospect\Tests\ProspectStatus\RequestFactories;

use Assist\Case\Enums\ColumnColorOptions;
use Worksome\RequestFactories\RequestFactory;
use Assist\Prospect\Enums\ProspectStatusColorOptions;

class EditProspectStatusRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'color' => $this->faker->randomElement(ProspectStatusColorOptions::cases())->value,
        ];
    }
}
