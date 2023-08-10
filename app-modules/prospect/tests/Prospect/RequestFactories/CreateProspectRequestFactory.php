<?php

namespace Assist\Prospect\Tests\Prospect\RequestFactories;

use App\Models\User;
use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Models\ProspectStatus;
use Worksome\RequestFactories\RequestFactory;

class CreateProspectRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'status_id' => ProspectStatus::inRandomOrder()->first() ?? ProspectStatus::factory()->create()->id,
            'source_id' => ProspectSource::inRandomOrder()->first() ?? ProspectSource::factory()->create()->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full' => "{$firstName} {$lastName}",
            'preferred' => $this->faker->firstName(),
            'description' => $this->faker->paragraph(),
            'email' => $this->faker->email(),
            'email_2' => $this->faker->email(),
            'mobile' => $this->faker->phoneNumber(),
            'sms_opt_out' => $this->faker->boolean(),
            'email_bounce' => $this->faker->boolean(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'address_2' => $this->faker->address(),
            'birthdate' => $this->faker->date(),
            'hsgrad' => $this->faker->year(),
            'assigned_to_id' => User::factory()->create()->id,
            'created_by_id' => User::factory()->create()->id,
        ];
    }
}
