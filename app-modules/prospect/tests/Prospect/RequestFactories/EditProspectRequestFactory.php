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

namespace Assist\Prospect\Tests\Prospect\RequestFactories;

use App\Models\User;
use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Models\ProspectStatus;
use Worksome\RequestFactories\RequestFactory;

class EditProspectRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'status_id' => ProspectStatus::inRandomOrder()->first()?->id ?? ProspectStatus::factory()->create()->id,
            'source_id' => ProspectSource::inRandomOrder()->first()?->id ?? ProspectSource::factory()->create()->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
            'preferred' => $this->faker->firstName(),
            'description' => $this->faker->paragraph(),
            'email' => $this->faker->email(),
            'email_2' => $this->faker->email(),
            'mobile' => $this->faker->phoneNumber(),
            'sms_opt_out' => $this->faker->boolean(),
            'email_bounce' => $this->faker->boolean(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'address_2' => $this->faker->address(),
            'birthdate' => $this->faker->date(),
            'hsgrad' => $this->faker->year(),
            'assigned_to_id' => User::factory()->create()->id,
            'created_by_id' => User::factory()->create()->id,
        ];
    }
}
