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

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\InventoryManagement\Database\Factories;

use Database\Factories\Concerns\RandomizeState;
use AdvisingApp\InventoryManagement\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\InventoryManagement\Models\MaintenanceProvider;
use AdvisingApp\InventoryManagement\Enums\MaintenanceActivityStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\InventoryManagement\Models\MaintenanceActivity>
 */
class MaintenanceActivityFactory extends Factory
{
    use RandomizeState;

    public function definition(): array
    {
        $date = fake()->date();

        return [
            'asset_id' => Asset::factory(),
            'completed_date' => $date,
            'details' => fake()->sentence(),
            'maintenance_provider_id' => MaintenanceProvider::factory(),
            'notes' => fake()->paragraph(),
            'scheduled_date' => $date,
        ];
    }

    public function inProgress(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::InProgress,
                'completed_date' => null,
                'scheduled_date' => now(),
            ];
        });
    }

    public function completed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Completed,
                'completed_date' => now(),
                'scheduled_date' => now(),
            ];
        });
    }

    public function cancelled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Cancelled,
                'completed_date' => null,
                'scheduled_date' => now()->subDays(7),
            ];
        });
    }

    public function delayed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Delayed,
                'completed_date' => null,
                'scheduled_date' => now()->addDays(7),
            ];
        });
    }
}
