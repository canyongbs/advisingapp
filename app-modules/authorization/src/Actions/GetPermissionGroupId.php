<?php

namespace AdvisingApp\Authorization\Actions;

use Exception;
use AdvisingApp\Authorization\Models\PermissionGroup;

class GetPermissionGroupId
{
    protected array $groups;

    public function __construct()
    {
        $this->groups = PermissionGroup::query()
            ->pluck('id', 'name')
            ->all();
    }

    public function __invoke(string $name): string
    {
        if (! str($name)->contains('.')) {
            throw new Exception("Invalid permission name: [{$name}] does not contain a period.");
        }

        $groupName = match ((string) str($name)->before('.')) {
            'sla' => 'SLA',
            'sms_template' => 'SMS Template',
            'in-app-communication' => 'In-App Communication',
            'integration-aws-ses-event-handling' => 'Integration: AWS SES Event Handling',
            'integration-google-analytics' => 'Integration: Google Analytics',
            'integration-google-recaptcha' => 'Integration: Google reCAPTCHA',
            'integration-microsoft-clarity' => 'Integration: Microsoft Clarity',
            'integration-twilio' => 'Integration: Twilio',
            default => (string) str($name)
                ->before('.')
                ->headline(),
        };

        if (blank($this->groups[$groupName] ?? null)) {
            $this->groups[$groupName] = PermissionGroup::create([
                'name' => $groupName,
            ])->id;
        }

        return $this->groups[$groupName];
    }
}
