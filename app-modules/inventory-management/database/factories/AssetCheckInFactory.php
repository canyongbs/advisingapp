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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\InventoryManagement\Database\Factories;

use App\Models\User;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\InventoryManagement\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\InventoryManagement\Models\AssetCheckIn>
 */
class AssetCheckInFactory extends Factory
{
    public function definition(): array
    {
        $checkedInBy = User::factory()->create();

        return [
            'asset_id' => Asset::factory(),
            'checked_in_by_type' => $checkedInBy->getMorphClass(),
            'checked_in_by_id' => $checkedInBy->getKey(),
            'checked_in_from_type' => fake()->randomElement([
                (new Student())->getMorphClass(),
                (new Prospect())->getMorphClass(),
            ]),
            'checked_in_from_id' => function (array $attributes) {
                $checkedInFromClass = Relation::getMorphedModel($attributes['checked_in_from_type']);

                /** @var Student|Prospect $senderModel */
                $checkedInFromModel = new $checkedInFromClass();

                $checkedInFromModel = $checkedInFromClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $checkedInFromModel::factory()->create();

                return $checkedInFromModel->getKey();
            },
            'checked_in_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->paragraph(),
        ];
    }
}
