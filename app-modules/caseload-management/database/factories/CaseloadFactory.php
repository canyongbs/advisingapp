<?php

namespace Assist\CaseloadManagement\Database\Factories;

use App\Models\User;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Caseload>
 */
class CaseloadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(asText: true),
            'model' => fake()->randomElement(CaseloadModel::cases()),
            'type' => CaseloadType::Dynamic, //TODO: add static later
        ];
    }

    public function configure(): CaseloadFactory|Factory
    {
        return $this->afterMaking(function (Caseload $caseload) {
            $caseload->user()->associate(User::inRandomOrder()->first() ?? User::factory()->create());
        });
    }
}
