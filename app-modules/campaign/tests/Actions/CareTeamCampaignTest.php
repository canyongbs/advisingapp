<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;
use Assist\Campaign\Models\Campaign;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\Collection;
use Assist\Campaign\Enums\CampaignActionType;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\AssistDataModel\Models\Contracts\Educatable;

it('will create the appropriate records for educatables in the caseload', function (array $priorCareTeam, Collection $educatables, bool $removePrior) {
    $caseload = Caseload::factory()->create([
        'type' => CaseloadType::Static,
    ]);

    $educatables->each(function (Educatable $educatable) use ($caseload, $priorCareTeam) {
        $caseload->subjects()->create([
            'subject_id' => $educatable->getKey(),
            'subject_type' => $educatable->getMorphClass(),
        ]);

        $educatable->careTeam()->sync($priorCareTeam);
    });

    $campaign = Campaign::factory()->create([
        'caseload_id' => $caseload->id,
    ]);

    $users = User::factory()->count(3)->create();

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
            'priorCareTeam' => fn () => User::factory()->count(3)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'prior care team | prospects | remove prior true' => [
            'priorCareTeam' => fn () => User::factory()->count(3)->create()->pluck('id')->toArray(),
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
            'priorCareTeam' => fn () => User::factory()->count(3)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'prior care team | students | remove prior true' => [
            'priorCareTeam' => fn () => User::factory()->count(3)->create()->pluck('id')->toArray(),
            'educatables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => true,
        ],
    ]
);
