<?php

namespace AdvisingApp\InventoryManagement\Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\InventoryManagement\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\InventoryManagement\Models\AssetCheckOut>
 */
class AssetCheckOutFactory extends Factory
{
    public function definition(): array
    {
        $checkedOutBy = User::factory()->create();

        return [
            'asset_id' => Asset::factory(),
            'asset_check_in_id' => null,
            'checked_out_by_type' => $checkedOutBy->getMorphClass(),
            'checked_out_by_id' => $checkedOutBy->getKey(),
            'checked_out_to_type' => fake()->randomElement([
                (new Student())->getMorphClass(),
                (new Prospect())->getMorphClass(),
            ]),
            'checked_out_to_id' => function (array $attributes) {
                $checkedOutToClass = Relation::getMorphedModel($attributes['checked_out_to_type']);

                /** @var Student|Prospect $senderModel */
                $checkedOutToModel = new $checkedOutToClass();

                $checkedOutToModel = $checkedOutToClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $checkedOutToModel::factory()->create();

                return $checkedOutToModel->getKey();
            },
            'checked_out_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'expected_check_in_at' => function (array $attributes) {
                $checkedOutAt = Carbon::parse($attributes['checked_out_at']);

                return fake()->dateTimeBetween($checkedOutAt->addDays(1), $checkedOutAt->addDays(50));
            },
            'notes' => fake()->paragraph(),
        ];
    }
}
