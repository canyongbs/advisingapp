<?php

namespace AdvisingApp\ServiceManagement\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\ServiceManagement\Models\ChangeRequestType;
use AdvisingApp\ServiceManagement\Models\ChangeRequestStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\ServiceManagement\Models\ChangeRequest>
 */
class ChangeRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'change_request_type_id' => ChangeRequestType::factory(),
            'change_request_status_id' => ChangeRequestStatus::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->text(),
            'reason' => fake()->paragraphs(1),
            'backout_strategy' => fake()->paragraphs(1),
            'start_time' => fake()->dateTime(),
            'end_time' => fake()->dateTime(),
        ];
    }
}
