<?php

use App\Models\User;
use AdvisingApp\Prospect\Models\Prospect;

use function Pest\Laravel\assertDatabaseHas;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\Student;

it('can auto subscribe user when adding in care team', function ($educatable) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $educatable->careTeam()->sync([$user->getKey()]);

    assertDatabaseHas('care_teams', [
        'user_id' => $user->getKey(),
        'educatable_id' => $educatable->getKey(),
        'educatable_type' => $educatable->getMorphClass(),
    ]);

    assertDatabaseHas('subscriptions', [
        'user_id' => $user->getKey(),
        'subscribable_id' => $educatable->getKey(),
        'subscribable_type' => $educatable->getMorphClass(),
    ]);
})
    ->with([
        'for Prospect' => [
            'educatable' => fn () => Prospect::factory()->create(),
        ],
        'for Student' => [
            'educatable' => fn () => Student::factory()->create(),
        ],
    ]);
