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

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;

it('will create the appropriate records for educatables in the segment', function () {
    // Given we have no proactive alerts
    expect(Alert::count())->toBe(0);

    // But we have a segment, a campaign, and an action that defines we should create an alert at a certain point in time
    $prospects = Prospect::factory()->count(3)->create([
        'first_name' => 'TestTest',
    ]);

    $segment = Segment::factory()->create([
        'type' => SegmentType::Static,
    ]);

    $prospects->each(function (Prospect $prospect) use ($segment) {
        $segment->subjects()->create([
            'subject_id' => $prospect->getKey(),
            'subject_type' => $prospect->getMorphClass(),
        ]);
    });

    $campaign = Campaign::factory()->create([
        'segment_id' => $segment->id,
    ]);

    $alertStatus = AlertStatus::factory()->create([
        'classification' => 'active',
        'name' => 'Active',
    ]);

    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::ProactiveAlert,
            'data' => [
                'description' => 'This is the description',
                'severity' => 'low',
                'suggested_intervention' => 'This is the suggested intervention',
                'status' => $alertStatus->getKey(),
            ],
        ]);

    // When that action runs
    $action->execute();

    // We should have created the appropriate alerts for the appropriate educatable records
    expect(Alert::count())->toBe(3);
    expect(Alert::first()->description)->toBe('This is the description');
    expect(Alert::first()->severity)->toBe(AlertSeverity::Low);
    expect(Alert::first()->suggested_intervention)->toBe('This is the suggested intervention');
    expect(Alert::first()->status->getKey())->toEqual($alertStatus->getKey());
});
