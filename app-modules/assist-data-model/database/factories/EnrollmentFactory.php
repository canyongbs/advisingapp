<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

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
