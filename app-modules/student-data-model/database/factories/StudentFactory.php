<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Database\Factories;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentAddress;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Carbon\Carbon;
use Faker\Provider\en_US\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::yesterday();
        $sourceDate = $this->faker->dateTimeBetween($startDate, $endDate);

        $attributes = [
            'sisid' => $this->faker->unique()->numerify('########'),
            'otherid' => $this->faker->numerify('##########'),
            'first' => $this->faker->firstName(),
            'last' => $this->faker->lastName(),
            'full_name' => fn (array $attributes) => "{$attributes['first']} {$attributes['last']}",
            'preferred' => $this->faker->randomElement([$this->faker->firstName(), null]),
            'birthdate' => $this->faker->date(),
            'hsgrad' => $this->faker->date(),
            'dual' => $this->faker->boolean(),
            'ferpa' => $this->faker->boolean(),
            'dfw' => $this->faker->date(),
            'sap' => $this->faker->boolean(),
            'holds' => $this->faker->regexify('[A-Z]{5}'),
            'standing' => $this->faker->randomElement(['Good', 'Probation', 'Suspended']),
            'firstgen' => $this->faker->boolean(),
            'ethnicity' => $this->faker->randomElement(['White', 'Black', 'Hispanic', 'Asian', 'Other']),
            'lastlmslogin' => $this->faker->dateTime(),
            'athletics_status' => $this->faker->randomElement(['Active', 'Inactive']),
            'athletic_details' => $this->faker->sentence(),
        ];

        $attributes['created_at'] = now();
        $attributes['updated_at'] = now();
        $attributes['created_at_source'] = $sourceDate;
        $attributes['updated_at_source'] = $sourceDate;

        return $attributes;
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Student $student) {
            $student->primaryEmailAddress()->associate(StudentEmailAddress::factory()->create([
                'sisid' => $student->getKey(),
                'address' => $this->faker->email(),
                'order' => 1,
            ]));
            $student->primaryPhoneNumber()->associate(StudentPhoneNumber::factory()->canReceiveSms()->create([
                'sisid' => $student->getKey(),
                'number' => $this->faker->e164PhoneNumber(),
                'order' => 1,
            ]));
            $student->primaryAddress()->associate(StudentAddress::factory()->create([
                'sisid' => $student->getKey(),
                'line_1' => $this->faker->buildingNumber() . ' ' . $this->faker->streetName(),
                'line_2' => $this->faker->randomElement([null, Address::secondaryAddress()]),
                'line_3' => null,
                'city' => $this->faker->city(),
                'state' => Address::stateAbbr(),
                'postal' => $this->faker->postcode(),
                'order' => 1,
            ]));

            $student->save();
        });
    }
}
