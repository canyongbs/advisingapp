<?php

namespace AdvisingApp\Authorization\Tests\Tenant\Http\Controllers\RequestFactories;

use App\Models\Authenticatable;
use Worksome\RequestFactories\RequestFactory;

class GenerateLoginMagicLinkRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->safeEmail(),
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement([
                Authenticatable::SUPER_ADMIN_ROLE,
                Authenticatable::PARTNER_ADMIN_ROLE,
            ]),
        ];
    }
}
