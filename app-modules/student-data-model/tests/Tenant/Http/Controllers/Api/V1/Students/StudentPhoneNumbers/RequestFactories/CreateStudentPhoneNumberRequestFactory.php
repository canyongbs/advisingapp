<?php

namespace AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentPhoneNumbers\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class CreateStudentPhoneNumberRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return array_filter([
            'number' => $this->faker->phoneNumber(),
            'ext' => $this->faker->optional()->randomNumber(3, true),
            'type' => $this->faker->optional()->randomElement(['Mobile', 'Home', 'Work']),
            'can_receive_sms' => $this->faker->boolean(),
        ], filled(...));
    }
}
