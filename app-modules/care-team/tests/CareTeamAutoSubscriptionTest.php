<?php

namespace AdvisingApp\CareTeam\Tests;

use App\Models\User;

use function Tests\asSuperAdmin;

use AdvisingApp\Prospect\Models\Prospect;

use function Pest\Laravel\assertDatabaseHas;

use Illuminate\Database\Eloquent\Collection;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

it('can auto subscribe user when adding in care team', function (array $careTeam, Collection $educatables) {
    $superAdmin = User::factory()->licensed([Prospect::getLicenseType(), Student::getLicenseType()])->create();

    asSuperAdmin($superAdmin);

    $educatables->each(function (Educatable $educatable) use ($careTeam) {
        $educatable->careTeam()->sync($careTeam);

        $educatable->careTeam->each(function ($user) use ($educatable) {
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
        });
    });
})
    ->with([
        'for Prospect' => [
            'careTeam' => fn () => User::factory()->licensed(LicenseType::cases())->count(1)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Prospect::factory()->count(1)->create(),
        ],
        'for Student' => [
            'careTeam' => fn () => User::factory()->licensed(LicenseType::cases())->count(1)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Student::factory()->count(1)->create(),
        ],
    ]);
