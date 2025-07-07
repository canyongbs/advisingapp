<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    ...existing copyright...

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class UpdateStudentRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return array_filter([
            'otherid' => $this->faker->optional()->numerify('##########'),
            'first' => $this->faker->optional()->firstName(),
            'last' => $this->faker->optional()->lastName(),
            'full_name' => $this->faker->optional()->name(),
            'preferred' => $this->faker->optional()->firstName(),
            'birthdate' => $this->faker->optional()->randomElement([$this->faker->date('Y-m-d'), null]),
            'hsgrad' => $this->faker->optional()->randomElement([$this->faker->numberBetween(1980, 2030), null]),
            'gender' => $this->faker->optional()->text(10),
            'sms_opt_out' => $this->faker->optional()->randomElement([true, false, null]),
            'email_bounce' => $this->faker->optional()->randomElement([true, false, null]),
            'dual' => $this->faker->optional()->randomElement([true, false, null]),
            'ferpa' => $this->faker->optional()->randomElement([true, false, null]),
            'firstgen' => $this->faker->optional()->randomElement([true, false, null]),
            'sap' => $this->faker->optional()->randomElement([true, false, null]),
            'holds' => $this->faker->optional()->text(10),
            'dfw' => $this->faker->optional()->randomElement([$this->faker->date('Y-m-d'), null]),
            'ethnicity' => $this->faker->optional()->text(10),
            'lastlmslogin' => $this->faker->optional()->randomElement([$this->faker->date('Y-m-d H:i:s'), null]),
            'f_e_term' => $this->faker->optional()->text(10),
            'mr_e_term' => $this->faker->optional()->text(10),
        ], filled(...));
    }
}
