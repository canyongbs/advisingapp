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

namespace Assist\Alert\Database\Factories;

use Assist\Alert\Models\Alert;
use Assist\Alert\Enums\AlertStatus;
use Assist\Prospect\Models\Prospect;
use Assist\Alert\Enums\AlertSeverity;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Factory<Alert>
 */
class AlertFactory extends Factory
{
    public function definition(): array
    {
        return [
            'concern_type' => fake()->randomElement([(new Student())->getMorphClass(), (new Prospect())->getMorphClass()]),
            'concern_id' => function (array $attributes) {
                $concernClass = Relation::getMorphedModel($attributes['concern_type']);

                /** @var Student|Prospect $concernModel */
                $concernModel = new $concernClass();

                $concern = $concernClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $concernModel::factory()->create();

                return $concern->getKey();
            },
            'description' => fake()->sentence(),
            'severity' => fake()->randomElement(AlertSeverity::cases()),
            'status' => fake()->randomElement(AlertStatus::cases()),
            'suggested_intervention' => fake()->sentence(),
        ];
    }
}
