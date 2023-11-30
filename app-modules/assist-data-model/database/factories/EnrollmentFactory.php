<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\AssistDataModel\Database\Factories;

use Assist\AssistDataModel\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            // TODO: Determine if we can have a different ID as the primary
            'sisid' => $this->faker->randomNumber(9),
            'acad_career' => $this->faker->randomElement(['NC', 'CRED']),
            'division' => $this->faker->randomElement(['ABC01', 'ABD02', 'ABE03']),
            'semester' => $this->faker->randomNumber(4),
            'class_nbr' => $this->faker->randomNumber(5),
            'subject' => $this->faker->randomElement(['ACC', 'FITNESS', 'MATH']),
            'catalog_nbr' => $this->faker->randomNumber(3) . '-' . $this->faker->randomNumber(5),
            'enrl_status' => $this->faker->randomElement(['DROP', 'ENRL']),
            'enrl_add_dt' => $this->faker->dateTime(),
            'enrl_drop_dt' => $this->faker->dateTime(),
            'crse_grade_off' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'W']),
            'unt_taken' => $this->faker->randomNumber(1),
            'unt_earned' => $this->faker->randomNumber(1),
            'last_upd_dt_stmp' => $this->faker->dateTime(),
        ];
    }
}
