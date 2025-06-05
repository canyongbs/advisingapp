<?php

use CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string, string> $campaignActionToJourneyStepPermissions
     */
    private array $campaignActionToJourneyStepPermissions = [
        'campaign_action.*.delete' => 'journey_step.*.delete',
        'campaign_action.*.force-delete' => 'journey_step.*.force-delete',
        'campaign_action.*.restore' => 'journey_step.*.restore',
        'campaign_action.*.update' => 'journey_step.*.update',
        'campaign_action.*.view' => 'journey_step.*.view',
        'campaign_action.create' => 'journey_step.create',
        'campaign_action.view-any' => 'journey_step.view-any',
    ];

    /**
     * @var array<string> $guards
     */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)->each(function (string $guard) {
            $this->renamePermissions($this->campaignActionToJourneyStepPermissions, $guard);
        });

        $this->renamePermissionGroups([
            'Campaign Action' => 'Journey Step',
        ]);
    }

    public function down(): void
    {
        collect($this->guards)->each(function (string $guard) {
            $this->renamePermissions(array_flip($this->campaignActionToJourneyStepPermissions), $guard);
        });

        $this->renamePermissionGroups([
            'Journey Step' => 'Campaign Action',
        ]);
    }
};
