<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sisid' => Student::factory(),
            'otherid' => function (array $attributes) {
                return Student::find($attributes['sisid'])->otherid;
            },
            'acad_career' => $this->faker->randomElement(['NC', 'CRED']),
            'division' => $this->faker->randomElement(['ABC01', 'ABD02', 'ABE03']),
            'acad_plan' => json_encode([
                'major' => $this->faker->words(3),
                'minor' => $this->faker->words(3),
            ]),
            'prog_status' => 'AC',
            'cum_gpa' => $this->faker->randomFloat(3, 0, 4),
            'semester' => $this->faker->numerify('####'),
            'descr' => $this->faker->words(2, true),
            'foi' => $this->faker->randomElement(['', 'FOI ' . $this->faker->words(2, true)]),
            'change_dt' => $this->faker->dateTime(),
            'declare_dt' => $this->faker->dateTime(),
            'graduation_dt' => $this->faker->dateTime(),
            'conferred_dt' => $this->faker->dateTime(),
        ];
    }
}
