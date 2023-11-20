<?php

namespace Assist\Prospect\Database\Factories;

use App\Models\User;
use Assist\Prospect\Models\Prospect;
use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Models\ProspectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prospect>
 */
class ProspectFactory extends Factory
{
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'status_id' => ProspectStatus::inRandomOrder()->first() ?? ProspectStatus::factory(),
            'source_id' => ProspectSource::inRandomOrder()->first() ?? ProspectSource::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
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
            'assigned_to_id' => User::factory(),
            'created_by_id' => User::factory(),
        ];
    }
}
