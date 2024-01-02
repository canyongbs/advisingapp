<?php

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
        $checkedOutBy = User::factory()->create();

        return [
            'asset_id' => Asset::factory(),
            'checked_in_by_type' => $checkedOutBy->getMorphClass(),
            'checked_in_by_id' => $checkedOutBy->getKey(),
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
