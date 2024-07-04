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

use Laravel\Pennant\Feature;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\Alert\Enums\AlertStatus;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\CaseloadManagement\Models\Caseload;
use AdvisingApp\CaseloadManagement\Enums\CaseloadType;

it('will create the appropriate records for educatables in the caseload', function () {
    // Given we have no proactive alerts
    expect(Alert::count())->toBe(0);

    // But we have a caseload, a campaign, and an action that defines we should create an alert at a certain point in time
    $prospects = Prospect::factory()->count(3)->create([
        'first_name' => 'TestTest',
    ]);

    Feature::active('enable-segments')
        ? $segmentOrCaseload = Segment::factory()->create([
            'type' => SegmentType::Static,
        ])
        : $segmentOrCaseload = Caseload::factory()->create([
            'type' => CaseloadType::Static,
        ]);

    $prospects->each(function (Prospect $prospect) use ($segmentOrCaseload) {
        $segmentOrCaseload->subjects()->create([
            'subject_id' => $prospect->getKey(),
            'subject_type' => $prospect->getMorphClass(),
        ]);
    });

    Feature::active('enable-segments')
        ? $foreignKey = 'segment_id'
        : $foreignKey = 'caseload_id';

    $campaign = Campaign::factory()->create([
        $foreignKey => $segmentOrCaseload->id,
    ]);

    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::ProactiveAlert,
            'data' => [
                'description' => 'This is the description',
                'severity' => 'low',
                'suggested_intervention' => 'This is the suggested intervention',
                'status' => 'active',
            ],
        ]);

    // When that action runs
    $action->execute();

    // We should have created the appropriate alerts for the appropriate educatable records
    expect(Alert::count())->toBe(3);
    expect(Alert::first()->description)->toBe('This is the description');
    expect(Alert::first()->severity)->toBe(AlertSeverity::Low);
    expect(Alert::first()->suggested_intervention)->toBe('This is the suggested intervention');
    expect(Alert::first()->status)->toBe(AlertStatus::Active);
});
