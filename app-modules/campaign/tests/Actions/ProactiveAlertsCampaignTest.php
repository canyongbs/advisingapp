<?php

use Assist\Alert\Models\Alert;
use Assist\Campaign\Models\Campaign;
use Assist\Prospect\Models\Prospect;
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
});
