<?php

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
 * @property string $id
 * @property int|null $completed_at
 * @property string $file_disk
 * @property string|null $file_name
 * @property string $exporter
 * @property int $processed_rows
 * @property int $total_rows
 * @property int $successful_rows
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperExport {}
}

namespace App\Models{
/**
 * @property string $id
 * @property array<array-key, mixed> $data
 * @property string $import_id
 * @property string|null $validation_error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Import|null $import
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFailedImportRow {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $check_name
 * @property string $check_label
 * @property string $status
 * @property string|null $notification_message
 * @property string|null $short_summary
 * @property array<array-key, mixed> $meta
 * @property string $ended_at
 * @property string $batch
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property string $id
 * @property int|null $completed_at
 * @property string $file_name
 * @property string $file_path
 * @property string $importer
 * @property int $processed_rows
 * @property int $total_rows
 * @property int $successful_rows
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FailedImportRow> $failedRows
 * @property-read int|null $failed_rows_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperImport {}
}

namespace App\Models{
/**
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandlordSettingsProperty {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $model_type
 * @property string $model_id
 * @property string|null $uuid
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property string|null $conversions_disk
 * @property int $size
 * @property array<array-key, mixed> $manipulations
 * @property array<array-key, mixed> $custom_properties
 * @property array<array-key, mixed> $generated_conversions
 * @property array<array-key, mixed> $responsive_images
 * @property int|null $order_column
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $created_by_type
 * @property string|null $created_by_id
 * @property-read \Illuminate\Database\Eloquent\Model|null $createdBy
 * @property-read mixed $extension
 * @property-read string $created_by_name
 * @property-read string|null $created_by_sub_label
 * @property-read mixed $human_readable_size
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @property-read mixed $original_url
 * @property-read mixed $preview_url
 * @property-read mixed $type
 * @method static \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, static> all($columns = ['*'])
 * @method static \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMedia {}
}

namespace App\Models{
/**
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMonitoredScheduledTask {}
}

namespace App\Models{
/**
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMonitoredScheduledTaskLogItem {}
}

namespace App\Models{
/**
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPronouns {}
}

namespace App\Models{
/**
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSettingsProperty {}
}

namespace App\Models{
/**
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withTrashed(bool $withTrashed = true)
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
 * @property string $id
 * @property string $name
 * @property \App\Enums\TagType $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTag {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $tag_id
 * @property string $taggable_id
 * @property string $taggable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property TenantConfig $config
 * @property string $id
 * @property string $name
 * @property string $domain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $setup_complete
 * @property \App\Enums\SubscriptionStatus $subscription_status
 * @method static \Spatie\Multitenancy\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Spatie\Multitenancy\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTenant {}
}

namespace App\Models{
/**
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
 * @property string|null $job_title
 * @property string|null $pronouns_id
 * @property bool $are_pronouns_visible_on_profile
 * @property bool $default_assistant_chat_folders_created
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Carbon\CarbonImmutable|null $last_chat_ping_at
 * @property string|null $multifactor_secret
 * @property string|null $multifactor_recovery_codes
 * @property string|null $multifactor_confirmed_at
 * @property bool $is_branding_bar_dismissed
 * @property \Illuminate\Support\Carbon|null $first_login_at
 * @property \Illuminate\Support\Carbon|null $last_logged_in_at
 * @property array<array-key, mixed>|null $password_history
 * @property \Illuminate\Support\Carbon $password_last_updated_at
 * @property bool $is_signature_enabled
 * @property array<array-key, mixed>|null $signature
 * @property string|null $team_id
 * @property bool $is_submit_ai_chat_on_enter_enabled
 * @property bool $is_action_center_update_enabled
 * @property \App\Enums\RetentionCrmRestriction|null $retention_crm_restriction
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
 * @property-read \AdvisingApp\Notification\Models\Subscription|\AdvisingApp\CareTeam\Models\CareTeam|\AdvisingApp\ResourceHub\Models\ManagerResourceHubArticle|\AdvisingApp\Consent\Models\UserConsentAgreement|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Consent\Models\ConsentAgreement> $consentAgreements
 * @property-read int|null $consent_agreements_count
 * @property-read \AdvisingApp\Team\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementBatch> $engagementBatches
 * @property-read int|null $engagement_batches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\CalendarEvent> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Group\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\License> $licenses
 * @property-read int|null $licenses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Report\Models\TrackedEvent> $logins
 * @property-read int|null $logins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Report\Models\TrackedEventCount> $loginsCount
 * @property-read int|null $logins_count_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticle> $managedResourceHubArticles
 * @property-read int|null $managed_resource_hub_articles_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \AdvisingApp\MeetingCenter\Models\PersonalBookingPage|null $personalBookingPage
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Student> $studentCareTeams
 * @property-read int|null $student_care_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Student> $studentSubscriptions
 * @property-read int|null $student_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Concern\Models\Concern> $studentConcerns
 * @property-read int|null $student_concerns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Concern\Models\Concern> $prospectConcerns
 * @property-read int|null $prospect_concerns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Permission> $permissionsFromRoles
 * @property-read int|null $permissions_from_roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User advancedFilter(array $data)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Settings\SettingsProperties{
/**
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstitutionDetailsSettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstitutionDetailsSettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstitutionDetailsSettingsProperty query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInstitutionDetailsSettingsProperty {}
}

namespace App\Settings\SettingsProperties{
/**
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingsProperty query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNotificationSettingsProperty {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string|null $assistant_id
 * @property string $name
 * @property string|null $description
 * @property string|null $instructions
 * @property string|null $knowledge
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \AdvisingApp\Ai\Enums\AiAssistantApplication $application
 * @property bool $is_default
 * @property \AdvisingApp\Ai\Enums\AiModel $model
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property bool $is_confidential
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property bool $has_resource_hub_knowledge
 * @property \AdvisingApp\Ai\Enums\EmployeeAdvisorResourceHubArticleAccess|null $resource_hub_article_access
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\EmployeeAdvisorCategory> $categories
 * @property-read int|null $categories_count
 * @property-read \AdvisingApp\Ai\Models\EmployeeAdvisorResourceHubCategory|\AdvisingApp\Ai\Models\AiAssistantConfidentialUser|\AdvisingApp\Ai\Models\AiAssistantConfidentialDepartment|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Department> $confidentialAccessDepartments
 * @property-read int|null $confidential_access_departments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $confidentialAccessUsers
 * @property-read int|null $confidential_access_users_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiAssistantFile> $files
 * @property-read int|null $files_count
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiAssistantLink> $links
 * @property-read int|null $links_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\EmployeeAdvisorQuestion> $questions
 * @property-read int|null $questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubCategory> $resourceHubCategories
 * @property-read int|null $resource_hub_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiThread> $threads
 * @property-read int|null $threads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiAssistantUpvote> $upvotes
 * @property-read int|null $upvotes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiAssistantUse> $uses
 * @property-read int|null $uses_count
 * @method static \AdvisingApp\Ai\Database\Factories\AiAssistantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistant withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistant {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $ai_assistant_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \AdvisingApp\Team\Models\Department $department
 * @method static \AdvisingApp\Ai\Database\Factories\AiAssistantConfidentialDepartmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantConfidentialDepartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantConfidentialDepartment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantConfidentialDepartment query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistantConfidentialDepartment {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Ai\Database\Factories\AiAssistantConfidentialUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantConfidentialUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantConfidentialUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantConfidentialUser query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistantConfidentialUser {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $assistant_id
 * @property string|null $file_id
 * @property string|null $name
 * @property string|null $temporary_url
 * @property string|null $mime_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $parsing_results
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore> $openAiVectorStores
 * @property-read int|null $open_ai_vector_stores_count
 * @method static \AdvisingApp\Ai\Database\Factories\AiAssistantFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantFile withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistantFile {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $ai_assistant_id
 * @property string $url
 * @property string|null $parsing_results
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore> $openAiVectorStores
 * @property-read int|null $open_ai_vector_stores_count
 * @method static \AdvisingApp\Ai\Database\Factories\AiAssistantLinkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantLink onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantLink withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantLink withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistantLink {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $assistant_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUpvote withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistantUpvote {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $assistant_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Ai\Database\Factories\AiAssistantUseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiAssistantUse query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiAssistantUse {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string|null $message_id
 * @property string $content
 * @property string|null $context
 * @property array<array-key, mixed>|null $request
 * @property string $thread_id
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $prompt_id
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiMessage {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string|null $message_id
 * @property string|null $file_id
 * @property string|null $name
 * @property string|null $temporary_url
 * @property string|null $mime_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $parsing_results
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\Ai\Models\AiMessage|null $message
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore> $openAiVectorStores
 * @property-read int|null $open_ai_vector_stores_count
 * @method static \AdvisingApp\Ai\Database\Factories\AiMessageFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessageFile withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiMessageFile {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string|null $thread_id
 * @property string|null $name
 * @property string $assistant_id
 * @property string|null $folder_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $locked_at
 * @property \Illuminate\Support\Carbon|null $saved_at
 * @property int $cloned_count
 * @property int $emailed_count
 * @property \AdvisingApp\Ai\Enums\AiThreadLockedReason|null $locked_reason
 * @property bool $is_preview
 * @property \Illuminate\Support\Carbon|null $named_by_user_at
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $assistant
 * @property-read \AdvisingApp\Ai\Models\AiThreadFolder|null $folder
 * @property-read \Carbon\CarbonInterface|null $last_engaged_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThread withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThread withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiThread {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $name
 * @property \AdvisingApp\Ai\Enums\AiAssistantApplication $application
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\AiThread> $threads
 * @property-read int|null $threads_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Ai\Database\Factories\AiThreadFolderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiThreadFolder withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAiThreadFolder {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $prompt_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Ai\Models\Prompt|null $prompt
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfidentialPromptUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfidentialPromptUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfidentialPromptUser query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConfidentialPromptUser {}
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
 * @property bool $is_requires_authentication_enabled
 * @property bool $is_generate_prospects_enabled
 * @property bool $is_introductory_message_enabled
 * @property bool $is_introductory_message_dynamic
 * @property string|null $introductory_message
 * @property string|null $title_text_color
 * @property string|null $description_text_color
 * @property string|null $button_text_color
 * @property string|null $button_text_hover_color
 * @property string|null $button_background_color
 * @property string|null $button_background_hover_color
 * @property string $default_theme
 * @property bool $has_resource_hub_knowledge
 * @property \AdvisingApp\Ai\Enums\EmployeeAdvisorResourceHubArticleAccess|null $resource_hub_article_access
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\CustomerAdvisorCategory> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\CustomerAdvisorFile> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\CustomerAdvisorLink> $links
 * @property-read int|null $links_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\CustomerAdvisorQuestion> $questions
 * @property-read int|null $questions_count
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisorResourceHubCategory|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubCategory> $resourceHubCategories
 * @property-read int|null $resource_hub_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\CustomerAdvisorThread> $threads
 * @property-read int|null $threads_count
 * @method static \AdvisingApp\Ai\Database\Factories\CustomerAdvisorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisor withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisor withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomerAdvisor {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $customer_advisor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisor|null $customerAdvisor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\CustomerAdvisorQuestion> $questions
 * @property-read int|null $questions_count
 * @method static \AdvisingApp\Ai\Database\Factories\CustomerAdvisorCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomerAdvisorCategory {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $advisor_id
 * @property string|null $file_id
 * @property string|null $name
 * @property string|null $temporary_url
 * @property string|null $mime_type
 * @property string|null $parsing_results
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisor|null $advisor
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore> $openAiVectorStores
 * @property-read int|null $open_ai_vector_stores_count
 * @method static \AdvisingApp\Ai\Database\Factories\CustomerAdvisorFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorFile withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorFile withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomerAdvisorFile {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $advisor_id
 * @property string $url
 * @property string|null $parsing_results
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_keep_current_enabled
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisor|null $advisor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore> $openAiVectorStores
 * @property-read int|null $open_ai_vector_stores_count
 * @method static \AdvisingApp\Ai\Database\Factories\CustomerAdvisorLinkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorLink onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorLink withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorLink withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomerAdvisorLink {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $thread_id
 * @property string|null $author_type
 * @property string|null $author_id
 * @property string|null $message_id
 * @property string $content
 * @property string|null $context
 * @property array<array-key, mixed>|null $request
 * @property array<array-key, mixed>|null $next_request_options
 * @property bool $is_advisor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|null $author
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisorThread $thread
 * @method static \AdvisingApp\Ai\Database\Factories\CustomerAdvisorMessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorMessage query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomerAdvisorMessage {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $question
 * @property string $answer
 * @property string $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisorCategory|null $category
 * @method static \AdvisingApp\Ai\Database\Factories\CustomerAdvisorQuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorQuestion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorQuestion withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomerAdvisorQuestion {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisor|null $customerAdvisor
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubCategory|null $resourceHubCategory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorResourceHubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorResourceHubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorResourceHubCategory query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomerAdvisorResourceHubCategory {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $advisor_id
 * @property string|null $author_type
 * @property string|null $author_id
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $interaction_id
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisor|null $advisor
 * @property-read \Illuminate\Database\Eloquent\Model|null $author
 * @property-read \AdvisingApp\Interaction\Models\Interaction|null $interaction
 * @property-read \AdvisingApp\Ai\Models\CustomerAdvisorMessage|null $latestMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\CustomerAdvisorMessage> $messages
 * @property-read int|null $messages_count
 * @method static \AdvisingApp\Ai\Database\Factories\CustomerAdvisorThreadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorThread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorThread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerAdvisorThread query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCustomerAdvisorThread {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAdvisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAdvisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAdvisor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAdvisor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAdvisor withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataAdvisor withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDataAdvisor {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $prompt_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Team\Models\Department $department
 * @property-read \AdvisingApp\Ai\Models\Prompt|null $prompt
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentConfidentialPrompt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentConfidentialPrompt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentConfidentialPrompt query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepartmentConfidentialPrompt {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $employee_advisor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $employeeAdvisor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\EmployeeAdvisorQuestion> $questions
 * @property-read int|null $questions_count
 * @method static \AdvisingApp\Ai\Database\Factories\EmployeeAdvisorCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmployeeAdvisorCategory {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $question
 * @property string $answer
 * @property string $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Ai\Models\EmployeeAdvisorCategory|null $category
 * @method static \AdvisingApp\Ai\Database\Factories\EmployeeAdvisorQuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorQuestion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorQuestion withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmployeeAdvisorQuestion {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property-read \AdvisingApp\Ai\Models\AiAssistant|null $aiAssistant
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubCategory|null $resourceHubCategory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorResourceHubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorResourceHubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeAdvisorResourceHubCategory query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmployeeAdvisorResourceHubCategory {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $message
 * @property array<array-key, mixed> $metadata
 * @property string $user_id
 * @property array<array-key, mixed> $request
 * @property \Illuminate\Support\Carbon $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $ai_assistant_name
 * @property \AdvisingApp\Ai\Enums\AiMessageLogFeature|null $feature
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
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property string $prompt
 * @property string $type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $user_id
 * @property bool $is_smart
 * @property bool $is_confidential
 * @property-read \AdvisingApp\Ai\Models\ConfidentialPromptUser|\AdvisingApp\Ai\Models\DepartmentConfidentialPrompt|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Department> $confidentialAccessDepartments
 * @property-read int|null $confidential_access_departments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $confidentialAccessUsers
 * @property-read int|null $confidential_access_users_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prompt withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prompt withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPrompt {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Ai\Models\Prompt> $prompts
 * @property-read int|null $prompts_count
 * @method static \AdvisingApp\Ai\Database\Factories\PromptTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPromptType {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $prompt_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Ai\Models\Prompt|null $prompt
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUpvote withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPromptUpvote {}
}

namespace AdvisingApp\Ai\Models{
/**
 * @property string $id
 * @property string $prompt_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Ai\Models\Prompt|null $prompt
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Ai\Database\Factories\PromptUseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptUse withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPromptUse {}
}

namespace AdvisingApp\Alert\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Alert\Presets\AlertPreset $preset
 * @property bool $is_enabled
 * @property string|null $configuration_type
 * @property string|null $configuration_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model|null $configuration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Alert\Models\StudentAlert> $studentAlerts
 * @property-read int|null $student_alerts_count
 * @method static \AdvisingApp\Alert\Database\Factories\AlertConfigurationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlertConfiguration query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAlertConfiguration {}
}

namespace AdvisingApp\Alert\Models{
/**
 * @property string|null $sisid
 * @property string|null $alert_configuration_id
 * @property-read \AdvisingApp\Alert\Models\AlertConfiguration|null $alertConfiguration
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentAlert query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudentAlert {}
}

namespace AdvisingApp\Application\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $embed_enabled
 * @property array<array-key, mixed>|null $allowed_domains
 * @property string|null $primary_color
 * @property \AdvisingApp\Form\Enums\Rounding|null $rounding
 * @property bool $is_wizard
 * @property array<array-key, mixed>|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property bool $should_generate_prospects
 * @property string|null $title
 * @property \App\Enums\FontWeight|null $title_font_weight
 * @property string|null $title_color
 * @property bool $notify_to_care_team
 * @property bool $notify_to_subscribers
 * @property bool $notify_via_app
 * @property bool $notify_via_email
 * @property bool $allow_view_past_submissions
 * @property string $root_id
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\Application\Models\ApplicationNotificationUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $notificationUsers
 * @property-read int|null $notification_users_count
 * @property-read Application $rootApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationStep> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Application> $versions
 * @property-read int|null $versions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowTrigger> $workflowTriggers
 * @property-read int|null $workflow_triggers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\Workflow> $workflows
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
 * @property string $id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $code
 * @property string $application_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $author
 * @property-read \AdvisingApp\Application\Models\Application $submissible
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
 * @property ApplicationFieldSubmission $pivot
 * @property string $id
 * @property string $label
 * @property string $type
 * @property bool $is_required
 * @property array<array-key, mixed> $config
 * @property string $application_id
 * @property string|null $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Application\Models\ApplicationStep|null $step
 * @property-read \AdvisingApp\Application\Models\Application $submissible
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \AdvisingApp\Application\Database\Factories\ApplicationFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationField {}
}

namespace AdvisingApp\Application\Models{
/**
 * @property string $id
 * @property array<array-key, mixed> $response
 * @property string $field_id
 * @property string $submission_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationFieldSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationFieldSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationFieldSubmission query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationFieldSubmission {}
}

namespace AdvisingApp\Application\Models{
/**
 * @property-read \AdvisingApp\Application\Models\Application|null $application
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationNotificationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationNotificationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationNotificationUser query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationNotificationUser {}
}

namespace AdvisingApp\Application\Models{
/**
 * @property string $id
 * @property string $label
 * @property array<array-key, mixed>|null $content
 * @property string $application_id
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\Application\Models\Application $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStep withTrashed(bool $withTrashed = true)
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
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property \Carbon\CarbonImmutable|null $submitted_at
 * @property \Carbon\CarbonImmutable|null $canceled_at
 * @property \AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod|null $request_method
 * @property string|null $request_note
 * @property string|null $requester_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmissionsChecklistItem> $checklistItems
 * @property-read int|null $checklist_items_count
 * @property-read \AdvisingApp\Application\Models\ApplicationFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read \App\Models\User|null $requester
 * @property-read \AdvisingApp\Application\Models\ApplicationSubmissionState|null $state
 * @property-read \AdvisingApp\Application\Models\Application $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission canceled()
 * @method static \AdvisingApp\Application\Database\Factories\ApplicationSubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission notSubmitted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission submitted()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationSubmission {}
}

namespace AdvisingApp\Application\Models{
/**
 * @property bool $is_default
 * @property string $id
 * @property \AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification $classification
 * @property string $name
 * @property \CanyonGBS\Common\Enums\Color $color
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowTrigger> $workflowTriggers
 * @property-read int|null $workflow_triggers_count
 * @method static \AdvisingApp\Application\Database\Factories\ApplicationSubmissionStateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmissionState withTrashed(bool $withTrashed = true)
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApplicationSubmissionsChecklistItem {}
}

namespace AdvisingApp\Audit\Models{
/**
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
 * @property string|null $change_agent_name
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $user
 * @method static \AdvisingApp\Audit\Database\Factories\AuditFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAudit {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property \AdvisingApp\Authorization\Enums\LicenseType $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLicense {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * @property string $id
 * @property string $code
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Authorization\Database\Factories\OneTimeLoginCodeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OneTimeLoginCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OneTimeLoginCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OneTimeLoginCode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OneTimeLoginCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OneTimeLoginCode withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OneTimeLoginCode withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOneTimeLoginCode {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $group_id
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermission {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermissionGroup {}
}

namespace AdvisingApp\Authorization\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $description
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRole {}
}

namespace AdvisingApp\BasicNeeds\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\BasicNeeds\Models\BasicNeedsProgram> $basicNeedsProgram
 * @property-read int|null $basic_needs_program_count
 * @method static \AdvisingApp\BasicNeeds\Database\Factories\BasicNeedsCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBasicNeedsCategory {}
}

namespace AdvisingApp\BasicNeeds\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $basic_needs_category_id
 * @property string|null $contact_person
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $location
 * @property string|null $availability
 * @property string|null $eligibility_criteria
 * @property string|null $application_process
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\BasicNeeds\Models\BasicNeedsCategory|null $basicNeedsCategories
 * @method static \AdvisingApp\BasicNeeds\Database\Factories\BasicNeedsProgramFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BasicNeedsProgram withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBasicNeedsProgram {}
}

namespace AdvisingApp\Campaign\Models{
/**
 * @property string $id
 * @property string $name
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $segment_id
 * @property string $created_by_type
 * @property string $created_by_id
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Campaign\Models\CampaignAction> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model $createdBy
 * @property-read \AdvisingApp\Group\Models\Group|null $group
 * @method static \AdvisingApp\Campaign\Database\Factories\CampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign hasNotBeenExecuted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCampaign {}
}

namespace AdvisingApp\Campaign\Models{
/**
 * @property string $id
 * @property string $campaign_id
 * @property \AdvisingApp\Campaign\Enums\CampaignActionType $type
 * @property array<array-key, mixed> $data
 * @property \Illuminate\Support\Carbon $execute_at
 * @property \Illuminate\Support\Carbon|null $execution_dispatched_at
 * @property \Illuminate\Support\Carbon|null $execution_finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Campaign\Models\Campaign|null $campaign
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Campaign\Models\CampaignActionEducatable> $campaignActionEducatables
 * @property-read int|null $campaign_action_educatables_count
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction campaignEnabled()
 * @method static \AdvisingApp\Campaign\Database\Factories\CampaignActionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignAction withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCampaignAction {}
}

namespace AdvisingApp\Campaign\Models{
/**
 * @property string $id
 * @property string $campaign_action_id
 * @property string $educatable_type
 * @property string $educatable_id
 * @property \Illuminate\Support\Carbon|null $succeeded_at
 * @property \Illuminate\Support\Carbon|null $last_failed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property string $id
 * @property string $campaign_action_educatable_id
 * @property string $related_id
 * @property string $related_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Campaign\Models\CampaignActionEducatable $campaignActionEducatable
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
 * @property string $care_team_role_id
 * @property Educatable $educatable
 * @property string $id
 * @property string $user_id
 * @property string $educatable_id
 * @property string $educatable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property bool $is_default
 * @property string $id
 * @property string $name
 * @property \App\Enums\CareTeamRoleType $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\CareTeam\Models\CareTeam> $careTeams
 * @property-read int|null $care_teams_count
 * @method static \AdvisingApp\CareTeam\Database\Factories\CareTeamRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CareTeamRole withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCareTeamRole {}
}

namespace AdvisingApp\Concern\Models{
/**
 * @property-read (Subscribable&(Student|Prospect))|null $concern
 * @property string $id
 * @property string $concern_type
 * @property string $concern_id
 * @property string $description
 * @property \AdvisingApp\Concern\Enums\ConcernSeverity $severity
 * @property string $suggested_intervention
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string $status_id
 * @property bool $is_visible_for_students
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Concern\Histories\ConcernHistory> $histories
 * @property-read int|null $histories_count
 * @property-read \AdvisingApp\Concern\Models\ConcernStatus|null $status
 * @method static \AdvisingApp\Concern\Database\Factories\ConcernFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concern licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concern newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concern newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concern onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concern query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concern withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concern withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConcern {}
}

namespace AdvisingApp\Concern\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Concern\Enums\SystemConcernStatusClassification $classification
 * @property string $name
 * @property int $order
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Concern\Models\Concern> $concerns
 * @property-read int|null $concerns_count
 * @method static \AdvisingApp\Concern\Database\Factories\ConcernStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernStatus withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConcernStatus {}
}

namespace AdvisingApp\Consent\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Consent\Enums\ConsentAgreementType $type
 * @property string $title
 * @property string $description
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
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
 * @property string $id
 * @property string $user_id
 * @property string $consent_agreement_id
 * @property string $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Consent\Models\ConsentAgreement $consentAgreement
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsentAgreement withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserConsentAgreement {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property array<array-key, mixed>|null $content
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Engagement\Database\Factories\EmailTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailTemplate {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property-read ?Educatable $recipient
 * @property string $id
 * @property string|null $user_id
 * @property string|null $engagement_batch_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property array<array-key, mixed>|null $subject
 * @property array<array-key, mixed>|null $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $channel
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property \Illuminate\Support\Carbon|null $dispatched_at
 * @property string|null $recipient_route
 * @property string|null $source_id
 * @property \Illuminate\Support\Carbon|null $dispatch_failed_at
 * @property \AdvisingApp\Notification\Enums\EmailType $email_type
 * @property string|null $source_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Engagement\Models\EngagementBatch|null $batch
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\EmailMessage> $emailMessages
 * @property-read int|null $email_messages_count
 * @property-read \AdvisingApp\Engagement\Models\EngagementBatch|null $engagementBatch
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \AdvisingApp\Notification\Models\EmailMessage|null $latestEmailMessage
 * @property-read \AdvisingApp\Notification\Models\SmsMessage|null $latestSmsMessage
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\SmsMessage> $smsMessages
 * @property-read int|null $sms_messages_count
 * @property-read \Illuminate\Database\Eloquent\Model|null $source
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagement {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property string $id
 * @property string|null $identifier
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \AdvisingApp\Notification\Enums\NotificationChannel|null $channel
 * @property array<array-key, mixed>|null $subject
 * @property array<array-key, mixed>|null $body
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property int|null $total_engagements
 * @property int|null $processed_engagements
 * @property int|null $successful_engagements
 * @property \AdvisingApp\Notification\Enums\EmailType $email_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Engagement\Database\Factories\EngagementBatchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementBatch {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property string $id
 * @property string $description
 * @property string|null $retention_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by_type
 * @property string|null $created_by_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model|null $createdBy
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementFile {}
}

namespace AdvisingApp\Engagement\Models{
/**
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementFileEntities {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property string $id
 * @property string|null $sender_id
 * @property string|null $sender_type
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $subject
 * @property \AdvisingApp\Engagement\Enums\EngagementResponseType $type
 * @property string|null $raw
 * @property \AdvisingApp\Engagement\Enums\EngagementResponseStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Engagement\Models\EngagementResponseActionedNote> $actionedNotes
 * @property-read int|null $actioned_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Engagement\Models\EngagementResponseActionedNote|null $latestActionedNote
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Model|null $sender
 * @property-read \AdvisingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AdvisingApp\Engagement\Database\Factories\EngagementResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse sentByProspect()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse sentByStudent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementResponse {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property string $id
 * @property string $engagement_response_id
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property string $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \AdvisingApp\Engagement\Models\EngagementResponse|null $engagementResponse
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @method static \AdvisingApp\Engagement\Database\Factories\EngagementResponseActionedNoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponseActionedNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponseActionedNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponseActionedNote query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementResponseActionedNote {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property string|null $record_type
 * @property string|null $record_id
 * @property string|null $direction
 * @property string|null $type
 * @property string|null $sent_to_type
 * @property string|null $sent_to_id
 * @property string|null $sent_by_type
 * @property string|null $sent_by_id
 * @property string|null $concern_type
 * @property string|null $concern_id
 * @property \Illuminate\Support\Carbon|null $record_sortable_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|null $concern
 * @property-read Engagement|EngagementResponse|null $record
 * @property-read covariant \AdvisingApp\StudentDataModel\Models\Student|\AdvisingApp\Prospect\Models\Prospect|\App\Models\User|null $sentBy
 * @property-read covariant \AdvisingApp\StudentDataModel\Models\Student|\AdvisingApp\Prospect\Models\Prospect|null $sentTo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HolisticEngagement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HolisticEngagement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HolisticEngagement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HolisticEngagement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HolisticEngagement withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HolisticEngagement withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHolisticEngagement {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property array<array-key, mixed>|null $content
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Engagement\Database\Factories\SmsTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTemplate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSmsTemplate {}
}

namespace AdvisingApp\Engagement\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Engagement\Enums\EngagementResponseType $type
 * @property string|null $subject
 * @property string $body
 * @property \Illuminate\Support\Carbon $occurred_at
 * @property string $sender
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
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
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $embed_enabled
 * @property array<array-key, mixed>|null $allowed_domains
 * @property string|null $primary_color
 * @property \AdvisingApp\Form\Enums\Rounding|null $rounding
 * @property bool $is_authenticated
 * @property bool $is_wizard
 * @property bool $recaptcha_enabled
 * @property array<array-key, mixed>|null $content
 * @property string|null $on_screen_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property bool $generate_prospects
 * @property string|null $title
 * @property \App\Enums\FontWeight|null $title_font_weight
 * @property string|null $title_color
 * @property bool $notify_to_care_team
 * @property bool $notify_to_subscribers
 * @property bool $notify_via_app
 * @property bool $notify_via_email
 * @property bool $allow_view_past_submissions
 * @property string $root_id
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \AdvisingApp\Form\Models\FormEmailAutoReply|null $emailAutoReply
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read \AdvisingApp\Form\Models\FormNotificationUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $notificationUsers
 * @property-read int|null $notification_users_count
 * @property-read Form $rootForm
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormStep> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Form> $versions
 * @property-read int|null $versions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowTrigger> $workflowTriggers
 * @property-read int|null $workflow_triggers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\Workflow> $workflows
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
 * @property string $id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $code
 * @property string $form_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $author
 * @property-read \AdvisingApp\Form\Models\Form $submissible
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
 * @property string $id
 * @property array<array-key, mixed>|null $subject
 * @property array<array-key, mixed>|null $body
 * @property bool $is_enabled
 * @property string $form_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Form\Models\Form $form
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormEmailAutoReply withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormEmailAutoReply {}
}

namespace AdvisingApp\Form\Models{
/**
 * @property string $id
 * @property string $label
 * @property string $type
 * @property bool $is_required
 * @property array<array-key, mixed> $config
 * @property string $form_id
 * @property string|null $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Form\Models\FormStep|null $step
 * @property-read \AdvisingApp\Form\Models\Form $submissible
 * @property-read \AdvisingApp\Form\Models\FormFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \AdvisingApp\Form\Database\Factories\FormFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormField {}
}

namespace AdvisingApp\Form\Models{
/**
 * @property string $id
 * @property array<array-key, mixed> $response
 * @property string $field_id
 * @property string $submission_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Form\Models\FormField|null $field
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\Form\Models\FormSubmission $submission
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
 * @property-read \AdvisingApp\Form\Models\Form|null $form
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormNotificationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormNotificationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormNotificationUser query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormNotificationUser {}
}

namespace AdvisingApp\Form\Models{
/**
 * @property string $id
 * @property string $label
 * @property array<array-key, mixed>|null $content
 * @property string $form_id
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read \AdvisingApp\Form\Models\Form $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFormStep {}
}

namespace AdvisingApp\Form\Models{
/**
 * @property Student|Prospect|null $author
 * @property string $id
 * @property string $form_id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property \Carbon\CarbonImmutable|null $submitted_at
 * @property \Carbon\CarbonImmutable|null $canceled_at
 * @property \AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod|null $request_method
 * @property string|null $request_note
 * @property string|null $requester_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \AdvisingApp\Form\Models\FormFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read \App\Models\User|null $requester
 * @property-read \AdvisingApp\Form\Models\Form $submissible
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

namespace AdvisingApp\Group\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property array<array-key, mixed>|null $filters
 * @property \AdvisingApp\Group\Enums\GroupModel $model
 * @property \AdvisingApp\Group\Enums\GroupType $type
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Campaign\Models\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Group\Models\GroupSubject> $subjects
 * @property-read int|null $subjects_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Group\Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group model(\AdvisingApp\Group\Enums\GroupModel $model)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGroup {}
}

namespace AdvisingApp\Group\Models{
/**
 * @property string $id
 * @property string $subject_id
 * @property string $subject_type
 * @property string $segment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Group\Models\Group|null $group
 * @property-read \Illuminate\Database\Eloquent\Model $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSubject onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSubject withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSubject withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGroupSubject {}
}

namespace AdvisingApp\IntegrationOpenAi\Models{
/**
 * @property string $id
 * @property string $research_request_id
 * @property string $deployment_hash
 * @property \Carbon\CarbonImmutable|null $ready_until
 * @property string|null $vector_store_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Research\Models\ResearchRequest $researchRequest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiResearchRequestVectorStore withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOpenAiResearchRequestVectorStore {}
}

namespace AdvisingApp\IntegrationOpenAi\Models{
/**
 * @property string $id
 * @property string $file_type
 * @property string $file_id
 * @property string $deployment_hash
 * @property \Carbon\CarbonImmutable|null $ready_until
 * @property string|null $vector_store_id
 * @property string|null $vector_store_file_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $context_type
 * @property string|null $context_id
 * @property-read \Illuminate\Database\Eloquent\Model|null $context
 * @property-read \Illuminate\Database\Eloquent\Model $file
 * @method static \AdvisingApp\IntegrationOpenAi\Database\Factories\OpenAiVectorStoreFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpenAiVectorStore withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOpenAiVectorStore {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * @property string $id
 * @property string $interaction_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Team\Models\Department $department
 * @property-read \AdvisingApp\Interaction\Models\Interaction|null $interaction
 * @method static \AdvisingApp\Interaction\Database\Factories\DepartmentConfidentialInteractionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentConfidentialInteraction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentConfidentialInteraction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentConfidentialInteraction query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepartmentConfidentialInteraction {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * @property string $id
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $user_id
 * @property string|null $interactable_id
 * @property string|null $interactable_type
 * @property string|null $interaction_type_id
 * @property string|null $interaction_relation_id
 * @property string|null $interaction_driver_id
 * @property string|null $interaction_status_id
 * @property string|null $interaction_outcome_id
 * @property \Illuminate\Support\Carbon $start_datetime
 * @property \Illuminate\Support\Carbon|null $end_datetime
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $interaction_initiative_id
 * @property bool $is_confidential
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Interaction\Models\InteractionConfidentialUser|\AdvisingApp\Interaction\Models\DepartmentConfidentialInteraction|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Department> $confidentialAccessDepartments
 * @property-read int|null $confidential_access_departments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $confidentialAccessUsers
 * @property-read int|null $confidential_access_users_count
 * @property-read \AdvisingApp\Interaction\Models\InteractionDriver|null $driver
 * @property-read \AdvisingApp\Interaction\Models\InteractionInitiative|null $initiative
 * @property-read \Illuminate\Database\Eloquent\Model|null $interactable
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interaction withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteraction {}
}

namespace AdvisingApp\Interaction\Models{
/**
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
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_default
 * @property \AdvisingApp\Interaction\Enums\InteractableType $interactable_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionDriverFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionDriver withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionDriver {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_default
 * @property \AdvisingApp\Interaction\Enums\InteractableType $interactable_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionInitiativeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionInitiative withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionInitiative {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_default
 * @property \AdvisingApp\Interaction\Enums\InteractableType $interactable_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionOutcomeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionOutcome withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionOutcome {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_default
 * @property \AdvisingApp\Interaction\Enums\InteractableType $interactable_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionRelationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionRelation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionRelation {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * @property string $id
 * @property string $name
 * @property \CanyonGBS\Common\Enums\Color $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_default
 * @property \AdvisingApp\Interaction\Enums\InteractableType $interactable_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionStatus {}
}

namespace AdvisingApp\Interaction\Models{
/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_default
 * @property \AdvisingApp\Interaction\Enums\InteractableType $interactable_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \AdvisingApp\Interaction\Database\Factories\InteractionTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InteractionType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInteractionType {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $default_appointment_duration
 * @property bool $is_default_appointment_buffer_enabled
 * @property int $default_appointment_buffer_before_duration
 * @property int $default_appointment_buffer_after_duration
 * @property array<array-key, mixed> $available_appointment_hours
 * @property string|null $slug
 * @property \AdvisingApp\MeetingCenter\Enums\BookingGroupBookWith $book_with
 * @property string|null $meeting_owner_id
 * @property int $minimum_booking_lead_time_hours
 * @property int $maximum_booking_lead_time_days
 * @property string|null $round_robin_last_assigned_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\BookingGroupAppointment> $bookingGroupAppointments
 * @property-read int|null $booking_group_appointments_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \AdvisingApp\MeetingCenter\Models\BookingGroupUser|\AdvisingApp\MeetingCenter\Models\BookingGroupDepartment|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Department> $departments
 * @property-read int|null $departments_count
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \App\Models\User|null $meetingOwner
 * @property-read \App\Models\User|null $roundRobinLastAssignedUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\BookingGroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroup query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBookingGroup {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property string $booking_group_id
 * @property string|null $calendar_event_provider_uid
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon $starts_at
 * @property \Illuminate\Support\Carbon $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $meeting_owner_id
 * @property string|null $calendar_event_id
 * @property-read \AdvisingApp\MeetingCenter\Models\BookingGroup $bookingGroup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\CalendarEvent> $calendarEvents
 * @property-read int|null $calendar_events_count
 * @property-read \App\Models\User|null $meetingOwner
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\BookingGroupAppointmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupAppointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupAppointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupAppointment query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBookingGroupAppointment {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property string $booking_group_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\MeetingCenter\Models\BookingGroup $bookingGroup
 * @property-read \AdvisingApp\Team\Models\Department $department
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupDepartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupDepartment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupDepartment query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBookingGroupDepartment {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property string $booking_group_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\MeetingCenter\Models\BookingGroup $bookingGroup
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingGroupUser query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBookingGroupUser {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property string|null $name
 * @property \AdvisingApp\MeetingCenter\Enums\CalendarProvider $provider_type
 * @property string|null $provider_id
 * @property string $provider_email
 * @property string|null $oauth_token
 * @property string|null $oauth_refresh_token
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $oauth_token_expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property array<array-key, mixed>|null $attendees
 * @property string|null $provider_id
 * @property string $calendar_id
 * @property \Illuminate\Support\Carbon $starts_at
 * @property \Illuminate\Support\Carbon $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \AdvisingApp\MeetingCenter\Enums\EventTransparency|null $transparency
 * @property string|null $provider_uid
 * @property-read \AdvisingApp\MeetingCenter\Models\BookingGroupAppointment|null $bookingGroupAppointment
 * @property-read \AdvisingApp\MeetingCenter\Models\Calendar $calendar
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
 * @property string $id
 * @property string $title
 * @property array<array-key, mixed>|null $description
 * @property string|null $location
 * @property int|null $capacity
 * @property \Illuminate\Support\Carbon $starts_at
 * @property \Illuminate\Support\Carbon $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $created_by_id
 * @property string|null $last_updated_by_id
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventAttendee> $attendees
 * @property-read int|null $attendees_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationForm|null $eventRegistrationForm
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEvent {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property \AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus $status
 * @property string $email
 * @property string $event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $archived_at
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
 * @property string|null $root_id
 * @property string $id
 * @property string $event_id
 * @property bool $embed_enabled
 * @property array<array-key, mixed>|null $allowed_domains
 * @property string|null $primary_color
 * @property \AdvisingApp\Form\Enums\Rounding|null $rounding
 * @property bool $is_wizard
 * @property bool $recaptcha_enabled
 * @property array<array-key, mixed>|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \AdvisingApp\MeetingCenter\Models\Event|null $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormField> $fields
 * @property-read int|null $fields_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationForm withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationForm withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationForm {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property-read EventRegistrationForm $submissible
 * @property string $id
 * @property string $event_attendee_id
 * @property string|null $code
 * @property string $form_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\MeetingCenter\Models\EventAttendee $author
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
 * @property string $id
 * @property string $label
 * @property string $type
 * @property bool $is_required
 * @property array<array-key, mixed> $config
 * @property string $form_id
 * @property string|null $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep|null $step
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationForm|null $submissible
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventRegistrationFormFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationFormField {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property array<array-key, mixed> $response
 * @property string $field_id
 * @property string $submission_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationFormField|null $field
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission $submission
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormFieldSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormFieldSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormFieldSubmission query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationFormFieldSubmission {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property string $label
 * @property array<array-key, mixed>|null $content
 * @property string $form_id
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormField> $fields
 * @property-read int|null $fields_count
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationForm|null $submissible
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventRegistrationFormStepFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationFormStep {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property string $form_id
 * @property string $event_attendee_id
 * @property \AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus $attendee_status
 * @property \Carbon\CarbonImmutable|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property \Carbon\CarbonImmutable|null $canceled_at
 * @property \AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod|null $request_method
 * @property string|null $request_note
 * @property string|null $requester_id
 * @property-read \AdvisingApp\MeetingCenter\Models\EventAttendee $author
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationFormFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\MeetingCenter\Models\EventRegistrationFormField> $fields
 * @property-read int|null $fields_count
 * @property-read \App\Models\User|null $requester
 * @property-read \AdvisingApp\MeetingCenter\Models\EventRegistrationForm|null $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission canceled()
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\EventRegistrationFormSubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission notSubmitted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventRegistrationFormSubmission submitted()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEventRegistrationFormSubmission {}
}

namespace AdvisingApp\MeetingCenter\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property bool $is_enabled
 * @property int $default_appointment_duration
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $minimum_booking_lead_time_hours
 * @property int $maximum_booking_lead_time_days
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\MeetingCenter\Database\Factories\PersonalBookingPageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalBookingPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalBookingPage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalBookingPage query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPersonalBookingPage {}
}

namespace AdvisingApp\Notification\Models{
/**
 * @property string $id
 * @property string $notification_class
 * @property string|null $notification_id
 * @property array<array-key, mixed> $content
 * @property string|null $related_type
 * @property string|null $related_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|null $recipient
 * @property-read \Illuminate\Database\Eloquent\Model|null $related
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
 * @property string $id
 * @property string $notification_class
 * @property string|null $external_reference_id
 * @property array<array-key, mixed> $content
 * @property int $quota_usage
 * @property string|null $related_type
 * @property string|null $related_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $recipient_address
 * @property \AdvisingApp\Notification\Enums\EmailType $email_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\EmailMessageEvent> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Model|null $recipient
 * @property-read \Illuminate\Database\Eloquent\Model|null $related
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
 * @property string $id
 * @property string $email_message_id
 * @property \AdvisingApp\Notification\Enums\EmailMessageEventType $type
 * @property array<array-key, mixed> $payload
 * @property \Illuminate\Support\Carbon $occurred_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property string $id
 * @property string $notification_class
 * @property string|null $external_reference_id
 * @property array<array-key, mixed> $content
 * @property int $quota_usage
 * @property string|null $related_type
 * @property string|null $related_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $recipient_number
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Notification\Models\SmsMessageEvent> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Model|null $recipient
 * @property-read \Illuminate\Database\Eloquent\Model|null $related
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
 * @property string $id
 * @property string $sms_message_id
 * @property \AdvisingApp\Notification\Enums\SmsMessageEventType $type
 * @property array<array-key, mixed> $payload
 * @property \Illuminate\Support\Carbon $occurred_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property string $id
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $type
 * @property string $route
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property string $id
 * @property string $user_id
 * @property string $subscribable_id
 * @property string $subscribable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property Carbon|null $created_at
 * @property string $id
 * @property string|null $educatable_id
 * @property string|null $educatable_type
 * @property string|null $code
 * @property \AdvisingApp\Portal\Enums\PortalType|null $portal_type
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|null $educatable
 * @method static \AdvisingApp\Portal\Database\Factories\PortalAuthenticationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPortalAuthentication {}
}

namespace AdvisingApp\Portal\Models{
/**
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Portal\Models\ResourceHubArticleVote> $resourceHubArticleVotes
 * @property-read int|null $resource_hub_article_votes_count
 * @method static \AdvisingApp\Portal\Database\Factories\PortalGuestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPortalGuest {}
}

namespace AdvisingApp\Portal\Models{
/**
 * @property string $id
 * @property bool $is_helpful
 * @property string $voter_type
 * @property string $voter_id
 * @property string $article_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubArticle|null $resourceHubArticle
 * @property-read \Illuminate\Database\Eloquent\Model $voter
 * @method static \AdvisingApp\Portal\Database\Factories\ResourceHubArticleVoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleVote query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubArticleVote {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * @property string $display_name
 * @property string $id
 * @property string $status_id
 * @property string $source_id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string|null $preferred
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $birthdate
 * @property string|null $hsgrad
 * @property string|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $student_id
 * @property string|null $primary_email_id
 * @property string|null $primary_phone_id
 * @property string|null $primary_address_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectAddress> $additionalAddresses
 * @property-read int|null $additional_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectEmailAddress> $additionalEmailAddresses
 * @property-read int|null $additional_email_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectPhoneNumber> $additionalPhoneNumbers
 * @property-read int|null $additional_phone_numbers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectAddress> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $applicationSubmissions
 * @property-read int|null $application_submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\BasicNeeds\Models\BasicNeedsProgram> $basicNeedsPrograms
 * @property-read int|null $basic_needs_programs_count
 * @property-read \App\Models\Taggable|\AdvisingApp\Notification\Models\Subscription|\AdvisingApp\Engagement\Models\EngagementFileEntities|\AdvisingApp\CareTeam\Models\CareTeam|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $careTeam
 * @property-read int|null $care_team_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Concern\Models\Concern> $concerns
 * @property-read int|null $concerns_count
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
 * @property-read \AdvisingApp\Prospect\Models\ProspectSource|null $source
 * @property-read \AdvisingApp\Prospect\Models\ProspectStatus|null $status
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Concern\Histories\ConcernHistory> $concernHistories
 * @property-read int|null $concern_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Task\Histories\TaskHistory> $taskHistories
 * @property-read int|null $task_histories_count
 * @method static \AdvisingApp\Prospect\Database\Factories\ProspectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prospect withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspect {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * @property string $id
 * @property string $prospect_id
 * @property string|null $line_1
 * @property string|null $line_2
 * @property string|null $line_3
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal
 * @property string|null $country
 * @property string|null $type
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property string $id
 * @property string $prospect_id
 * @property string $address
 * @property string|null $type
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\StudentDataModel\Models\BouncedEmailAddress|null $bounced
 * @property-read \AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut|null $optedOut
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
 * @property string $id
 * @property string $prospect_id
 * @property string $number
 * @property int|null $ext
 * @property string|null $type
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber|null $bounced
 * @property-read \AdvisingApp\StudentDataModel\Models\PhoneNumberLookup|null $phoneNumberLookup
 * @property-read \AdvisingApp\Prospect\Models\Prospect|null $prospect
 * @property-read \AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber|null $smsOptOut
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectSource withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspectSource {}
}

namespace AdvisingApp\Prospect\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Prospect\Enums\SystemProspectClassification $classification
 * @property string $name
 * @property \CanyonGBS\Common\Enums\Color $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $sort
 * @property bool $is_system_protected
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static \AdvisingApp\Prospect\Database\Factories\ProspectStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProspectStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProspectStatus {}
}

namespace AdvisingApp\Report\Models{
/**
 * @property string $id
 * @property string $report_key
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Team\Models\Department $department
 * @method static \AdvisingApp\Report\Database\Factories\ReportDepartmentAccessFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportDepartmentAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportDepartmentAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportDepartmentAccess query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperReportDepartmentAccess {}
}

namespace AdvisingApp\Report\Models{
/**
 * @property string $id
 * @property string $report_key
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Report\Database\Factories\ReportUserAccessFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportUserAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportUserAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportUserAccess query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperReportUserAccess {}
}

namespace AdvisingApp\Report\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Report\Enums\TrackedEventType $type
 * @property string|null $occurred_at
 * @property string|null $deleted_at
 * @property string|null $related_to_type
 * @property string|null $related_to_id
 * @property-read \Illuminate\Database\Eloquent\Model|null $relatedTo
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
 * @property string $id
 * @property \AdvisingApp\Report\Enums\TrackedEventType $type
 * @property int $count
 * @property \Illuminate\Support\Carbon|null $last_occurred_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $related_to_type
 * @property string|null $related_to_id
 * @property-read \Illuminate\Database\Eloquent\Model|null $relatedTo
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
 * @property string $id
 * @property string|null $title
 * @property string $topic
 * @property string|null $results
 * @property string $user_id
 * @property \Carbon\CarbonImmutable|null $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $folder_id
 * @property array<array-key, mixed>|null $links
 * @property \AdvisingApp\Ai\Enums\AiModel|null $research_model
 * @property \Carbon\CarbonImmutable|null $started_at
 * @property array<array-key, mixed>|null $search_queries
 * @property array<array-key, mixed>|null $outline
 * @property array<array-key, mixed>|null $remaining_outline
 * @property array<array-key, mixed>|null $sources
 * @property-read \AdvisingApp\Research\Models\ResearchRequestFolder|null $folder
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
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
 * @property string $id
 * @property string $name
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Research\Models\ResearchRequest> $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestFolder withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestFolder {}
}

namespace AdvisingApp\Research\Models{
/**
 * @property string $id
 * @property string $research_request_id
 * @property string $uploaded_at
 * @property string $results
 * @property int $media_id
 * @property string $file_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media $media
 * @property-read \AdvisingApp\Research\Models\ResearchRequest $researchRequest
 * @method static \AdvisingApp\Research\Database\Factories\ResearchRequestParsedFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedFile withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestParsedFile {}
}

namespace AdvisingApp\Research\Models{
/**
 * @property string $id
 * @property string $research_request_id
 * @property string $results
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Research\Models\ResearchRequest $researchRequest
 * @method static \AdvisingApp\Research\Database\Factories\ResearchRequestParsedLinkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedLink withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestParsedLink {}
}

namespace AdvisingApp\Research\Models{
/**
 * @property string $id
 * @property string $research_request_id
 * @property string $results
 * @property string $search_query
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Research\Models\ResearchRequest $researchRequest
 * @method static \AdvisingApp\Research\Database\Factories\ResearchRequestParsedSearchResultsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResearchRequestParsedSearchResults withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResearchRequestParsedSearchResults {}
}

namespace AdvisingApp\Research\Models{
/**
 * @property string $id
 * @property string $content
 * @property string|null $response
 * @property string $research_request_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \AdvisingApp\Research\Models\ResearchRequest $researchRequest
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
 * @property-read \App\Models\User|null $manager
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubArticle|null $resourceHubArticle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManagerResourceHubArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManagerResourceHubArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManagerResourceHubArticle query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperManagerResourceHubArticle {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * @property string $id
 * @property bool $public
 * @property string $title
 * @property array<array-key, mixed>|null $article_details
 * @property string|null $notes
 * @property string|null $quality_id
 * @property string|null $status_id
 * @property string|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $portal_view_count
 * @property bool $has_table_of_contents
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern> $concerns
 * @property-read int|null $concerns_count
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \AdvisingApp\ResourceHub\Models\ManagerResourceHubArticle|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $managers
 * @property-read int|null $managers_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore> $openAiVectorStores
 * @property-read int|null $open_ai_vector_stores_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticle withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubArticle {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * @property string $id
 * @property string $description
 * @property \AdvisingApp\ResourceHub\Enums\ConcernStatus $status
 * @property string $created_by_id
 * @property string $last_updated_by_id
 * @property string $resource_hub_article_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \AdvisingApp\ResourceHub\Models\ResourceHubArticle|null $resourceHubArticle
 * @method static \AdvisingApp\ResourceHub\Database\Factories\ResourceHubArticleConcernFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleConcern newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleConcern newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleConcern onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleConcern query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleConcern withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubArticleConcern withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubArticleConcern {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * @property string $id
 * @property string $resource_hub_item_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
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
 * @property string $id
 * @property string $resource_hub_item_id
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
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
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticle> $resourceHubArticles
 * @property-read int|null $resource_hub_articles_count
 * @method static \AdvisingApp\ResourceHub\Database\Factories\ResourceHubCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubCategory {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticle> $resourceHubArticles
 * @property-read int|null $resource_hub_articles_count
 * @method static \AdvisingApp\ResourceHub\Database\Factories\ResourceHubQualityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubQuality withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubQuality {}
}

namespace AdvisingApp\ResourceHub\Models{
/**
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\ResourceHub\Models\ResourceHubArticle> $resourceHubArticles
 * @property-read int|null $resource_hub_articles_count
 * @method static \AdvisingApp\ResourceHub\Database\Factories\ResourceHubStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceHubStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResourceHubStatus {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $id
 * @property string $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Prospect\Models\ProspectEmailAddress|null $prospectEmailAddress
 * @property-read \AdvisingApp\StudentDataModel\Models\StudentEmailAddress|null $studentEmailAddress
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\BouncedEmailAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BouncedEmailAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BouncedEmailAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BouncedEmailAddress query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBouncedEmailAddress {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $id
 * @property string $number
 * @property string|null $external_error_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Prospect\Models\ProspectPhoneNumber|null $prospectPhoneNumber
 * @property-read \AdvisingApp\StudentDataModel\Models\StudentPhoneNumber|null $studentPhoneNumber
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\BouncedPhoneNumberFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BouncedPhoneNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BouncedPhoneNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BouncedPhoneNumber query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBouncedPhoneNumber {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $id
 * @property string $address
 * @property \AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Prospect\Models\ProspectEmailAddress|null $prospectEmailAddress
 * @property-read \AdvisingApp\StudentDataModel\Models\StudentEmailAddress|null $studentEmailAddress
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\EmailAddressOptInOptOutFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailAddressOptInOptOut newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailAddressOptInOptOut newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailAddressOptInOptOut query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailAddressOptInOptOut {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $sisid
 * @property string|null $division
 * @property string|null $class_nbr
 * @property string|null $crse_grade_off
 * @property int|null $unt_taken
 * @property int|null $unt_earned
 * @property \Illuminate\Support\Carbon|null $last_upd_dt_stmp
 * @property string|null $section
 * @property string|null $name
 * @property string|null $department
 * @property string|null $faculty_name
 * @property string|null $faculty_email
 * @property string|null $semester_code
 * @property string|null $semester_name
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $id
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEnrollment {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $id
 * @property string $name
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\EnrollmentSemesterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnrollmentSemester newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnrollmentSemester newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnrollmentSemester onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnrollmentSemester query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnrollmentSemester withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnrollmentSemester withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEnrollmentSemester {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $id
 * @property string $sisid
 * @property string|null $hold_id
 * @property string $name
 * @property string|null $category
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hold newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hold newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hold query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHold {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $id
 * @property string $number
 * @property \AdvisingApp\StudentDataModel\Enums\PhoneNumberLookupStatus $status
 * @property string|null $carrier_name
 * @property string|null $carrier_type
 * @property array<array-key, mixed>|null $raw_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Prospect\Models\ProspectPhoneNumber> $prospectPhoneNumbers
 * @property-read int|null $prospect_phone_numbers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentPhoneNumber> $studentPhoneNumbers
 * @property-read int|null $student_phone_numbers_count
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\PhoneNumberLookupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneNumberLookup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneNumberLookup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneNumberLookup query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPhoneNumberLookup {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $sisid
 * @property string|null $acad_career
 * @property string|null $division
 * @property string|null $prog_status
 * @property float|null $cum_gpa
 * @property string|null $semester
 * @property string|null $descr
 * @property string|null $foi
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $id
 * @property array<array-key, mixed>|null $acad_plan
 * @property \Illuminate\Support\Carbon|null $change_dt
 * @property \Illuminate\Support\Carbon|null $declare_dt
 * @property \Illuminate\Support\Carbon|null $graduation_dt
 * @property \Illuminate\Support\Carbon|null $conferred_dt
 * @property string|null $catalog_year
 * @property-read string|null $formatted_acad_plan
 * @property-read \AdvisingApp\StudentDataModel\Models\Student|null $student
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\ProgramFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProgram {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $id
 * @property string $number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\StudentDataModel\Models\StudentPhoneNumber|null $phoneNumber
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\SmsOptOutPhoneNumberFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsOptOutPhoneNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsOptOutPhoneNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsOptOutPhoneNumber query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSmsOptOutPhoneNumber {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $display_name
 * @property string $mobile
 * @property string $sisid
 * @property string|null $otherid
 * @property string|null $first
 * @property string|null $last
 * @property string|null $full_name
 * @property string|null $preferred
 * @property \Illuminate\Support\Carbon|null $birthdate
 * @property \Illuminate\Support\Carbon|null $hsgrad
 * @property bool|null $dual
 * @property bool|null $ferpa
 * @property \Illuminate\Support\Carbon|null $dfw
 * @property bool|null $sap
 * @property \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\Hold> $holds
 * @property bool|null $firstgen
 * @property string|null $ethnicity
 * @property string|null $lastlmslogin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at_source
 * @property \Illuminate\Support\Carbon|null $updated_at_source
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $primary_email_id
 * @property string|null $primary_phone_id
 * @property string|null $primary_address_id
 * @property string|null $gender
 * @property string|null $athletics_status
 * @property string|null $athletic_details
 * @property string|null $standing
 * @property string|null $sis_category
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentAddress> $additionalAddresses
 * @property-read int|null $additional_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentEmailAddress> $additionalEmailAddresses
 * @property-read int|null $additional_email_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentPhoneNumber> $additionalPhoneNumbers
 * @property-read int|null $additional_phone_numbers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\StudentDataModel\Models\StudentAddress> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Application\Models\ApplicationSubmission> $applicationSubmissions
 * @property-read int|null $application_submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\BasicNeeds\Models\BasicNeedsProgram> $basicNeedsPrograms
 * @property-read int|null $basic_needs_programs_count
 * @property-read \App\Models\Taggable|\AdvisingApp\Notification\Models\Subscription|\AdvisingApp\Engagement\Models\EngagementFileEntities|\AdvisingApp\CareTeam\Models\CareTeam|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $careTeam
 * @property-read int|null $care_team_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Concern\Models\Concern> $concerns
 * @property-read int|null $concerns_count
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
 * @property-read \AdvisingApp\StudentDataModel\Models\Enrollment|null $firstEnrollmentTerm
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Form\Models\FormSubmission> $formSubmissions
 * @property-read int|null $form_submissions_count
 * @property-read string|null $full_address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Group\Models\GroupSubject> $groupSubjects
 * @property-read int|null $group_subjects_count
 * @property-read int|null $holds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \AdvisingApp\StudentDataModel\Models\Enrollment|null $mostRecentEnrollmentTerm
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Alert\Models\StudentAlert> $studentAlerts
 * @property-read int|null $student_alerts_count
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Concern\Histories\ConcernHistory> $concernHistories
 * @property-read int|null $concern_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Task\Histories\TaskHistory> $taskHistories
 * @property-read int|null $task_histories_count
 * @method static \AdvisingApp\StudentDataModel\Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudent {}
}

namespace AdvisingApp\StudentDataModel\Models{
/**
 * @property string $id
 * @property string $sisid
 * @property string|null $line_1
 * @property string|null $line_2
 * @property string|null $line_3
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal
 * @property string|null $country
 * @property string|null $type
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @property string $id
 * @property string $user_id
 * @property string $students_import_id
 * @property string|null $email_addresses_import_id
 * @property string|null $phone_numbers_import_id
 * @property string|null $addresses_import_id
 * @property string|null $programs_import_id
 * @property string|null $enrollments_import_id
 * @property string|null $job_batch_id
 * @property string|null $started_at
 * @property \Carbon\CarbonImmutable|null $completed_at
 * @property \Carbon\CarbonImmutable|null $canceled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
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
 * @property string $id
 * @property string $sisid
 * @property string $address
 * @property string|null $type
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\StudentDataModel\Models\BouncedEmailAddress|null $bounced
 * @property-read \AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut|null $optedOut
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
 * @property string $id
 * @property string $sisid
 * @property string $number
 * @property int|null $ext
 * @property string|null $type
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber|null $bounced
 * @property-read \AdvisingApp\StudentDataModel\Models\PhoneNumberLookup|null $phoneNumberLookup
 * @property-read \AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber|null $smsOptOut
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
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $embed_enabled
 * @property array<array-key, mixed>|null $allowed_domains
 * @property string|null $primary_color
 * @property \AdvisingApp\Form\Enums\Rounding|null $rounding
 * @property bool $is_authenticated
 * @property bool $is_wizard
 * @property bool $recaptcha_enabled
 * @property array<array-key, mixed>|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveyField> $fields
 * @property-read int|null $fields_count
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
 * @property string $id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $code
 * @property string $survey_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $author
 * @property-read \AdvisingApp\Survey\Models\Survey $submissible
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
 * @property string $id
 * @property string $label
 * @property string $type
 * @property bool $is_required
 * @property array<array-key, mixed> $config
 * @property string $survey_id
 * @property string|null $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AdvisingApp\Survey\Models\SurveyStep|null $step
 * @property-read \AdvisingApp\Survey\Models\Survey $submissible
 * @property-read \AdvisingApp\Survey\Models\SurveyFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveySubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \AdvisingApp\Survey\Database\Factories\SurveyFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSurveyField {}
}

namespace AdvisingApp\Survey\Models{
/**
 * @property string $id
 * @property array<array-key, mixed> $response
 * @property string $field_id
 * @property string $submission_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Survey\Models\SurveyField|null $field
 * @property-read \AdvisingApp\Survey\Models\SurveySubmission $submission
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
 * @property string $id
 * @property string $label
 * @property array<array-key, mixed>|null $content
 * @property string $survey_id
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveyField> $fields
 * @property-read int|null $fields_count
 * @property-read \AdvisingApp\Survey\Models\Survey $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SurveyStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSurveyStep {}
}

namespace AdvisingApp\Survey\Models{
/**
 * @property Student|Prospect|null $author
 * @property string $id
 * @property string $survey_id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property \Carbon\CarbonImmutable|null $submitted_at
 * @property \Carbon\CarbonImmutable|null $canceled_at
 * @property \AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod|null $request_method
 * @property string|null $request_note
 * @property string|null $requester_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \AdvisingApp\Survey\Models\SurveyFieldSubmission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Survey\Models\SurveyField> $fields
 * @property-read int|null $fields_count
 * @property-read \App\Models\User|null $requester
 * @property-read \AdvisingApp\Survey\Models\Survey $submissible
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
 * @property-read \AdvisingApp\Task\Models\Task|null $task
 * @property-read \App\Models\User|null $user
 * @method static \AdvisingApp\Task\Database\Factories\ConfidentialTasksUsersFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfidentialTasksUsers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfidentialTasksUsers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfidentialTasksUsers query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConfidentialTasksUsers {}
}

namespace AdvisingApp\Task\Models{
/**
 * @property string $id
 * @property string $task_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Team\Models\Department $department
 * @property-read \AdvisingApp\Task\Models\Task|null $task
 * @method static \AdvisingApp\Task\Database\Factories\DepartmentsConfidentialTasksFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentsConfidentialTasks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentsConfidentialTasks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentsConfidentialTasks query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepartmentsConfidentialTasks {}
}

namespace AdvisingApp\Task\Models{
/**
 * @property-read Student|Prospect|null $concern
 * @property string $id
 * @property string $title
 * @property string $description
 * @property \AdvisingApp\Task\Enums\TaskStatus $status
 * @property \Illuminate\Support\Carbon|null $due
 * @property string|null $assigned_to
 * @property string|null $created_by
 * @property string|null $concern_type
 * @property string|null $concern_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_confidential
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Task\Models\ConfidentialTasksUsers|\AdvisingApp\Task\Models\DepartmentsConfidentialTasks|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Team\Models\Department> $confidentialAccessDepartments
 * @property-read int|null $confidential_access_departments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $confidentialAccessUsers
 * @property-read int|null $confidential_access_users_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTask {}
}

namespace AdvisingApp\Team\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \AdvisingApp\Team\Database\Factories\DepartmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepartment {}
}

namespace AdvisingApp\Timeline\Models{
/**
 * @property string $id
 * @property string $entity_type
 * @property string $entity_id
 * @property string $timelineable_type
 * @property string $timelineable_id
 * @property string $record_sortable_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $timelineable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline forEntity(\Illuminate\Database\Eloquent\Model $entity)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTimeline {}
}

namespace AdvisingApp\Webhook\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Webhook\Enums\InboundWebhookSource $source
 * @property string $event
 * @property string $url
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandlordInboundWebhook {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property string $workflow_trigger_id
 * @property string $name
 * @property bool $is_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Workflow\Models\WorkflowStep> $workflowSteps
 * @property-read int|null $workflow_steps_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowTrigger|null $workflowTrigger
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workflow withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflow {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property array<array-key, mixed> $care_team
 * @property bool $remove_prior
 * @property string $workflow_step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowCareTeamDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowCareTeamDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $channel
 * @property array<array-key, mixed> $subject
 * @property array<array-key, mixed> $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \AdvisingApp\Notification\Enums\EmailType $email_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowEngagementEmailDetailsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementEmailDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowEngagementEmailDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Notification\Enums\NotificationChannel $channel
 * @property array<array-key, mixed> $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Filament\Forms\Components\RichEditor\RichContentAttribute|null $rich_content
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowEngagementSmsDetailsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEngagementSmsDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowEngagementSmsDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property string|null $event_id
 * @property string $workflow_step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowEventDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowEventDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property string|null $interaction_initiative_id
 * @property string|null $interaction_driver_id
 * @property string|null $interaction_outcome_id
 * @property string|null $interaction_relation_id
 * @property string|null $interaction_status_id
 * @property string|null $interaction_type_id
 * @property \Illuminate\Support\Carbon $start_datetime
 * @property \Illuminate\Support\Carbon|null $end_datetime
 * @property string|null $subject
 * @property string|null $description
 * @property string $workflow_step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowInteractionDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowInteractionDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property string $description
 * @property \AdvisingApp\Concern\Enums\ConcernSeverity $severity
 * @property string $suggested_intervention
 * @property string|null $status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowProactiveConcernDetailsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveConcernDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveConcernDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveConcernDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveConcernDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveConcernDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowProactiveConcernDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowProactiveConcernDetails {}
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
 * @property-read \AdvisingApp\Workflow\Models\WorkflowTrigger|null $workflowTrigger
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowRunFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRun query()
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
 * @property \Illuminate\Support\Carbon|null $execute_at
 * @property \Illuminate\Support\Carbon|null $dispatched_at
 * @property \Illuminate\Support\Carbon|null $succeeded_at
 * @property \Illuminate\Support\Carbon|null $last_failed_at
 * @property string $workflow_run_id
 * @property string $details_type
 * @property string $details_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $delay_minutes
 * @property string|null $previous_workflow_run_step_id
 * @property-read \Illuminate\Database\Eloquent\Model $details
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRun|null $workflowRun
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowRunStepFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStep query()
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
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRunStep|null $workflowRunStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRunStepRelated query()
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowStep withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowStep {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property array<array-key, mixed> $user_ids
 * @property bool $remove_prior
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowSubscriptionDetailsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowSubscriptionDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowSubscriptionDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property array<array-key, mixed> $tag_ids
 * @property bool $remove_prior
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowTagsDetailsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTagsDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowTagsDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $due
 * @property string|null $assigned_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AdvisingApp\Workflow\Models\WorkflowStep|null $workflowStep
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowTaskDetailsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTaskDetails withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowTaskDetails {}
}

namespace AdvisingApp\Workflow\Models{
/**
 * @property string $id
 * @property \AdvisingApp\Workflow\Enums\WorkflowTriggerType $type
 * @property string $related_id
 * @property string $related_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $created_by_type
 * @property string $created_by_id
 * @property string|null $sub_related_type
 * @property string|null $sub_related_id
 * @property \AdvisingApp\Workflow\Enums\WorkflowTriggerEvent|null $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model $createdBy
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @property-read \Illuminate\Database\Eloquent\Model|null $subRelated
 * @property-read \AdvisingApp\Workflow\Models\Workflow|null $workflow
 * @property-read \AdvisingApp\Workflow\Models\WorkflowRun|null $workflowRun
 * @method static \AdvisingApp\Workflow\Database\Factories\WorkflowTriggerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowTrigger withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWorkflowTrigger {}
}

namespace AdvisingApp\Concern\Histories{
/**
 * @property string $id
 * @property string $event
 * @property array<array-key, mixed> $old
 * @property array<array-key, mixed> $new
 * @property string $subject_type
 * @property string $subject_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $formatted
 * @property-read \Illuminate\Database\Eloquent\Model $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernHistory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConcernHistory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConcernHistory {}
}

namespace AdvisingApp\Task\Histories{
/**
 * @property string $id
 * @property string $event
 * @property array<array-key, mixed> $old
 * @property array<array-key, mixed> $new
 * @property string $subject_type
 * @property string $subject_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $formatted
 * @property-read \Illuminate\Database\Eloquent\Model $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskHistory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskHistory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTaskHistory {}
}

namespace AdvisingApp\Alert\Configurations{
/**
 * @property string $id
 * @property int $minimum_age
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Alert\Models\AlertConfiguration|null $alertConfiguration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \AdvisingApp\Alert\Database\Factories\Configurations\AdultLearnerAlertConfigurationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdultLearnerAlertConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdultLearnerAlertConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdultLearnerAlertConfiguration query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdultLearnerAlertConfiguration {}
}

namespace AdvisingApp\Alert\Configurations{
/**
 * @property string $id
 * @property int $minimum_earned_credit_percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Alert\Models\AlertConfiguration|null $alertConfiguration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \AdvisingApp\Alert\Database\Factories\Configurations\LowEarnedCreditPercentageAlertConfigurationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LowEarnedCreditPercentageAlertConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LowEarnedCreditPercentageAlertConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LowEarnedCreditPercentageAlertConfiguration query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLowEarnedCreditPercentageAlertConfiguration {}
}

namespace AdvisingApp\Alert\Configurations{
/**
 * @property string $id
 * @property int $number_of_semesters
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AdvisingApp\Alert\Models\AlertConfiguration|null $alertConfiguration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AdvisingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \AdvisingApp\Alert\Database\Factories\Configurations\NewStudentAlertConfigurationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewStudentAlertConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewStudentAlertConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewStudentAlertConfiguration query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNewStudentAlertConfiguration {}
}

