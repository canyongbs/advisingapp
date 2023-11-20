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

use Assist\Alert\Models\Alert;
use Assist\Alert\Enums\AlertStatus;
use Assist\Campaign\Models\Campaign;
use Assist\Prospect\Models\Prospect;
use Assist\Alert\Enums\AlertSeverity;
use Assist\Campaign\Models\CampaignAction;
use Assist\Campaign\Enums\CampaignActionType;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadType;

it('will create the appropriate records for educatables in the caseload', function () {
    // Given we have no proactive alerts
    expect(Alert::count())->toBe(0);

    // But we have a caseload, a campaign, and an action that defines we should create an alert at a certain point in time
    $prospects = Prospect::factory()->count(3)->create([
        'first_name' => 'TestTest',
    ]);

    $caseload = Caseload::factory()->create([
        'type' => CaseloadType::Static,
    ]);

    $prospects->each(function (Prospect $prospect) use ($caseload) {
        $caseload->subjects()->create([
            'subject_id' => $prospect->getKey(),
            'subject_type' => $prospect->getMorphClass(),
        ]);
    });

    $campaign = Campaign::factory()->create([
        'caseload_id' => $caseload->id,
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
