<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentEnrollments\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class StudentEnrollmentRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return array_filter([
            'division' => $this->faker->randomElement(['ABC01', 'ABD02', 'ABE03']),
            'class_nbr' => $this->faker->numerify('19###'),
            'crse_grade_off' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'W']),
            'unt_taken' => $this->faker->numberBetween(1, 4),
            'unt_earned' => function (array $attributes) {
                return $attributes['unt_taken'] - $this->faker->numberBetween(0, $attributes['unt_taken']);
            },
            'last_upd_dt_stmp' => $this->faker->date('Y-m-d H:i:s'),
            'section' => $this->faker->numerify('####'),
            'name' => $this->faker->randomElement(['Introduction to Mathematics', 'College Algebra', 'Business Communication: Writing for the Workplace']),
            'department' => $this->faker->optional(0.8)->randomElement(['Business', 'Business Administration', 'BA: Business Administration']),
            'faculty_name' => $this->faker->name(),
            'faculty_email' => $this->faker->safeEmail(),
            'semester_code' => $this->faker->optional(0.8)->numerify('42##'),
            'semester_name' => $this->faker->optional(0.8)->randomElement(['Fall 2006', 'Spring Cohort A 2006', 'Summer A 2006', 'Summer 2012']),
            'start_date' => $this->faker->optional(0.8)->date('Y-m-d H:i:s'),
            'end_date' => $this->faker->optional(0.8)->date('Y-m-d H:i:s'),
        ], filled(...));
    }
}
