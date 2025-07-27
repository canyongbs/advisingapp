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

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperExport {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Import|null $import
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFailedImportRow {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHealthCheckResultHistoryItem {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FailedImportRow> $failedRows
 * @property-read int|null $failed_rows_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperImport {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandlordSettingsProperty {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $type
 * @property string $cron_expression
 * @property string|null $timezone
 * @property string|null $ping_url
 * @property \Illuminate\Support\Carbon|null $last_started_at
 * @property \Illuminate\Support\Carbon|null $last_finished_at
 * @property \Illuminate\Support\Carbon|null $last_failed_at
 * @property \Illuminate\Support\Carbon|null $last_skipped_at
 * @property \Illuminate\Support\Carbon|null $registered_on_oh_dear_at
 * @property \Illuminate\Support\Carbon|null $last_pinged_at
 * @property int $grace_time_in_minutes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MonitoredScheduledTaskLogItem> $logItems
 * @property-read int|null $log_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereCronExpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereGraceTimeInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereLastFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereLastFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereLastPingedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereLastSkippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereLastStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask wherePingUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereRegisteredOnOhDearAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTask whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMonitoredScheduledTask {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $monitored_scheduled_task_id
 * @property string $type
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MonitoredScheduledTask $monitoredScheduledTask
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem whereMonitoredScheduledTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoredScheduledTaskLogItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMonitoredScheduledTaskLogItem {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\NotificationSettingPivot|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Division\Models\Division> $divisions
 * @property-read int|null $divisions_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationSettingPivot> $settings
 * @property-read int|null $settings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNotificationSetting {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model $relatedTo
 * @property-read \App\Models\NotificationSetting|null $setting
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNotificationSettingPivot {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\PronounsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPronouns {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSettingsProperty {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\SystemUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSystemUser {}
}

namespace App\Models{
/**
 * 
 *
 * @property \App\Enums\TagType $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Taggable|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Student> $students
 * @property-read int|null $students_count
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTag {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model $prospects
 * @property-read \Illuminate\Database\Eloquent\Model $students
 * @property-read \App\Models\Tag|null $tag
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taggable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taggable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taggable query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTaggable {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $domain
 * @property mixed $key
 * @property mixed $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $setup_complete
 * @method static \Spatie\Multitenancy\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Spatie\Multitenancy\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereSetupComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTenant {}
}

namespace App\Models{
/**
 * 
 *
 * @property CareTeamRole $careTeamRole
 * @property string $id
 * @property string|null $emplid
 * @property string|null $name
 * @property string|null $email
 * @property bool $is_email_visible_on_profile
 * @property string|null $password
 * @property string|null $remember_token
 * @property string|null $locale
 * @property string|null $type
 * @property bool $is_external
 * @property string|null $bio
 * @property bool $is_bio_visible_on_profile
 * @property string|null $avatar_url
 * @property bool $are_teams_visible_on_profile
 * @property bool $is_division_visible_on_profile
 * @property string $timezone
 * @property bool $has_enabled_public_profile
 * @property string|null $public_profile_slug
 * @property bool $office_hours_are_enabled
 * @property bool $appointments_are_restricted_to_existing_students
 * @property array<array-key, mixed>|null $office_hours
 * @property bool $out_of_office_is_enabled
 * @property \Illuminate\Support\Carbon|null $out_of_office_starts_at
 * @property \Illuminate\Support\Carbon|null $out_of_office_ends_at
 * @property string|null $phone_number
 * @property bool $is_phone_number_visible_on_profile
 * @property bool $working_hours_are_enabled
 * @property bool $are_working_hours_visible_on_profile
 * @property array<array-key, mixed>|null $working_hours
 * @property string|null $job_title
 * @property string|null $pronouns_id
 * @property bool $are_pronouns_visible_on_profile
 * @property bool $default_assistant_chat_folders_created
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiAssistantUpvote> $aiAssistantUpvotes
 * @property-read int|null $ai_assistant_upvotes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiThreadFolder> $aiThreadFolders
 * @property-read int|null $ai_thread_folders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiThread> $aiThreads
 * @property-read int|null $ai_threads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Task\Models\Task> $assignedTasks
 * @property-read int|null $assigned_tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\MeetingCenter\Models\Calendar|null $calendar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CareTeam\Models\CareTeam> $careTeams
 * @property-read int|null $care_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseAssignment> $caseAssignments
 * @property-read int|null $case_assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseType> $caseTypeIndividualAssignment
 * @property-read int|null $case_type_individual_assignment_count
 * @property-read \AdvisingApp\Notification\Models\Subscription|\AdvisingApp\CareTeam\Models\CareTeam|\AdvisingApp\Consent\Models\UserConsentAgreement|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Consent\Models\ConsentAgreement> $consentAgreements
 * @property-read int|null $consent_agreements_count
 * @property-read \AdvisingApp\InAppCommunication\Models\TwilioConversationUser|null $participant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\InAppCommunication\Models\TwilioConversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementBatch> $engagementBatches
 * @property-read int|null $engagement_batches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\CalendarEvent> $events
 * @property-read int|null $events_count
 * @property-read bool $is_admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\License> $licenses
 * @property-read int|null $licenses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Report\Models\TrackedEvent> $logins
 * @property-read int|null $logins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Report\Models\TrackedEventCount> $loginsCount
 * @property-read int|null $logins_count_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $multifactor_recovery_codes
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Pronouns|null $pronouns
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospectCareTeams
 * @property-read int|null $prospect_care_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospectSubscriptions
 * @property-read int|null $prospect_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Research\Models\ResearchRequestFolder> $researchRequestFolders
 * @property-read int|null $research_request_folders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Research\Models\ResearchRequest> $researchRequests
 * @property-read int|null $research_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Segment\Models\Segment> $segments
 * @property-read int|null $segments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Student> $studentCareTeams
 * @property-read int|null $student_care_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Student> $studentSubscriptions
 * @property-read int|null $student_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \AdvisingApp\Team\Models\Team|null $team
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Alert\Models\Alert[] $studentAlerts
 * @property-read int|null $student_alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Alert\Models\Alert[] $prospectAlerts
 * @property-read int|null $prospect_alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Authorization\Models\Permission[] $permissionsFromRoles
 * @property-read int|null $permissions_from_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\CaseManagement\Models\CaseModel[] $cases
 * @property-read int|null $cases_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User advancedFilter($data)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppointmentsAreRestrictedToExistingStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereArePronounsVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAreTeamsVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAreWorkingHoursVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDefaultAssistantChatFoldersCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmplid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereHasEnabledPublicProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBioVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsDivisionVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsEmailVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsPhoneNumberVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOfficeHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOfficeHoursAreEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutOfOfficeEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutOfOfficeIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutOfOfficeStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePronounsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePublicProfileSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWorkingHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWorkingHoursAreEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property \AdvisingApp\Ai\Enums\AiAssistantApplication $application
 * @property \AdvisingApp\Ai\Enums\AiModel $model
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiAssistantFile> $files
 * @property-read int|null $files_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiThread> $threads
 * @property-read int|null $threads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiAssistantUpvote> $upvotes
 * @property-read int|null $upvotes_count
 * @method static \AdvisingApp\Ai\Database\Factories\AiAssistantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistant {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore|null $openAiVectorStore
 * @method static \AdvisingApp\Ai\Database\Factories\AiAssistantFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistantFile {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistantUpvote {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiMessageFile> $files
 * @property-read int|null $files_count
 * @property-read \AdvisingApp\Ai\Models\Prompt|null $prompt
 * @property-read \AdvisingApp\Ai\Models\AiThread|null $thread
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Ai\Database\Factories\AiMessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiMessage {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\Ai\Models\AiMessage|null $message
 * @property-read \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore|null $openAiVectorStore
 * @method static \AdvisingApp\Ai\Database\Factories\AiMessageFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiMessageFile {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \AdvisingApp\Ai\Models\AiThreadFolder|null $folder
 * @property-read \Carbon\CarbonInterface|null $last_engaged_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User|null $user
 * @property-read \AdvisingApp\Ai\Models\AiMessage|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \AdvisingApp\Ai\Database\Factories\AiThreadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThread onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThread query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThread withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThread withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiThread {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property \AdvisingApp\Ai\Enums\AiAssistantApplication $application
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiThread> $threads
 * @property-read int|null $threads_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Ai\Database\Factories\AiThreadFolderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiThreadFolder {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property \AdvisingApp\Ai\Enums\AiMessageLogFeature $feature
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegacyAiMessageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegacyAiMessageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegacyAiMessageLog query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLegacyAiMessageLog {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Ai\Models\PromptType|null $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\PromptUpvote> $upvotes
 * @property-read int|null $upvotes_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\PromptUse> $uses
 * @property-read int|null $uses_count
 * @method static \AdvisingApp\Ai\Database\Factories\PromptFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prompt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prompt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prompt onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prompt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prompt withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prompt withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPrompt {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\Prompt> $prompts
 * @property-read int|null $prompts_count
 * @method static \AdvisingApp\Ai\Database\Factories\PromptTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPromptType {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Ai\Models\Prompt|null $prompt
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPromptUpvote {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Ai\Models\Prompt|null $prompt
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Ai\Database\Factories\PromptUseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPromptUse {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property \AdvisingApp\Ai\Enums\AiModel|null $model
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_embed_enabled
 * @property array<array-key, mixed>|null $authorized_domains
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\QnaAdvisorCategory> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\QnaAdvisorFile> $files
 * @property-read int|null $files_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\QnaAdvisorQuestion> $questions
 * @property-read int|null $questions_count
 * @method static \AdvisingApp\Ai\Database\Factories\QnaAdvisorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereAuthorizedDomains($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereIsEmbedEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisor withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperQnaAdvisor {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Ai\Models\QnaAdvisor|null $qnaAdvisor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\QnaAdvisorQuestion> $questions
 * @property-read int|null $questions_count
 * @method static \AdvisingApp\Ai\Database\Factories\QnaAdvisorCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperQnaAdvisorCategory {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Ai\Models\QnaAdvisor|null $advisor
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore|null $openAiVectorStore
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorFile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorFile withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperQnaAdvisorFile {}
}

namespace AdvisingApp\Ai\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Ai\Models\QnaAdvisorCategory|null $category
 * @method static \AdvisingApp\Ai\Database\Factories\QnaAdvisorQuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorQuestion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QnaAdvisorQuestion withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperQnaAdvisorQuestion {}
}

namespace AdvisingApp\Alert\Models{
/**
 * 
 *
 * @property-read (Subscribable&(Student|Prospect))|null $concern
 * @property \AdvisingApp\Alert\Enums\AlertSeverity $severity
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Alert\Histories\AlertHistory> $histories
 * @property-read int|null $histories_count
 * @property-read \AdvisingApp\Alert\Models\AlertStatus|null $status
 * @method static \AdvisingApp\Alert\Database\Factories\AlertFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAlert {}
}

namespace AdvisingApp\Alert\Models{
/**
 * 
 *
 * @property \AdvisingApp\Alert\Enums\SystemAlertStatusClassification $classification
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Alert\Models\Alert> $alerts
 * @property-read int|null $alerts_count
 * @method static \AdvisingApp\Alert\Database\Factories\AlertStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAlertStatus {}
}

namespace AdvisingApp\Application\Models{
/**
 * 
 *
 * @property \AdvisingApp\Form\Enums\Rounding $rounding
 * @property-read mixed $allowed_domains
 * @property-read mixed $content
 * @property-read mixed $embed_enabled
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $is_wizard
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationStep> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowTrigger> $workflowTriggers
 * @property-read int|null $workflow_triggers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Workflow\Models\Workflow[] $workflows
 * @property-read int|null $workflows_count
 * @method static \AdvisingApp\Application\Database\Factories\ApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplication {}
}

namespace AdvisingApp\Application\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $author
 * @property-read \AdvisingApp\Application\Models\Application|null $submissible
 * @method static \AdvisingApp\Application\Database\Factories\ApplicationAuthenticationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationAuthentication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationAuthentication {}
}

namespace AdvisingApp\Application\Models{
/**
 * 
 *
 * @property-read mixed $config
 * @property-read mixed $is_required
 * @property-read mixed $label
 * @property-read \AdvisingApp\Application\Models\ApplicationStep|null $step
 * @property-read \AdvisingApp\Application\Models\Application|null $submissible
 * @property-read mixed $type
 * @method static \AdvisingApp\Application\Database\Factories\ApplicationFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationField {}
}

namespace AdvisingApp\Application\Models{
/**
 * 
 *
 * @property-read mixed $content
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $label
 * @property-read \AdvisingApp\Application\Models\Application|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationStep {}
}

namespace AdvisingApp\Application\Models{
/**
 * @property string $id
 * @property string $application_id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string $state_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmissionsChecklistItem> $checklistItems
 * @property-read int|null $checklist_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read \AdvisingApp\Application\Models\ApplicationSubmissionState|null $state
 * @property-read \AdvisingApp\Application\Models\Application|null $submissible
 * @method static \AdvisingApp\Application\Database\Factories\ApplicationSubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationSubmission {}
}

namespace AdvisingApp\Application\Models{
/**
 * 
 *
 * @property \AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification $classification
 * @property \AdvisingApp\Application\Enums\ApplicationSubmissionStateColorOptions $color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \AdvisingApp\Application\Database\Factories\ApplicationSubmissionStateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationSubmissionState {}
}

namespace AdvisingApp\Application\Models{
/**
 * @property string $id
 * @property string $application_submission_id
 * @property string $title
 * @property bool $is_checked
 * @property string|null $created_by
 * @property string|null $completed_by
 * @property \Illuminate\Support\Carbon|null $completed_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \AdvisingApp\Application\Models\ApplicationSubmission $applicationSubmission
 * @property-read \App\Models\User|null $completedBy
 * @property-read \App\Models\User|null $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereApplicationSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereCompletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereCompletedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereIsChecked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionsChecklistItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationSubmissionsChecklistItem {}
}

namespace AdvisingApp\Audit\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $change_agent_type
 * @property string|null $change_agent_id
 * @property string $event
 * @property string $auditable_type
 * @property string $auditable_id
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $user
 * @method static \AdvisingApp\Audit\Database\Factories\AuditFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereChangeAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereChangeAgentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUserAgent($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAudit {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * 
 *
 * @property \AdvisingApp\Authorization\Enums\LicenseType $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLicense {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Authorization\Models\PermissionGroup|null $group
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SystemUser> $systemUsers
 * @property-read int|null $system_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission api()
 * @method static \AdvisingApp\Authorization\Database\Factories\PermissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission web()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermission {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermissionGroup {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role api()
 * @method static \AdvisingApp\Authorization\Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role superAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role web()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRole {}
}

namespace AdvisingApp\BasicNeeds\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\BasicNeeds\Models\BasicNeedsProgram> $basicNeedsProgram
 * @property-read int|null $basic_needs_program_count
 * @method static \AdvisingApp\BasicNeeds\Database\Factories\BasicNeedsCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBasicNeedsCategory {}
}

namespace AdvisingApp\BasicNeeds\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\BasicNeeds\Models\BasicNeedsCategory|null $basicNeedsCategories
 * @method static \AdvisingApp\BasicNeeds\Database\Factories\BasicNeedsProgramFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBasicNeedsProgram {}
}

namespace AdvisingApp\Campaign\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Campaign\Models\CampaignAction> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model $createdBy
 * @property-read \AdvisingApp\Segment\Models\Segment|null $segment
 * @method static \AdvisingApp\Campaign\Database\Factories\CampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign hasNotBeenExecuted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCampaign {}
}

namespace AdvisingApp\Campaign\Models{
/**
 * 
 *
 * @property \AdvisingApp\Campaign\Enums\CampaignActionType $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Campaign\Models\Campaign|null $campaign
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Campaign\Models\CampaignActionEducatable> $campaignActionEducatables
 * @property-read int|null $campaign_action_educatables_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction campaignEnabled()
 * @method static \AdvisingApp\Campaign\Database\Factories\CampaignActionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCampaignAction {}
}

namespace AdvisingApp\Campaign\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Campaign\Models\CampaignAction|null $campaignAction
 * @property-read \Illuminate\Database\Eloquent\Model $educatable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Campaign\Models\CampaignActionEducatableRelated> $related
 * @property-read int|null $related_count
 * @method static \AdvisingApp\Campaign\Database\Factories\CampaignActionEducatableFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignActionEducatable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignActionEducatable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignActionEducatable query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCampaignActionEducatable {}
}

namespace AdvisingApp\Campaign\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Campaign\Models\CampaignActionEducatable|null $campaignActionEducatable
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignActionEducatableRelated newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignActionEducatableRelated newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignActionEducatableRelated query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCampaignActionEducatableRelated {}
}

namespace AdvisingApp\CareTeam\Models{
/**
 * 
 *
 * @property string $care_team_role_id
 * @property Educatable $educatable
 * @property-read \AdvisingApp\CareTeam\Models\CareTeamRole|null $careTeamRole
 * @property-read \AdvisingApp\CareTeam\Models\CareTeamRole|null $prospectCareTeamRole
 * @property-read \AdvisingApp\CareTeam\Models\CareTeamRole|null $studentCareTeamRole
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\CareTeam\Database\Factories\CareTeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeam query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCareTeam {}
}

namespace AdvisingApp\CareTeam\Models{
/**
 * 
 *
 * @property bool $is_default
 * @property \App\Enums\CareTeamRoleType $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CareTeam\Models\CareTeam> $careTeams
 * @property-read int|null $care_teams_count
 * @method static \AdvisingApp\CareTeam\Database\Factories\CareTeamRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCareTeamRole {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property \AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus $status
 * @property-read \App\Models\User|null $assignedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\CaseManagement\Models\CaseModel|null $case
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseAssignmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseAssignment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseAssignment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseAssignment withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseAssignment {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read Educatable $assignee
 * @property-read \AdvisingApp\CaseManagement\Models\CaseModel|null $case
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFeedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFeedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFeedback onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFeedback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFeedback withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFeedback withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseFeedback {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property \AdvisingApp\Form\Enums\Rounding $rounding
 * @property-read mixed $allowed_domains
 * @property-read mixed $content
 * @property-read mixed $embed_enabled
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseFormField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $is_wizard
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseFormStep> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseFormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \AdvisingApp\CaseManagement\Models\CaseType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseForm query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseForm {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $author
 * @property-read \AdvisingApp\CaseManagement\Models\CaseForm|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormAuthentication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseFormAuthentication {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read mixed $config
 * @property-read mixed $is_required
 * @property-read mixed $label
 * @property-read \AdvisingApp\CaseManagement\Models\CaseForm|null $step
 * @property-read \AdvisingApp\CaseManagement\Models\CaseForm|null $submissible
 * @property-read mixed $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseFormField {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read mixed $content
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseFormField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $label
 * @property-read \AdvisingApp\CaseManagement\Models\CaseForm|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseFormStep {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property Student|Prospect|null $author
 * @property \AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod $request_method
 * @property-read \AdvisingApp\CaseManagement\Models\CaseModel|null $case
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseFormField> $fields
 * @property-read int|null $fields_count
 * @property-read \AdvisingApp\CaseManagement\Models\CasePriority|null $priority
 * @property-read \App\Models\User|null $requester
 * @property-read \AdvisingApp\CaseManagement\Models\CaseForm|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission canceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission notSubmitted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission requested()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseFormSubmission submitted()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseFormSubmission {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\CaseManagement\Models\CaseModel|null $case
 * @property-read mixed $new_values_formatted
 * @property-read mixed $original_values_formatted
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseHistory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseHistory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseHistory {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read Student|Prospect $respondent
 * @property-read \AdvisingApp\CaseManagement\Models\CaseAssignment|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseAssignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\CaseManagement\Models\CaseFormSubmission|null $caseFormSubmission
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseUpdate> $caseUpdates
 * @property-read int|null $case_updates_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \AdvisingApp\Division\Models\Division|null $division
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\EmailMessage> $emailMessages
 * @property-read int|null $email_messages_count
 * @property-read \AdvisingApp\CaseManagement\Models\CaseFeedback|null $feedback
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseHistory> $histories
 * @property-read int|null $histories_count
 * @property-read \AdvisingApp\CaseManagement\Models\CaseAssignment|null $initialAssignment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \AdvisingApp\CaseManagement\Models\CaseUpdate|null $latestInboundCaseUpdate
 * @property-read \AdvisingApp\CaseManagement\Models\CaseUpdate|null $latestOutboundCaseUpdate
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $orderedInteractions
 * @property-read int|null $ordered_interactions_count
 * @property-read \AdvisingApp\CaseManagement\Models\CasePriority|null $priority
 * @property-read \AdvisingApp\CaseManagement\Models\CaseStatus|null $status
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseModelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseModel {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseModel> $cases
 * @property-read int|null $cases_count
 * @property-read \AdvisingApp\CaseManagement\Models\Sla|null $sla
 * @property-read \AdvisingApp\CaseManagement\Models\CaseType|null $type
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CasePriorityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CasePriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CasePriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CasePriority onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CasePriority query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CasePriority withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CasePriority withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCasePriority {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property \AdvisingApp\CaseManagement\Enums\SystemCaseClassification $classification
 * @property \AdvisingApp\CaseManagement\Enums\ColumnColorOptions $color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseModel> $cases
 * @property-read int|null $cases_count
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseStatus {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property \AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes $assignment_type
 * @property-read \App\Models\User|null $assignmentTypeIndividual
 * @property-read \AdvisingApp\CaseManagement\Models\CaseTypeManager|\AdvisingApp\CaseManagement\Models\CaseTypeAuditor|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Team> $auditors
 * @property-read int|null $auditors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseModel> $cases
 * @property-read int|null $cases_count
 * @property-read \AdvisingApp\CaseManagement\Models\CaseForm|null $form
 * @property-read \App\Models\User|null $lastAssignedUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Team> $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CasePriority> $priorities
 * @property-read int|null $priorities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseTypeEmailTemplate> $templates
 * @property-read int|null $templates_count
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseType {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\CaseManagement\Models\CaseType|null $caseType
 * @property-read \AdvisingApp\Team\Models\Team|null $team
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseTypeAuditorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeAuditor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeAuditor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeAuditor query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseTypeAuditor {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property \AdvisingApp\CaseManagement\Enums\CaseEmailTemplateType $type
 * @property \AdvisingApp\CaseManagement\Enums\CaseTypeEmailTemplateRole $role
 * @property-read \AdvisingApp\CaseManagement\Models\CaseType|null $caseType
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseTypeEmailTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeEmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeEmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeEmailTemplate query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseTypeEmailTemplate {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\CaseManagement\Models\CaseType|null $caseType
 * @property-read \AdvisingApp\Team\Models\Team|null $team
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseTypeManagerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseTypeManager query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseTypeManager {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property \AdvisingApp\CaseManagement\Enums\CaseUpdateDirection $direction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\CaseManagement\Models\CaseModel|null $case
 * @method static \AdvisingApp\CaseManagement\Database\Factories\CaseUpdateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseUpdate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseUpdate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseUpdate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCaseUpdate {}
}

namespace AdvisingApp\CaseManagement\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CasePriority> $casePriorities
 * @property-read int|null $case_priorities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSla {}
}

namespace AdvisingApp\Consent\Models{
/**
 * 
 *
 * @property \AdvisingApp\Consent\Enums\ConsentAgreementType $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Consent\Models\UserConsentAgreement> $userConsentAgreements
 * @property-read int|null $user_consent_agreements_count
 * @property-read \AdvisingApp\Consent\Models\UserConsentAgreement|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \AdvisingApp\Consent\Database\Factories\ConsentAgreementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsentAgreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsentAgreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsentAgreement query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConsentAgreement {}
}

namespace AdvisingApp\Consent\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Consent\Models\ConsentAgreement|null $consentAgreement
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserConsentAgreement {}
}

namespace AdvisingApp\Division\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $code
 * @property string|null $header
 * @property string|null $footer
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \App\Models\NotificationSettingPivot|null $notificationSetting
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \AdvisingApp\Division\Database\Factories\DivisionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereHeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereLastUpdatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDivision {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * 
 *
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Engagement\Database\Factories\EmailTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailTemplate {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * 
 *
 * @property-read ?Educatable $recipient
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Engagement\Models\EngagementBatch|null $batch
 * @property-read \AdvisingApp\Campaign\Models\CampaignAction|null $campaignAction
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\EmailMessage> $emailMessages
 * @property-read int|null $email_messages_count
 * @property-read \AdvisingApp\Engagement\Models\EngagementBatch|null $engagementBatch
 * @property-read \AdvisingApp\Notification\Models\EmailMessage|null $latestEmailMessage
 * @property-read \AdvisingApp\Notification\Models\SmsMessage|null $latestSmsMessage
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\SmsMessage> $smsMessages
 * @property-read int|null $sms_messages_count
 * @property-read \AdvisingApp\Timeline\Models\Timeline|null $timelineRecord
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Engagement\Database\Factories\EngagementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement isNotPartOfABatch()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement sentToProspect()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement sentToStudent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagement {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $identifier
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @method static \AdvisingApp\Engagement\Database\Factories\EngagementBatchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementBatch {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $description
 * @property string|null $retention_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model $createdBy
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\Engagement\Models\EngagementFileEntities|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Student> $students
 * @property-read int|null $students_count
 * @method static \AdvisingApp\Engagement\Database\Factories\EngagementFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereRetentionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementFile {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * 
 *
 * @property string $engagement_file_id
 * @property string $entity_id
 * @property string $entity_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Engagement\Models\EngagementFile $engagementFile
 * @property-read \Illuminate\Database\Eloquent\Model $entity
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereEngagementFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementFileEntities {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * 
 *
 * @property \AdvisingApp\Engagement\Enums\EngagementResponseType $type
 * @property \AdvisingApp\Engagement\Enums\EngagementResponseStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Model $sender
 * @property-read \AdvisingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AdvisingApp\Engagement\Database\Factories\EngagementResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse sentByProspect()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse sentByStudent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementResponse {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * 
 *
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Engagement\Database\Factories\SmsTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSmsTemplate {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * 
 *
 * @property \AdvisingApp\Engagement\Enums\EngagementResponseType $type
 * @method static \AdvisingApp\Engagement\Database\Factories\UnmatchedInboundCommunicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnmatchedInboundCommunication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnmatchedInboundCommunication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnmatchedInboundCommunication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUnmatchedInboundCommunication {}
}

namespace AdvisingApp\Form\Models{
/**
 * 
 *
 * @property \AdvisingApp\Form\Enums\Rounding $rounding
 * @property-read mixed $allowed_domains
 * @property-read mixed $content
 * @property-read \AdvisingApp\Form\Models\FormEmailAutoReply|null $emailAutoReply
 * @property-read mixed $embed_enabled
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $is_wizard
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormStep> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowTrigger> $workflowTriggers
 * @property-read int|null $workflow_triggers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Workflow\Models\Workflow[] $workflows
 * @property-read int|null $workflows_count
 * @method static \AdvisingApp\Form\Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperForm {}
}

namespace AdvisingApp\Form\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $author
 * @property-read \AdvisingApp\Form\Models\Form|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormAuthentication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormAuthentication {}
}

namespace AdvisingApp\Form\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Form\Models\Form|null $form
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormEmailAutoReply {}
}

namespace AdvisingApp\Form\Models{
/**
 * 
 *
 * @property-read mixed $config
 * @property-read mixed $is_required
 * @property-read mixed $label
 * @property-read \AdvisingApp\Form\Models\FormStep|null $step
 * @property-read \AdvisingApp\Form\Models\Form|null $submissible
 * @property-read \AdvisingApp\Form\Models\FormFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read mixed $type
 * @method static \AdvisingApp\Form\Database\Factories\FormFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormField {}
}

namespace AdvisingApp\Form\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Form\Models\FormField|null $field
 * @property-read \AdvisingApp\Form\Models\FormSubmission|null $submission
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormFieldSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormFieldSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormFieldSubmission query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormFieldSubmission {}
}

namespace AdvisingApp\Form\Models{
/**
 * 
 *
 * @property-read mixed $content
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $label
 * @property-read \AdvisingApp\Form\Models\Form|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormStep {}
}

namespace AdvisingApp\Form\Models{
/**
 * 
 *
 * @property Student|Prospect|null $author
 * @property \AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod $request_method
 * @property-read \AdvisingApp\Form\Models\FormFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read \App\Models\User|null $requester
 * @property-read \AdvisingApp\Form\Models\Form|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission canceled()
 * @method static \AdvisingApp\Form\Database\Factories\FormSubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission notSubmitted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission requested()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission submitted()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormSubmission {}
}

namespace AdvisingApp\InAppCommunication\Models{
/**
 * 
 *
 * @property \AdvisingApp\InAppCommunication\Enums\ConversationType $type
 * @property-read \AdvisingApp\InAppCommunication\Models\TwilioConversationUser|null $participant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTwilioConversation {}
}

namespace AdvisingApp\InAppCommunication\Models{
/**
 * 
 *
 * @property \AdvisingApp\InAppCommunication\Enums\ConversationNotificationPreference $notification_preference
 * @property-read \AdvisingApp\InAppCommunication\Models\TwilioConversation|null $conversation
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTwilioConversationUser {}
}

namespace AdvisingApp\IntegrationOpenAi\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Research\Models\ResearchRequest|null $researchRequest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOpenAiResearchRequestVectorStore {}
}

namespace AdvisingApp\IntegrationOpenAi\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model $file
 * @method static \AdvisingApp\IntegrationOpenAi\Database\Factories\OpenAiVectorStoreFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOpenAiVectorStore {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Interaction\Models\InteractionConfidentialUser|\AdvisingApp\Interaction\Models\InteractionConfidentialTeam|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Team> $confidentialAccessTeams
 * @property-read int|null $confidential_access_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $confidentialAccessUsers
 * @property-read int|null $confidential_access_users_count
 * @property-read \AdvisingApp\Division\Models\Division|null $division
 * @property-read \AdvisingApp\Interaction\Models\InteractionDriver|null $driver
 * @property-read \AdvisingApp\Interaction\Models\InteractionInitiative|null $initiative
 * @property-read \Illuminate\Database\Eloquent\Model $interactable
 * @property-read \AdvisingApp\Interaction\Models\InteractionOutcome|null $outcome
 * @property-read \AdvisingApp\Interaction\Models\InteractionRelation|null $relation
 * @property-read \AdvisingApp\Interaction\Models\InteractionStatus|null $status
 * @property-read \AdvisingApp\Timeline\Models\Timeline|null $timelineRecord
 * @property-read \AdvisingApp\Interaction\Models\InteractionType|null $type
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteraction {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Interaction\Models\Interaction|null $interaction
 * @property-read \AdvisingApp\Team\Models\Team|null $team
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionConfidentialTeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionConfidentialTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionConfidentialTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionConfidentialTeam query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionConfidentialTeam {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Interaction\Models\Interaction|null $interaction
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionConfidentialUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionConfidentialUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionConfidentialUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionConfidentialUser query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionConfidentialUser {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionDriverFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionDriver {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionInitiativeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionInitiative {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionOutcomeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionOutcome {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionRelationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionRelation {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property \AdvisingApp\Interaction\Enums\InteractionStatusColorOptions $color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionStatus {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionType {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property \AdvisingApp\MeetingCenter\Enums\CalendarProvider $provider_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\CalendarEvent> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\CalendarFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCalendar {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\MeetingCenter\Models\Calendar|null $calendar
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\CalendarEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCalendarEvent {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventAttendee> $attendees
 * @property-read int|null $attendees_count
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationForm|null $eventRegistrationForm
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEvent {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property \AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus $status
 * @property-read \AdvisingApp\MeetingCenter\Models\Event|null $event
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventAttendeeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAttendee query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventAttendee {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property \AdvisingApp\Form\Enums\Rounding $rounding
 * @property-read mixed $allowed_domains
 * @property-read mixed $content
 * @property-read mixed $embed_enabled
 * @property-read \AdvisingApp\MeetingCenter\Models\Event|null $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $is_wizard
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventRegistrationFormFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationForm onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationForm query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationForm withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationForm withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationForm {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property-read EventRegistrationForm $submissible
 * @property-read \AdvisingApp\MeetingCenter\Models\EventAttendee|null $author
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormAuthentication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationFormAuthentication {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property-read mixed $config
 * @property-read mixed $is_required
 * @property-read mixed $label
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep|null $step
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationForm|null $submissible
 * @property-read mixed $type
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventRegistrationFormFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationFormField {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property-read mixed $content
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $label
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationForm|null $submissible
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventRegistrationFormStepFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationFormStep {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * 
 *
 * @property \AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod $request_method
 * @property \AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus $attendee_status
 * @property-read \AdvisingApp\MeetingCenter\Models\EventAttendee|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormField> $fields
 * @property-read int|null $fields_count
 * @property-read \App\Models\User|null $requester
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationForm|null $submissible
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventRegistrationFormSubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationFormSubmission {}
}

namespace AdvisingApp\Notification\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model $recipient
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @method static \AdvisingApp\Notification\Database\Factories\DatabaseMessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDatabaseMessage {}
}

namespace AdvisingApp\Notification\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\EmailMessageEvent> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Model $recipient
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @method static \AdvisingApp\Notification\Database\Factories\EmailMessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailMessage {}
}

namespace AdvisingApp\Notification\Models{
/**
 * 
 *
 * @property \AdvisingApp\Notification\Enums\EmailMessageEventType $type
 * @property-read \AdvisingApp\Notification\Models\EmailMessage|null $message
 * @method static \AdvisingApp\Notification\Database\Factories\EmailMessageEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailMessageEvent {}
}

namespace AdvisingApp\Notification\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\SmsMessageEvent> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Model $recipient
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @method static \AdvisingApp\Notification\Database\Factories\SmsMessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsMessage query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSmsMessage {}
}

namespace AdvisingApp\Notification\Models{
/**
 * 
 *
 * @property \AdvisingApp\Notification\Enums\SmsMessageEventType $type
 * @property-read \AdvisingApp\Notification\Models\SmsMessage|null $message
 * @method static \AdvisingApp\Notification\Database\Factories\SmsMessageEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsMessageEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsMessageEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsMessageEvent query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSmsMessageEvent {}
}

namespace AdvisingApp\Notification\Models{
/**
 * 
 *
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStoredAnonymousNotifiable {}
}

namespace AdvisingApp\Notification\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model $subscribable
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Notification\Database\Factories\SubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSubscription {}
}

namespace AdvisingApp\Portal\Models{
/**
 * 
 *
 * @property Carbon|null $created_at
 * @property \AdvisingApp\Portal\Enums\PortalType $portal_type
 * @property-read \Illuminate\Database\Eloquent\Model $educatable
 * @method static \AdvisingApp\Portal\Database\Factories\PortalAuthenticationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPortalAuthentication {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * 
 *
 * @property string $display_name
 * @property string $id
 * @property string $status_id
 * @property string $source_id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string|null $preferred
 * @property string|null $description
 * @property string|null $email
 * @property string|null $email_2
 * @property string|null $mobile
 * @property bool $sms_opt_out
 * @property bool $email_bounce
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $address_2
 * @property string|null $address_3
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal
 * @property \Illuminate\Support\Carbon|null $birthdate
 * @property string|null $hsgrad
 * @property string|null $assigned_to_id
 * @property string|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectAddress> $additionalAddresses
 * @property-read int|null $additional_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectEmailAddress> $additionalEmailAddresses
 * @property-read int|null $additional_email_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectPhoneNumber> $additionalPhoneNumbers
 * @property-read int|null $additional_phone_numbers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectAddress> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Alert\Models\Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $applicationSubmissions
 * @property-read int|null $application_submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\BasicNeeds\Models\BasicNeedsProgram> $basicNeedsPrograms
 * @property-read int|null $basic_needs_programs_count
 * @property-read \App\Models\Taggable|\AdvisingApp\Notification\Models\Subscription|\AdvisingApp\Engagement\Models\EngagementFileEntities|\AdvisingApp\CareTeam\Models\CareTeam|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $careTeam
 * @property-read int|null $care_team_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseModel> $cases
 * @property-read int|null $cases_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectEmailAddress> $emailAddresses
 * @property-read int|null $email_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventAttendee> $eventAttendeeRecords
 * @property-read int|null $event_attendee_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormSubmission> $formSubmissions
 * @property-read int|null $form_submissions_count
 * @property-read string|null $full_address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementResponse> $orderedEngagementResponses
 * @property-read int|null $ordered_engagement_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\Engagement> $orderedEngagements
 * @property-read int|null $ordered_engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $orderedInteractions
 * @property-read int|null $ordered_interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectPhoneNumber> $phoneNumbers
 * @property-read int|null $phone_numbers_count
 * @property-read \AdvisingApp\Prospect\Models\ProspectAddress|null $primaryAddress
 * @property-read \AdvisingApp\Prospect\Models\ProspectEmailAddress|null $primaryEmailAddress
 * @property-read \AdvisingApp\Prospect\Models\ProspectPhoneNumber|null $primaryPhoneNumber
 * @property-read \AdvisingApp\Prospect\Models\ProspectSource $source
 * @property-read \AdvisingApp\Prospect\Models\ProspectStatus $status
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $subscribedUsers
 * @property-read int|null $subscribed_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Task\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \AdvisingApp\Timeline\Models\Timeline|null $timeline
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Alert\Histories\AlertHistory[] $alertHistories
 * @property-read int|null $alert_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Task\Histories\TaskHistory[] $taskHistories
 * @property-read int|null $task_histories_count
 * @method static \AdvisingApp\Prospect\Database\Factories\ProspectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereAddress3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereAssignedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereEmail2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereEmailBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereHsgrad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect wherePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect wherePreferred($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereSmsOptOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspect {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $full
 * @property-read \AdvisingApp\Prospect\Models\Prospect|null $prospect
 * @method static \AdvisingApp\Prospect\Database\Factories\ProspectAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectAddress query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspectAddress {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Prospect\Models\Prospect|null $prospect
 * @method static \AdvisingApp\Prospect\Database\Factories\ProspectEmailAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectEmailAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectEmailAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectEmailAddress query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspectEmailAddress {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Prospect\Models\Prospect|null $prospect
 * @method static \AdvisingApp\Prospect\Database\Factories\ProspectPhoneNumberFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectPhoneNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectPhoneNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectPhoneNumber query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspectPhoneNumber {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static \AdvisingApp\Prospect\Database\Factories\ProspectSourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspectSource {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * 
 *
 * @property string $id
 * @property \AdvisingApp\Prospect\Enums\SystemProspectClassification $classification
 * @property string $name
 * @property \AdvisingApp\Prospect\Enums\ProspectStatusColorOptions $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static \AdvisingApp\Prospect\Database\Factories\ProspectStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspectStatus {}
}

namespace AdvisingApp\Report\Models{
/**
 * 
 *
 * @property \AdvisingApp\Report\Enums\ReportModel $model
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Report\Database\Factories\ReportFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperReport {}
}

namespace AdvisingApp\Report\Models{
/**
 * 
 *
 * @property \AdvisingApp\Report\Enums\TrackedEventType $type
 * @property-read \Illuminate\Database\Eloquent\Model $relatedTo
 * @method static \AdvisingApp\Report\Database\Factories\TrackedEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrackedEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrackedEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrackedEvent query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTrackedEvent {}
}

namespace AdvisingApp\Report\Models{
/**
 * 
 *
 * @property \AdvisingApp\Report\Enums\TrackedEventType $type
 * @property-read \Illuminate\Database\Eloquent\Model $relatedTo
 * @method static \AdvisingApp\Report\Database\Factories\TrackedEventCountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrackedEventCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrackedEventCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrackedEventCount query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTrackedEventCount {}
}

namespace AdvisingApp\Research\Models{
/**
 * 
 *
 * @property \AdvisingApp\Ai\Enums\AiModel $research_model
 * @property-read \AdvisingApp\Research\Models\ResearchRequestFolder|null $folder
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Research\Models\ResearchRequestParsedFile> $parsedFiles
 * @property-read int|null $parsed_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Research\Models\ResearchRequestParsedLink> $parsedLinks
 * @property-read int|null $parsed_links_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Research\Models\ResearchRequestParsedSearchResults> $parsedSearchResults
 * @property-read int|null $parsed_search_results_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Research\Models\ResearchRequestQuestion> $questions
 * @property-read int|null $questions_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Research\Database\Factories\ResearchRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequest query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequest {}
}

namespace AdvisingApp\Research\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Research\Models\ResearchRequest> $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestFolder {}
}

namespace AdvisingApp\Research\Models{
/**
 * 
 *
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media|null $media
 * @property-read \AdvisingApp\Research\Models\ResearchRequest|null $researchRequest
 * @method static \AdvisingApp\Research\Database\Factories\ResearchRequestParsedFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestParsedFile {}
}

namespace AdvisingApp\Research\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Research\Models\ResearchRequest|null $researchRequest
 * @method static \AdvisingApp\Research\Database\Factories\ResearchRequestParsedLinkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestParsedLink {}
}

namespace AdvisingApp\Research\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Research\Models\ResearchRequest|null $researchRequest
 * @method static \AdvisingApp\Research\Database\Factories\ResearchRequestParsedSearchResultsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestParsedSearchResults {}
}

namespace AdvisingApp\Research\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Research\Models\ResearchRequest|null $researchRequest
 * @method static \AdvisingApp\Research\Database\Factories\ResearchRequestQuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestQuestion query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestQuestion {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Division\Models\Division> $division
 * @property-read int|null $division_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubQuality|null $quality
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubStatus|null $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticleUpvote> $upvotes
 * @property-read int|null $upvotes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticleView> $views
 * @property-read int|null $views_count
 * @method static \AdvisingApp\ResourceHub\Database\Factories\ResourceHubArticleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubArticle {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubArticle|null $resourceHubArticle
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleUpvote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleUpvote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleUpvote query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubArticleUpvote {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubArticle|null $resourceHubArticle
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleView query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubArticleView {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticle> $resourceHubArticles
 * @property-read int|null $resource_hub_articles_count
 * @method static \AdvisingApp\ResourceHub\Database\Factories\ResourceHubCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubCategory {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticle> $resourceHubArticles
 * @property-read int|null $resource_hub_articles_count
 * @method static \AdvisingApp\ResourceHub\Database\Factories\ResourceHubQualityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubQuality {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticle> $resourceHubArticles
 * @property-read int|null $resource_hub_articles_count
 * @method static \AdvisingApp\ResourceHub\Database\Factories\ResourceHubStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubStatus {}
}

namespace AdvisingApp\Segment\Models{
/**
 * 
 *
 * @property \AdvisingApp\Segment\Enums\SegmentModel $model
 * @property \AdvisingApp\Segment\Enums\SegmentType $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Campaign\Models\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Segment\Models\SegmentSubject> $subjects
 * @property-read int|null $subjects_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Segment\Database\Factories\SegmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Segment model(\AdvisingApp\Segment\Enums\SegmentModel $model)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Segment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Segment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Segment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Segment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Segment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Segment withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSegment {}
}

namespace AdvisingApp\Segment\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Segment\Models\Segment|null $segment
 * @property-read \Illuminate\Database\Eloquent\Model $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentSubject onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentSubject withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentSubject withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSegmentSubject {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEnrollment {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\ProgramFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProgram {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * 
 *
 * @property string $display_name
 * @property string $mobile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentAddress> $additionalAddresses
 * @property-read int|null $additional_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentEmailAddress> $additionalEmailAddresses
 * @property-read int|null $additional_email_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentPhoneNumber> $additionalPhoneNumbers
 * @property-read int|null $additional_phone_numbers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentAddress> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Alert\Models\Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $applicationSubmissions
 * @property-read int|null $application_submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\BasicNeeds\Models\BasicNeedsProgram> $basicNeedsPrograms
 * @property-read int|null $basic_needs_programs_count
 * @property-read \App\Models\Taggable|\AdvisingApp\Notification\Models\Subscription|\AdvisingApp\Engagement\Models\EngagementFileEntities|\AdvisingApp\CareTeam\Models\CareTeam|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $careTeam
 * @property-read int|null $care_team_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseModel> $cases
 * @property-read int|null $cases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentEmailAddress> $emailAddresses
 * @property-read int|null $email_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventAttendee> $eventAttendeeRecords
 * @property-read int|null $event_attendee_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormSubmission> $formSubmissions
 * @property-read int|null $form_submissions_count
 * @property-read string|null $full_address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementResponse> $orderedEngagementResponses
 * @property-read int|null $ordered_engagement_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\Engagement> $orderedEngagements
 * @property-read int|null $ordered_engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $orderedInteractions
 * @property-read int|null $ordered_interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentPhoneNumber> $phoneNumbers
 * @property-read int|null $phone_numbers_count
 * @property-read \AdvisingApp\StudentDataModel\Models\StudentAddress|null $primaryAddress
 * @property-read \AdvisingApp\StudentDataModel\Models\StudentEmailAddress|null $primaryEmailAddress
 * @property-read \AdvisingApp\StudentDataModel\Models\StudentPhoneNumber|null $primaryPhoneNumber
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Program> $programs
 * @property-read int|null $programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Segment\Models\SegmentSubject> $segmentSubjects
 * @property-read int|null $segment_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $subscribedUsers
 * @property-read int|null $subscribed_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Task\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \AdvisingApp\Timeline\Models\Timeline|null $timeline
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Alert\Histories\AlertHistory[] $alertHistories
 * @property-read int|null $alert_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AdvisingApp\Task\Histories\TaskHistory[] $taskHistories
 * @property-read int|null $task_histories_count
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudent {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $full
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\StudentAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentAddress query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudentAddress {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * 
 *
 * @property-read \App\Models\Import|null $addressesImport
 * @property-read \App\Models\Import|null $emailAddressesImport
 * @property-read \App\Models\Import|null $enrollmentsImport
 * @property-read \App\Models\Import|null $phoneNumbersImport
 * @property-read \App\Models\Import|null $programsImport
 * @property-read \App\Models\Import|null $studentsImport
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentDataImport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentDataImport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentDataImport query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudentDataImport {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\StudentEmailAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEmailAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEmailAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentEmailAddress query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudentEmailAddress {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\StudentPhoneNumberFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPhoneNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPhoneNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentPhoneNumber query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudentPhoneNumber {}
}

namespace AdvisingApp\Survey\Models{
/**
 * 
 *
 * @property \AdvisingApp\Form\Enums\Rounding $rounding
 * @property-read mixed $allowed_domains
 * @property-read mixed $content
 * @property-read mixed $embed_enabled
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveyField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $is_wizard
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveyStep> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveySubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \AdvisingApp\Survey\Database\Factories\SurveyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Survey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Survey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Survey query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSurvey {}
}

namespace AdvisingApp\Survey\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $author
 * @property-read \AdvisingApp\Survey\Models\Survey|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyAuthentication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSurveyAuthentication {}
}

namespace AdvisingApp\Survey\Models{
/**
 * 
 *
 * @property-read mixed $config
 * @property-read mixed $is_required
 * @property-read mixed $label
 * @property-read \AdvisingApp\Survey\Models\SurveyStep|null $step
 * @property-read \AdvisingApp\Survey\Models\Survey|null $submissible
 * @property-read \AdvisingApp\Survey\Models\SurveyFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveySubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read mixed $type
 * @method static \AdvisingApp\Survey\Database\Factories\SurveyFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSurveyField {}
}

namespace AdvisingApp\Survey\Models{
/**
 * 
 *
 * @property-read \AdvisingApp\Survey\Models\SurveyField|null $field
 * @property-read \AdvisingApp\Survey\Models\SurveySubmission|null $submission
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyFieldSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyFieldSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyFieldSubmission query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSurveyFieldSubmission {}
}

namespace AdvisingApp\Survey\Models{
/**
 * 
 *
 * @property-read mixed $content
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveyField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $label
 * @property-read \AdvisingApp\Survey\Models\Survey|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSurveyStep {}
}

namespace AdvisingApp\Survey\Models{
/**
 * 
 *
 * @property Student|Prospect|null $author
 * @property \AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod $request_method
 * @property-read \AdvisingApp\Survey\Models\SurveyFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveyField> $fields
 * @property-read int|null $fields_count
 * @property-read \App\Models\User|null $requester
 * @property-read \AdvisingApp\Survey\Models\Survey|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission canceled()
 * @method static \AdvisingApp\Survey\Database\Factories\SurveySubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission notSubmitted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission requested()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveySubmission submitted()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSurveySubmission {}
}

namespace AdvisingApp\Task\Models{
/**
 * 
 *
 * @property-read Student|Prospect $concern
 * @property \AdvisingApp\Task\Enums\TaskStatus $status
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Task\Histories\TaskHistory> $histories
 * @property-read int|null $histories_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task byNextDue()
 * @method static \AdvisingApp\Task\Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTask {}
}

namespace AdvisingApp\Team\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $division_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \AdvisingApp\CaseManagement\Models\CaseTypeManager|\AdvisingApp\CaseManagement\Models\CaseTypeAuditor|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseType> $auditableCaseTypes
 * @property-read int|null $auditable_case_types_count
 * @property-read \AdvisingApp\Division\Models\Division|null $division
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CaseManagement\Models\CaseType> $manageableCaseTypes
 * @property-read int|null $manageable_case_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \AdvisingApp\Team\Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTeam {}
}

namespace AdvisingApp\Timeline\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model $timelineable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline forEntity(\Illuminate\Database\Eloquent\Model $entity)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTimeline {}
}

namespace AdvisingApp\Webhook\Models{
/**
 * 
 *
 * @property \AdvisingApp\Webhook\Enums\InboundWebhookSource $source
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInboundWebhook {}
}

namespace AdvisingApp\Webhook\Models{
/**
 * 
 *
 * @property string $id
 * @property \AdvisingApp\Webhook\Enums\InboundWebhookSource $source
 * @property string $event
 * @property string $url
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereUrl($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandlordInboundWebhook {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowStep> $workflowSteps
 * @property-read int|null $workflow_steps_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowTrigger|null $workflowTrigger
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflow {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowCareTeamDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereAssignedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereCloseDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereResDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCaseDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowCaseDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowEngagementEmailDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowEngagementSmsDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowEventDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowInteractionDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property \AdvisingApp\Alert\Enums\AlertSeverity $severity
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveAlertDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveAlertDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveAlertDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveAlertDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveAlertDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveAlertDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowProactiveAlertDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read covariant \Illuminate\Database\Eloquent\Model $related
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowRunStep> $workflowRunSteps
 * @property-read int|null $workflow_run_steps_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowTrigger|null $workflowTrigger
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRun {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property \AdvisingApp\Workflow\Enums\WorkflowActionType $details_type
 * @property-read \Illuminate\Database\Eloquent\Model $details
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRun|null $workflowRun
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRunStep {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRunStep|null $workflowRunStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRunStepRelated {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowTrigger|null $workflowTrigger
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRun {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read WorkflowRunStep|null $workflowRunStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRunStep {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRun|null $workflowRun
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRunStepRelated {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property string $workflow_trigger_id
 * @property string $related_id
 * @property string $related_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowRunStep> $workflowRunSteps
 * @property-read int|null $workflow_run_steps_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowTrigger $workflowTrigger
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun whereWorkflowTriggerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRun {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property \Illuminate\Support\Carbon $execute_at
 * @property \Illuminate\Support\Carbon|null $dispatched_at
 * @property \Illuminate\Support\Carbon|null $succeeded_at
 * @property \Illuminate\Support\Carbon|null $last_failed_at
 * @property string $workflow_run_id
 * @property string $details_type
 * @property string $details_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $details
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRun $workflowRun
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereDetailsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereDispatchedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereExecuteAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereLastFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereSucceededAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep whereWorkflowRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRunStep {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property string $workflow_run_step_id
 * @property string $related_type
 * @property string $related_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRunStep $workflowRunStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated whereWorkflowRunStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowRunStepRelated {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property string $current_details_type
 * @property string $current_details_id
 * @property int $delay_minutes
 * @property string $workflow_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $previous_step_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model $currentDetails
 * @property-read WorkflowStep|null $previousWorkflowStep
 * @property-read \AdvisingApp\Workflow\Models\Workflow|null $workflow
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowStep {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowSubscriptionDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowTagsDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowTaskDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * 
 *
 * @property \AdvisingApp\Workflow\Enums\WorkflowTriggerType $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model $createdBy
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @property-read \AdvisingApp\Workflow\Models\Workflow|null $workflow
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRun|null $workflowRun
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowTrigger {}
}

