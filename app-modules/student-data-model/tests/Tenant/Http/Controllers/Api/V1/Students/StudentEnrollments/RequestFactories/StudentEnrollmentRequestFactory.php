<?php

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