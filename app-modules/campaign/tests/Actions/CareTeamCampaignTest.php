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

use App\Models\User;
use Laravel\Pennant\Feature;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentType;
use Illuminate\Database\Eloquent\Collection;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\CaseloadManagement\Models\Caseload;
use AdvisingApp\CaseloadManagement\Enums\CaseloadType;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

it('will create the appropriate records for educatables in the caseload', function (array $priorCareTeam, Collection $educatables, bool $removePrior) {
    Feature::active('enable-segments')
        ? $segmentOrCaseload = Segment::factory()->create([
            'type' => SegmentType::Static,
        ])
        : $segmentOrCaseload = Caseload::factory()->create([
            'type' => CaseloadType::Static,
        ]);

    $educatables->each(function (Educatable $educatable) use ($segmentOrCaseload, $priorCareTeam) {
        $segmentOrCaseload->subjects()->create([
            'subject_id' => $educatable->getKey(),
            'subject_type' => $educatable->getMorphClass(),
        ]);

        $educatable->careTeam()->sync($priorCareTeam);
    });

    Feature::active('enable-segments')
        ? $foreignKey = 'segment_id'
        : $foreignKey = 'caseload_id';

    $campaign = Campaign::factory()->create([
        $foreignKey => $segmentOrCaseload->id,
    ]);

    $users = User::factory()->licensed(LicenseType::cases())->count(3)->create();

    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::CareTeam,
            'data' => [
                'user_ids' => $users->pluck('id')->toArray(),
                'remove_prior' => $removePrior,
            ],
        ]);

    // When that action runs
    $action->execute();

    $educatables->each(
        fn (Educatable $educatable) => expect(
            $educatable->careTeam->pluck('id')->toArray()
        )
            ->toBe(
                $removePrior
                    ? $users->pluck('id')->toArray()
                    : [...$priorCareTeam, ...$users->pluck('id')->toArray()]
            )
    );
})->with(
    [
        'no prior care team | prospects | remove prior false' => [
            'priorCareTeam' => [],
            'educatables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'no prior care team | prospects | remove prior true' => [
            'priorCareTeam' => [],
            'educatables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => true,
        ],
        'prior care team | prospects | remove prior false' => [
            'priorCareTeam' => fn () => User::factory()->licensed(LicenseType::cases())->count(3)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'prior care team | prospects | remove prior true' => [
            'priorCareTeam' => fn () => User::factory()->licensed(LicenseType::cases())->count(3)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => true,
        ],
        'no prior care team | students | remove prior false' => [
            'priorCareTeam' => [],
            'educatables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'no prior care team | students | remove prior true' => [
            'priorCareTeam' => [],
            'educatables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => true,
        ],
        'prior care team | students | remove prior false' => [
            'priorCareTeam' => fn () => User::factory()->licensed(LicenseType::cases())->count(3)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'prior care team | students | remove prior true' => [
            'priorCareTeam' => fn () => User::factory()->licensed(LicenseType::cases())->count(3)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => true,
        ],
    ]
);
