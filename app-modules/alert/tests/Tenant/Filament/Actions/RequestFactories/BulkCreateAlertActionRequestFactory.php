<?php

namespace AdvisingApp\Alert\Tests\Tenant\Filament\Actions\RequestFactories;

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\Relation;
use Worksome\RequestFactories\RequestFactory;

class BulkCreateAlertActionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'concern_type' => $this->faker->randomElement([(new Student())->getMorphClass(), (new Prospect())->getMorphClass()]),
            'concern_id' => function (array $attributes) {
                $concernClass = Relation::getMorphedModel($attributes['concern_type']);

                /** @var Student|Prospect $concernModel */
                $concernModel = new $concernClass();

                $concern = $concernClass === Student::class
                  ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                  : $concernModel::factory()->create();

                return $concern->getKey();
            },
            'description' => $this->faker->sentence(),
            'severity' => $this->faker->randomElement(AlertSeverity::cases()),
            'status_id' => AlertStatus::factory(),
            'suggested_intervention' => $this->faker->sentence(),
        ];
    }
}
