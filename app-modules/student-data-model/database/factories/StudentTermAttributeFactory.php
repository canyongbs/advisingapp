<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Database\Factories;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentTermAttribute>
 */
class StudentTermAttributeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sisid' => Student::factory(),
            'sis_term_id' => fn (): string => Term::factory()->create()->sis_term_id,
            'enrollment_status' => $this->faker->randomElement(['CONTINUING', '1ST TIME FRESHMAN', 'RETURNING']),
            'academic_status' => $this->faker->optional(0.5)->randomElement(['GOOD STANDING', 'ACADEMIC PROBATION']),
            'campus' => $this->faker->randomElement(['Main', 'Online']),
            'college_level' => $this->faker->optional(0.5)->randomElement(['Freshman', 'Sophomore', 'Junior', 'Senior']),
            'commuter' => $this->faker->randomElement(['Commuter', 'Resident']),
            'student_registered' => $this->faker->randomElement(['Yes', 'No']),
        ];
    }
}
