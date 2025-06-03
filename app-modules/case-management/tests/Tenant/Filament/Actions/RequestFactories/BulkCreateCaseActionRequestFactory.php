<?php

namespace AdvisingApp\CaseManagement\Tests\Tenant\Filament\Actions\RequestFactories;

use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Exception;
use Worksome\RequestFactories\RequestFactory;

class BulkCreateCaseActionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $priority = CasePriority::factory()->create();
        $respondent = $this->faker->randomElement([
            Student::class,
            Prospect::class,
        ]);

        throw_unless(in_array($respondent, [
            Student::class,
            Prospect::class,
        ], true), new Exception('Invalid respondent type'));

        $respondent = match ($respondent) {
            Student::class => Student::inRandomOrder()->first() ?? Student::factory()->create(),
            Prospect::class => Prospect::factory()->create(),
        };

        return [
            'division_id' => Division::inRandomOrder()->first()->id ?? Division::factory()->create()->id,
            'status_id' => CaseStatus::factory()->create()->id,
            'priority_id' => $priority->id,
            'respondent_id' => $respondent->getKey(),
            'respondent_type' => $respondent->getMorphClass(),
            'close_details' => $this->faker->sentence,
            'res_details' => $this->faker->sentence,
        ];
    }
}
