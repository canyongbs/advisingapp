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

namespace AdvisingApp\Prospect\Database\Factories;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectAddress;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prospect>
 */
class ProspectFactory extends Factory
{
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $address3 = $this->faker->optional()->words(asText: true);

        return [
            'status_id' => ProspectStatus::inRandomOrder()->first() ?? ProspectStatus::factory(),
            'source_id' => ProspectSource::inRandomOrder()->first() ?? ProspectSource::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
            'preferred' => $this->faker->firstName(),
            'description' => $this->faker->paragraph(),
            'birthdate' => $this->faker->date(),
            'hsgrad' => $this->faker->year(),
            'created_by_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Prospect $prospect) {
            $prospect->primaryEmailAddress()->associate(ProspectEmailAddress::factory()->create([
                'prospect_id' => $prospect->getKey(),
                'address' => $this->faker->unique()->email(),
                'order' => 1,
            ]));
            $prospect->primaryPhoneNumber()->associate(ProspectPhoneNumber::factory()->canReceiveSms()->create([
                'prospect_id' => $prospect->getKey(),
                'number' => $this->faker->e164PhoneNumber(),
                'order' => 1,
            ]));
            $prospect->primaryAddress()->associate(ProspectAddress::factory()->create([
                'prospect_id' => $prospect->getKey(),
                'line_1' => $this->faker->streetAddress(),
                'line_2' => $this->faker->secondaryAddress(),
                'line_3' => $this->faker->optional()->words(asText: true) ?? null,
                'city' => $this->faker->city(),
                'state' => $this->faker->stateAbbr(),
                'postal' => str($this->faker->postcode())->before('-')->toString(),
                'order' => 1,
            ]));

            $prospect->save();
        });
    }
}
