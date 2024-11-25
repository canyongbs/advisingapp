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

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'license_settings.manage' => 'License Settings',
        'audit.view_audit_settings' => 'Audit',
        'portal.view_portal_settings' => 'Portal',
        'multifactor_settings.manage' => 'Multifactor',
        'theme.view_theme_settings' => 'Theme',
        'integration-google-analytics.view_google_analytics_settings' => 'Integration: Google Analytics',
        'integration-google-recaptcha.view_google_recaptcha_settings' => 'Integration: Google reCAPTCHA',
        'integration-microsoft-clarity.view_microsoft_clarity_settings' => 'Integration: Microsoft Clarity',
        'integration-twilio.view_twilio_settings' => 'Integration: Twilio',
        'integration-aws-ses-event-handling.view_ses_settings' => 'Integration: AWS SES Event Handling',
        'authorization.view_azure_sso_settings' => 'Authorization',
        'authorization.view_google_sso_settings' => 'Authorization',
        'meeting-center.view_azure_calendar_settings' => 'Meeting Center',
        'meeting-center.view_google_calendar_settings' => 'Meeting Center',
        'ai.view_cognitive_services_settings' => 'Integration: Cognitive Services',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->deletePermissions(array_keys($this->permissions), $guard);
            });
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $this->createPermissions($this->permissions, $guard);
            });
    }
};
