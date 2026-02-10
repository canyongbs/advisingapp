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
            'hsgrad' => $this->faker->optional()->randomElement([$this->faker->date('Y-m-d'), null]),
            'gender' => $this->faker->optional()->text(10),
            'dual' => $this->faker->optional()->randomElement([true, false, null]),
            'ferpa' => $this->faker->optional()->randomElement([true, false, null]),
            'firstgen' => $this->faker->optional()->randomElement([true, false, null]),
            'sap' => $this->faker->optional()->randomElement([true, false, null]),
            'holds' => $this->faker->optional()->text(10),
            'dfw' => $this->faker->optional()->randomElement([$this->faker->date('Y-m-d'), null]),
            'ethnicity' => $this->faker->optional()->text(10),
            'lastlmslogin' => $this->faker->optional()->randomElement([$this->faker->date('Y-m-d H:i:s'), null]),
        ], filled(...));
    }
}
