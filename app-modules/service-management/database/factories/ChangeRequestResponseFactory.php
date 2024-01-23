<?php

namespace AdvisingApp\ServiceManagement\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\ServiceManagement\Models\ChangeRequest;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\ServiceManagement\Models\ChangeRequestResponse>
 */
class ChangeRequestResponseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'change_request_id' => ChangeRequest::factory(),
            'user_id' => User::factory(),
            'approved' => $this->faker->boolean,
        ];
    }
}
