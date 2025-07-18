<?php

namespace AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentPrograms\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class StudentProgramRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return array_filter([
            'acad_career' => $this->faker->randomElement(['NC', 'CRED']),
            'division' => $this->faker->randomElement(['ABC01', 'ABD02', 'ABE03']),
            'acad_plan' => [
                'major' => $this->faker->words(3),
                'minor' => $this->faker->words(3),
            ],
            'prog_status' => 'AC',
            'cum_gpa' => $this->faker->randomFloat(2, 1, 4),
            'semester' => $this->faker->numerify('####'),
            'descr' => $this->faker->words(2, true),
            'foi' => $this->faker->randomElement(['', 'FOI ' . $this->faker->words(2, true)]),
            'change_dt' => $this->faker->date('Y-m-d H:i:s'),
            'declare_dt' => $this->faker->date('Y-m-d H:i:s'),
        ], filled(...));
    }
}
