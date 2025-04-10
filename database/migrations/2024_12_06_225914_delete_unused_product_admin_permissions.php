<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
     * @var array<string> $permissions
     */
    private array $permissions = [
        'ai.access_integrated_assistant_settings' => 'Integrated Assistant',
        'application_submission_state.*.delete' => 'Application Submission State',
        'application_submission_state.*.force-delete' => 'Application Submission State',
        'application_submission_state.*.restore' => 'Application Submission State',
        'application_submission_state.*.update' => 'Application Submission State',
        'application_submission_state.*.view' => 'Application Submission State',
        'application_submission_state.create' => 'Application Submission State',
        'application_submission_state.view-any' => 'Application Submission State',
        'alert_status.*.delete' => 'Alert Status',
        'alert_status.*.force-delete' => 'Alert Status',
        'alert_status.*.restore' => 'Alert Status',
        'alert_status.*.update' => 'Alert Status',
        'alert_status.*.view' => 'Alert Status',
        'alert_status.create' => 'Alert Status',
        'alert_status.view-any' => 'Alert Status',
        'assistant.access_ai_settings' => 'Assistant',
        'basic_needs_category.*.delete' => 'Basic Needs Category',
        'basic_needs_category.*.force-delete' => 'Basic Needs Category',
        'basic_needs_category.*.restore' => 'Basic Needs Category',
        'basic_needs_category.*.update' => 'Basic Needs Category',
        'basic_needs_category.*.view' => 'Basic Needs Category',
        'basic_needs_category.create' => 'Basic Needs Category',
        'basic_needs_category.view-any' => 'Basic Needs Category',
        'basic_needs_program.*.delete' => 'Basic Needs Program',
        'basic_needs_program.*.force-delete' => 'Basic Needs Program',
        'basic_needs_program.*.restore' => 'Basic Needs Program',
        'basic_needs_program.*.update' => 'Basic Needs Program',
        'basic_needs_program.*.view' => 'Basic Needs Program',
        'basic_needs_program.create' => 'Basic Needs Program',
        'basic_needs_program.view-any' => 'Basic Needs Program',
        'case_form.*.delete' => 'Case Form',
        'case_form.*.force-delete' => 'Case Form',
        'case_form.*.restore' => 'Case Form',
        'case_form.*.update' => 'Case Form',
        'case_form.*.view' => 'Case Form',
        'case_form.create' => 'Case Form',
        'case_form.view-any' => 'Case Form',
        'case_priority.*.delete' => 'Case Priority',
        'case_priority.*.force-delete' => 'Case Priority',
        'case_priority.*.restore' => 'Case Priority',
        'case_priority.*.update' => 'Case Priority',
        'case_priority.*.view' => 'Case Form',
        'case_priority.create' => 'Case Form',
        'case_priority.view-any' => 'Case Priority',
        'case_status.*.delete' => 'Case Status',
        'case_status.*.force-delete' => 'Case Status',
        'case_status.*.restore' => 'Case Status',
        'case_status.*.update' => 'Case Status',
        'case_status.*.view' => 'Case Status',
        'case_status.create' => 'Case Status',
        'case_status.view-any' => 'Case Status',
        'case_type.*.delete' => 'Case Type',
        'case_type.*.force-delete' => 'Case Type',
        'case_type.*.restore' => 'Case Type',
        'case_type.*.update' => 'Case Type',
        'case_type.*.view' => 'Case Type',
        'case_type.create' => 'Case Type',
        'case_type.view-any' => 'Case Type',
        'change_request_status.*.delete' => 'Change Request Status',
        'change_request_status.*.force-delete' => 'Change Request Status',
        'change_request_status.*.restore' => 'Change Request Status',
        'change_request_status.*.update' => 'Change Request Status',
        'change_request_status.*.view' => 'Change Request Status',
        'change_request_status.create' => 'Change Request Status',
        'change_request_status.view-any' => 'Change Request Status',
        'change_request_type.*.delete' => 'Change Request Type',
        'change_request_type.*.force-delete' => 'Change Request Type',
        'change_request_type.*.restore' => 'Change Request Type',
        'change_request_type.*.update' => 'Change Request Type',
        'change_request_type.*.view' => 'Change Request Type',
        'change_request_type.create' => 'Change Request Type',
        'change_request_type.view-any' => 'Change Request Type',
        'college_branding.manage_college_brand_settings' => 'College Branding',
        'consent_agreement.*.update' => 'Consent Agreement',
        'consent_agreement.*.view' => 'Consent Agreement',
        'consent_agreement.view-any' => 'Consent Agreement',
        'display_settings.manage' => 'Display Settings',
        'email_template.*.delete' => 'Email Template',
        'email_template.*.force-delete' => 'Email Template',
        'email_template.*.restore' => 'Email Template',
        'email_template.*.update' => 'Email Template',
        'email_template.*.view' => 'Email Template',
        'email_template.create' => 'Email Template',
        'email_template.view-any' => 'Email Template',
        'interaction_driver.*.delete' => 'Interaction Driver',
        'interaction_driver.*.force-delete' => 'Interaction Driver',
        'interaction_driver.*.restore' => 'Interaction Driver',
        'interaction_driver.*.update' => 'Interaction Driver',
        'interaction_driver.*.view' => 'Interaction Driver',
        'interaction_driver.create' => 'Interaction Driver',
        'interaction_driver.view-any' => 'Interaction Driver',
        'interaction_initiative.*.delete' => 'Interaction Initiative',
        'interaction_initiative.*.force-delete' => 'Interaction Initiative',
        'interaction_initiative.*.restore' => 'Interaction Initiative',
        'interaction_initiative.*.update' => 'Interaction Initiative',
        'interaction_initiative.*.view' => 'Interaction Initiative',
        'interaction_initiative.create' => 'Interaction Initiative',
        'interaction_initiative.view-any' => 'Interaction Initiative',
        'interaction_outcome.*.delete' => 'Interaction Outcome',
        'interaction_outcome.*.force-delete' => 'Interaction Outcome',
        'interaction_outcome.*.restore' => 'Interaction Outcome',
        'interaction_outcome.*.update' => 'Interaction Outcome',
        'interaction_outcome.*.view' => 'Interaction Outcome',
        'interaction_outcome.create' => 'Interaction Outcome',
        'interaction_outcome.view-any' => 'Interaction Outcome',
        'interaction_relation.*.delete' => 'Interaction Relation',
        'interaction_relation.*.force-delete' => 'Interaction Relation',
        'interaction_relation.*.restore' => 'Interaction Relation',
        'interaction_relation.*.update' => 'Interaction Relation',
        'interaction_relation.*.view' => 'Interaction Relation',
        'interaction_relation.create' => 'Interaction Relation',
        'interaction_relation.view-any' => 'Interaction Relation',
        'interaction_status.*.delete' => 'Interaction Status',
        'interaction_status.*.force-delete' => 'Interaction Status',
        'interaction_status.*.restore' => 'Interaction Status',
        'interaction_status.*.update' => 'Interaction Status',
        'interaction_status.*.view' => 'Interaction Status',
        'interaction_status.create' => 'Interaction Status',
        'interaction_status.view-any' => 'Interaction Status',
        'interaction_type.*.delete' => 'Interaction Type',
        'interaction_type.*.force-delete' => 'Interaction Type',
        'interaction_type.*.restore' => 'Interaction Type',
        'interaction_type.*.update' => 'Interaction Type',
        'interaction_type.*.view' => 'Interaction Type',
        'interaction_type.create' => 'Interaction Type',
        'interaction_type.view-any' => 'Interaction Type',
        'manage_prospect_pipeline_settings' => 'Pipieline',
        'notification_setting.*.delete' => 'Notification Setting',
        'notification_setting.*.force-delete' => 'Notification Setting',
        'notification_setting.*.restore' => 'Notification Setting',
        'notification_setting.*.update' => 'Notification Setting',
        'notification_setting.*.view' => 'Notification Setting',
        'notification_setting.create' => 'Notification Setting',
        'notification_setting.view-any' => 'Notification Setting',
        'prompt_type.*.delete' => 'Prompt Type',
        'prompt_type.*.force-delete' => 'Prompt Type',
        'prompt_type.*.restore' => 'Prompt Type',
        'prompt_type.*.update' => 'Prompt Type',
        'prompt_type.*.view' => 'Prompt Type',
        'prompt_type.create' => 'Prompt Type',
        'prompt_type.view-any' => 'Prompt Type',
        'pronouns.*.delete' => 'Pronouns',
        'pronouns.*.force-delete' => 'Pronouns',
        'pronouns.*.restore' => 'Pronouns',
        'pronouns.*.update' => 'Pronouns',
        'pronouns.*.view' => 'Pronouns',
        'pronouns.create' => 'Pronouns',
        'pronouns.view-any' => 'Pronouns',
        'prospect_conversion.manage' => 'Prospect Conversion',
        'prospect_source.*.delete' => 'Prospect Source',
        'prospect_source.*.force-delete' => 'Prospect Source',
        'prospect_source.*.restore' => 'Prospect Source',
        'prospect_source.*.update' => 'Prospect Source',
        'prospect_source.*.view' => 'Prospect Source',
        'prospect_source.create' => 'Prospect Source',
        'prospect_source.view-any' => 'Prospect Source',
        'prospect_status.*.delete' => 'Prospect Status',
        'prospect_status.*.force-delete' => 'Prospect Status',
        'prospect_status.*.restore' => 'Prospect Status',
        'prospect_status.*.update' => 'Prospect Status',
        'prospect_status.*.view' => 'Prospect Status',
        'prospect_status.create' => 'Prospect Status',
        'prospect_status.view-any' => 'Prospect Status',
        'resource_hub_category.*.delete' => 'Resource Hub Category',
        'resource_hub_category.*.force-delete' => 'Resource Hub Category',
        'resource_hub_category.*.restore' => 'Resource Hub Category',
        'resource_hub_category.*.update' => 'Resource Hub Category',
        'resource_hub_category.*.view' => 'Resource Hub Category',
        'resource_hub_category.create' => 'Resource Hub Category',
        'resource_hub_category.view-any' => 'Resource Hub Category',
        'resource_hub_quality.*.delete' => 'Resource Hub Quality',
        'resource_hub_quality.*.force-delete' => 'Resource Hub Quality',
        'resource_hub_quality.*.restore' => 'Resource Hub Quality',
        'resource_hub_quality.*.update' => 'Resource Hub Quality',
        'resource_hub_quality.*.view' => 'Resource Hub Quality',
        'resource_hub_quality.create' => 'Resource Hub Quality',
        'resource_hub_quality.view-any' => 'Resource Hub Quality',
        'resource_hub_status.*.delete' => 'Resource Hub Status',
        'resource_hub_status.*.force-delete' => 'Resource Hub Status',
        'resource_hub_status.*.restore' => 'Resource Hub Status',
        'resource_hub_status.*.update' => 'Resource Hub Status',
        'resource_hub_status.*.view' => 'Resource Hub Status',
        'resource_hub_status.create' => 'Resource Hub Status',
        'resource_hub_status.view-any' => 'Resource Hub Status',
        'sla.*.delete' => 'SLA',
        'sla.*.force-delete' => 'SLA',
        'sla.*.restore' => 'SLA',
        'sla.*.update' => 'SLA',
        'sla.*.view' => 'SLA',
        'sla.create' => 'SLA',
        'sla.view-any' => 'SLA',
        'sms_template.*.delete' => 'SMS Template',
        'sms_template.*.force-delete' => 'SMS Template',
        'sms_template.*.restore' => 'SMS Template',
        'sms_template.*.update' => 'SMS Template',
        'sms_template.*.view' => 'SMS Template',
        'sms_template.create' => 'SMS Template',
        'sms_template.view-any' => 'SMS Template',
        'student_record_manager.configuration' => 'Student Record Manager',
        'student_record_manager.*.delete' => 'Student Record Manager',
        'student_record_manager.*.force-delete' => 'Student Record Manager',
        'student_record_manager.*.restore' => 'Student Record Manager',
        'student_record_manager.*.update' => 'Student Record Manager',
        'student_record_manager.*.view' => 'Student Record Manager',
        'student_record_manager.create' => 'Student Record Manager',
        'student_record_manager.view-any' => 'Student Record Manager',
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
        collect($this->guards)
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);
            });
    }
};
