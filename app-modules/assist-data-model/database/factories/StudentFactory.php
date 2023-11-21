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

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sisid' => $this->faker->randomNumber(9),
            'otherid' => $this->faker->randomNumber(9),
            'first' => $this->faker->firstName(),
            'last' => $this->faker->lastName(),
            'full_name' => $this->faker->name(),
            'preferred' => $this->faker->firstName(),
            'email' => $this->faker->email(),
            'email_2' => $this->faker->email(),
            'mobile' => $this->faker->phoneNumber(),
            'sms_opt_out' => $this->faker->boolean(),
            'email_bounce' => $this->faker->boolean(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'address2' => $this->faker->address(),
            'address3' => $this->faker->address(),
            'city' => $this->faker->city(),
            'state' => $this->faker->locale(),
            'postal' => $this->faker->postcode(),
            'birthdate' => $this->faker->date(),
            'hsgrad' => $this->faker->year(),
            'dual' => $this->faker->boolean(),
            'ferpa' => $this->faker->boolean(),
            'dfw' => $this->faker->date(),
            'sap' => $this->faker->boolean(),
            'holds' => $this->faker->word(),
            'firstgen' => $this->faker->boolean(),
            'ethnicity' => $this->faker->randomElement(['White', 'Black', 'Hispanic', 'Asian', 'Other']),
            'lastlmslogin' => $this->faker->dateTime(),
            'f_e_term' => $this->faker->randomNumber(4),
            'mr_e_term' => $this->faker->randomNumber(4),
        ];
    }

    protected function store(Collection $results)
    {
        if (config('database.adm_materialized_views_enabled')) {
            // Because Students points to a Materialized View, we need to set the table to the actual table name before storing and then refresh the view after storing.

            $results = $results->map(fn (Model $result) => $result->setTable('students'));

            parent::store($results);

            $results->each(function (Model $result) {
                $result->setTable('students_local');
            });

            DB::connection('pgsql')->statement('REFRESH MATERIALIZED VIEW students_local');
        } else {
            parent::store($results);
        }
    }
}
