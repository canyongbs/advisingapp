<?php

namespace AdvisingApp\Organization\Database\Factories;

use AdvisingApp\Organization\Models\Organization;
use Faker\Provider\en_US\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'email' => $this->faker->companyEmail(),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'website' => $this->faker->url(),
            'description' => $this->faker->text(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'state' => Address::stateAbbr(),
            'postalcode' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'domains' => array_map(fn (): array => ['domain' => $this->faker->unique()->domainName()], range(1, 3)),
        ];
    }
}
