<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\FailedImportRow
 *
 * @property string $id
 * @property array $data
 * @property string $import_id
 * @property string|null $validation_error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Import $import
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow query()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereImportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereValidationError($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperFailedImportRow {}
}

namespace App\Models{
/**
 * App\Models\Import
 *
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FailedImportRow> $failedRows
 * @property-read int|null $failed_rows_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereImporter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereProcessedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereSuccessfulRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperImport {}
}

namespace App\Models{
/**
 * App\Models\Institution
 *
 * @property string $id
 * @property string|null $code
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\InstitutionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Institution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution query()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInstitution {}
}

namespace App\Models{
/**
 * App\Models\SettingsProperty
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property mixed $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperSettingsProperty {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property string $id
 * @property string|null $emplid
 * @property string|null $name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property string|null $locale
 * @property string|null $type
 * @property bool $is_external
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Task\Models\Task> $assignedTasks
 * @property-read int|null $assigned_tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Assistant\Models\AssistantChatMessageLog> $assistantChatMessageLogs
 * @property-read int|null $assistant_chat_message_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Assistant\Models\AssistantChat> $assistantChats
 * @property-read int|null $assistant_chats_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Consent\Models\ConsentAgreement> $consentAgreements
 * @property-read int|null $consent_agreements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\EngagementBatch> $engagementBatches
 * @property-read int|null $engagement_batches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read mixed $is_admin
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $roleGroups
 * @property-read int|null $role_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Notifications\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Team\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $traitRoleGroups
 * @property-read int|null $trait_role_groups_count
 * @method static \Illuminate\Database\Eloquent\Builder|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder|User advancedFilter($data)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmplid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperUser {}
}

namespace Assist\Alert\Models{
/**
 * Assist\Alert\Models\Alert
 *
 * @property string $id
 * @property string $concern_type
 * @property string $concern_id
 * @property string $description
 * @property \Assist\Alert\Enums\AlertSeverity $severity
 * @property string $suggested_intervention
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Assist\Alert\Enums\AlertStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $concern
 * @method static \Assist\Alert\Database\Factories\AlertFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Alert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert query()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert status(\Assist\Alert\Enums\AlertStatus $status)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereConcernId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereConcernType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereSuggestedIntervention($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperAlert {}
}

namespace Assist\AssistDataModel\Models{
/**
 * Assist\AssistDataModel\Models\Enrollment
 *
 * @method static \Assist\AssistDataModel\Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEnrollment {}
}

namespace Assist\AssistDataModel\Models{
/**
 * Assist\AssistDataModel\Models\Performance
 *
 * @method static \Assist\AssistDataModel\Database\Factories\PerformanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Performance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Performance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Performance query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperPerformance {}
}

namespace Assist\AssistDataModel\Models{
/**
 * Assist\AssistDataModel\Models\Program
 *
 * @method static \Assist\AssistDataModel\Database\Factories\ProgramFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperProgram {}
}

namespace Assist\AssistDataModel\Models{
/**
 * Assist\AssistDataModel\Models\Student
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Alert\Models\Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\AssistDataModel\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\AssistDataModel\Models\Performance> $performances
 * @property-read int|null $performances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\AssistDataModel\Models\Program> $programs
 * @property-read int|null $programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Notifications\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Task\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Assist\AssistDataModel\Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperStudent {}
}

namespace Assist\Assistant\Models{
/**
 * Assist\Assistant\Models\AssistantChat
 *
 * @property string $id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Assistant\Models\AssistantChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChat whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperAssistantChat {}
}

namespace Assist\Assistant\Models{
/**
 * Assist\Assistant\Models\AssistantChatMessage
 *
 * @property string $id
 * @property string $assistant_chat_id
 * @property string $message
 * @property \Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom $from
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Assist\Assistant\Models\AssistantChat|null $chat
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage whereAssistantChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperAssistantChatMessage {}
}

namespace Assist\Assistant\Models{
/**
 * Assist\Assistant\Models\AssistantChatMessageLog
 *
 * @property string $id
 * @property string $message
 * @property array $metadata
 * @property string $user_id
 * @property array $request
 * @property int $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog whereRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssistantChatMessageLog whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperAssistantChatMessageLog {}
}

namespace Assist\Audit\Models{
/**
 * Assist\Audit\Models\Audit
 *
 * @property string $id
 * @property string|null $change_agent_type
 * @property string|null $change_agent_id
 * @property string $event
 * @property string $auditable_type
 * @property string $auditable_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \Assist\Audit\Database\Factories\AuditFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Audit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereChangeAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereChangeAgentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUserAgent($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperAudit {}
}

namespace Assist\Authorization\Models{
/**
 * Assist\Authorization\Models\Permission
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Permission api()
 * @method static \Assist\Authorization\Database\Factories\PermissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission web()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperPermission {}
}

namespace Assist\Authorization\Models\Pivots{
/**
 * Assist\Authorization\Models\Pivots\RoleGroupRolePivot
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupRolePivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupRolePivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupRolePivot query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperRoleGroupRolePivot {}
}

namespace Assist\Authorization\Models\Pivots{
/**
 * Assist\Authorization\Models\Pivots\RoleGroupUserPivot
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupUserPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupUserPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupUserPivot query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperRoleGroupUserPivot {}
}

namespace Assist\Authorization\Models{
/**
 * Assist\Authorization\Models\Role
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $roleGroups
 * @property-read int|null $role_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $traitRoleGroups
 * @property-read int|null $trait_role_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role api()
 * @method static \Assist\Authorization\Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role superAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder|Role web()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperRole {}
}

namespace Assist\Authorization\Models{
/**
 * Assist\Authorization\Models\RoleGroup
 *
 * @property string $id
 * @property string $name
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Assist\Authorization\Database\Factories\RoleGroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperRoleGroup {}
}

namespace Assist\CaseloadManagement\Models{
/**
 * Assist\CaseloadManagement\Models\Caseload
 *
 * @property \Assist\CaseloadManagement\Enums\CaseloadModel $model
 * @property \Assist\CaseloadManagement\Enums\CaseloadType $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\CaseloadManagement\Models\CaseloadSubject> $subjects
 * @property-read int|null $subjects_count
 * @property-read \App\Models\User $user
 * @method static \Assist\CaseloadManagement\Database\Factories\CaseloadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Caseload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Caseload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Caseload query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperCaseload {}
}

namespace Assist\CaseloadManagement\Models{
/**
 * Assist\CaseloadManagement\Models\CaseloadSubject
 *
 * @property-read \Assist\CaseloadManagement\Models\Caseload|null $caseload
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @method static \Illuminate\Database\Eloquent\Builder|CaseloadSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CaseloadSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CaseloadSubject query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperCaseloadSubject {}
}

namespace Assist\Consent\Models{
/**
 * Assist\Consent\Models\ConsentAgreement
 *
 * @property string $id
 * @property \Assist\Consent\Enums\ConsentAgreementType $type
 * @property string $title
 * @property string $description
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Assist\Consent\Database\Factories\ConsentAgreementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement query()
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsentAgreement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperConsentAgreement {}
}

namespace Assist\Division\Models{
/**
 * Assist\Division\Models\Division
 *
 * @property string $id
 * @property string $name
 * @property string $code
 * @property string|null $header
 * @property string|null $footer
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @method static \Assist\Division\Database\Factories\DivisionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Division onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Division query()
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereHeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereLastUpdatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Division withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperDivision {}
}

namespace Assist\Engagement\Models{
/**
 * Assist\Engagement\Models\Engagement
 *
 * @property-read Educatable $recipient
 * @property string $id
 * @property string|null $user_id
 * @property string|null $engagement_batch_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property string|null $subject
 * @property string|null $body
 * @property \Illuminate\Support\Carbon $deliver_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\Engagement\Models\EngagementBatch|null $batch
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\EngagementDeliverable> $deliverables
 * @property-read int|null $deliverables_count
 * @property-read \Assist\Engagement\Models\EngagementBatch|null $engagementBatch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\EngagementDeliverable> $engagementDeliverables
 * @property-read int|null $engagement_deliverables_count
 * @property-read \App\Models\User|null $user
 * @method static \Assist\Engagement\Database\Factories\EngagementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement hasNotBeenDelivered()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement isNotPartOfABatch()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereDeliverAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereEngagementBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereRecipientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEngagement {}
}

namespace Assist\Engagement\Models{
/**
 * Assist\Engagement\Models\EngagementBatch
 *
 * @property string $id
 * @property string|null $identifier
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \App\Models\User $user
 * @method static \Assist\Engagement\Database\Factories\EngagementBatchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEngagementBatch {}
}

namespace Assist\Engagement\Models{
/**
 * Assist\Engagement\Models\EngagementDeliverable
 *
 * @property string $id
 * @property string $engagement_id
 * @property \Assist\Engagement\Enums\EngagementDeliveryMethod $channel
 * @property \Assist\Engagement\Enums\EngagementDeliveryStatus $delivery_status
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $last_delivery_attempt
 * @property string|null $delivery_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\Engagement\Models\Engagement $engagement
 * @method static \Assist\Engagement\Database\Factories\EngagementDeliverableFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereDeliveryResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereEngagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereLastDeliveryAttempt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEngagementDeliverable {}
}

namespace Assist\Engagement\Models{
/**
 * Assist\Engagement\Models\EngagementFile
 *
 * @property string $id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\AssistDataModel\Models\Student> $students
 * @property-read int|null $students_count
 * @method static \Assist\Engagement\Database\Factories\EngagementFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEngagementFile {}
}

namespace Assist\Engagement\Models{
/**
 * Assist\Engagement\Models\EngagementFileEntities
 *
 * @property string $engagement_file_id
 * @property string $entity_id
 * @property string $entity_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Assist\Engagement\Models\EngagementFile|null $engagementFile
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $entity
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEngagementFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEngagementFileEntities {}
}

namespace Assist\Engagement\Models{
/**
 * Assist\Engagement\Models\EngagementResponse
 *
 * @property string $id
 * @property string|null $sender_id
 * @property string|null $sender_type
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $sender
 * @method static \Assist\Engagement\Database\Factories\EngagementResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereSenderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEngagementResponse {}
}

namespace Assist\Form\Models{
/**
 * Assist\Form\Models\Form
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Form\Models\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Form\Models\FormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \Assist\Form\Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperForm {}
}

namespace Assist\Form\Models{
/**
 * Assist\Form\Models\FormField
 *
 * @property-read \Assist\Form\Models\Form|null $form
 * @method static \Assist\Form\Database\Factories\FormFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperFormField {}
}

namespace Assist\Form\Models{
/**
 * Assist\Form\Models\FormSubmission
 *
 * @property-read \Assist\Form\Models\Form|null $form
 * @method static \Assist\Form\Database\Factories\FormSubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormSubmission query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperFormSubmission {}
}

namespace Assist\Interaction\Models{
/**
 * Assist\Interaction\Models\Interaction
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $interactable_id
 * @property string|null $interactable_type
 * @property string|null $interaction_type_id
 * @property string|null $interaction_relation_id
 * @property string|null $interaction_campaign_id
 * @property string|null $interaction_driver_id
 * @property string|null $interaction_status_id
 * @property string|null $interaction_outcome_id
 * @property string|null $interaction_institution_id
 * @property \Illuminate\Support\Carbon $start_datetime
 * @property \Illuminate\Support\Carbon|null $end_datetime
 * @property string|null $subject
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\Interaction\Models\InteractionCampaign|null $campaign
 * @property-read \Assist\Interaction\Models\InteractionDriver|null $driver
 * @property-read \Assist\Interaction\Models\InteractionInstitution|null $institution
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $interactable
 * @property-read \Assist\Interaction\Models\InteractionOutcome|null $outcome
 * @property-read \Assist\Interaction\Models\InteractionRelation|null $relation
 * @property-read \Assist\Interaction\Models\InteractionStatus|null $status
 * @property-read \Assist\Interaction\Models\InteractionType|null $type
 * @method static \Assist\Interaction\Database\Factories\InteractionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereEndDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionOutcomeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereInteractionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereStartDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interaction whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInteraction {}
}

namespace Assist\Interaction\Models{
/**
 * Assist\Interaction\Models\InteractionCampaign
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \Assist\Interaction\Database\Factories\InteractionCampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionCampaign withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInteractionCampaign {}
}

namespace Assist\Interaction\Models{
/**
 * Assist\Interaction\Models\InteractionDriver
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \Assist\Interaction\Database\Factories\InteractionDriverFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionDriver withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInteractionDriver {}
}

namespace Assist\Interaction\Models{
/**
 * Assist\Interaction\Models\InteractionInstitution
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \Assist\Interaction\Database\Factories\InteractionInstitutionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionInstitution withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInteractionInstitution {}
}

namespace Assist\Interaction\Models{
/**
 * Assist\Interaction\Models\InteractionOutcome
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \Assist\Interaction\Database\Factories\InteractionOutcomeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionOutcome withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInteractionOutcome {}
}

namespace Assist\Interaction\Models{
/**
 * Assist\Interaction\Models\InteractionRelation
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \Assist\Interaction\Database\Factories\InteractionRelationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionRelation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInteractionRelation {}
}

namespace Assist\Interaction\Models{
/**
 * Assist\Interaction\Models\InteractionStatus
 *
 * @property string $id
 * @property string $name
 * @property \Assist\Interaction\Enums\InteractionStatusColorOptions $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \Assist\Interaction\Database\Factories\InteractionStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInteractionStatus {}
}

namespace Assist\Interaction\Models{
/**
 * Assist\Interaction\Models\InteractionType
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static \Assist\Interaction\Database\Factories\InteractionTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InteractionType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInteractionType {}
}

namespace Assist\KnowledgeBase\Models{
/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseCategory
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperKnowledgeBaseCategory {}
}

namespace Assist\KnowledgeBase\Models{
/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseItem
 *
 * @property string $id
 * @property string $question
 * @property bool $public
 * @property string|null $solution
 * @property string|null $notes
 * @property string|null $quality_id
 * @property string|null $status_id
 * @property string|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Institution> $institution
 * @property-read int|null $institution_count
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseQuality|null $quality
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseStatus|null $status
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereQualityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereSolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperKnowledgeBaseItem {}
}

namespace Assist\KnowledgeBase\Models{
/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseQuality
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseQualityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperKnowledgeBaseQuality {}
}

namespace Assist\KnowledgeBase\Models{
/**
 * Assist\KnowledgeBase\Models\KnowledgeBaseStatus
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperKnowledgeBaseStatus {}
}

namespace Assist\Notifications\Models{
/**
 * Assist\Notifications\Models\Subscription
 *
 * @property string $id
 * @property string $user_id
 * @property string $subscribable_id
 * @property string $subscribable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subscribable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubscribableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubscribableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperSubscription {}
}

namespace Assist\Prospect\Models{
/**
 * Assist\Prospect\Models\Prospect
 *
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
 * @property string|null $birthdate
 * @property string|null $hsgrad
 * @property string|null $assigned_to_id
 * @property string|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Alert\Models\Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read \Assist\Prospect\Models\ProspectSource $source
 * @property-read \Assist\Prospect\Models\ProspectStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Notifications\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Task\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Assist\Prospect\Database\Factories\ProspectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect query()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereAssignedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereEmail2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereEmailBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereHsgrad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect wherePreferred($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereSmsOptOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperProspect {}
}

namespace Assist\Prospect\Models{
/**
 * Assist\Prospect\Models\ProspectSource
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static \Assist\Prospect\Database\Factories\ProspectSourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperProspectSource {}
}

namespace Assist\Prospect\Models{
/**
 * Assist\Prospect\Models\ProspectStatus
 *
 * @property string $id
 * @property string $name
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static \Assist\Prospect\Database\Factories\ProspectStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperProspectStatus {}
}

namespace Assist\ServiceManagement\Models{
/**
 * Assist\ServiceManagement\Models\ServiceRequest
 *
 * @property-read Student|Prospect $respondent
 * @property string $id
 * @property string $service_request_number
 * @property string|null $respondent_type
 * @property string|null $respondent_id
 * @property string|null $close_details
 * @property string|null $res_details
 * @property string|null $institution_id
 * @property string|null $status_id
 * @property string|null $type_id
 * @property string|null $priority_id
 * @property string|null $assigned_to_id
 * @property string|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Institution|null $institution
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Interaction\Models\Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Assist\ServiceManagement\Models\ServiceRequestPriority|null $priority
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\ServiceManagement\Models\ServiceRequestUpdate> $serviceRequestUpdates
 * @property-read int|null $service_request_updates_count
 * @property-read \Assist\ServiceManagement\Models\ServiceRequestStatus|null $status
 * @property-read \Assist\ServiceManagement\Models\ServiceRequestType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest educatableSearch(string $relationship, string $search)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest educatableSort(string $direction)
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereAssignedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereCloseDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereResDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereRespondentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereRespondentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereServiceRequestNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperServiceRequest {}
}

namespace Assist\ServiceManagement\Models{
/**
 * Assist\ServiceManagement\Models\ServiceRequestPriority
 *
 * @property string $id
 * @property string $name
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestPriorityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperServiceRequestPriority {}
}

namespace Assist\ServiceManagement\Models{
/**
 * Assist\ServiceManagement\Models\ServiceRequestStatus
 *
 * @property string $id
 * @property string $name
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperServiceRequestStatus {}
}

namespace Assist\ServiceManagement\Models{
/**
 * Assist\ServiceManagement\Models\ServiceRequestType
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperServiceRequestType {}
}

namespace Assist\ServiceManagement\Models{
/**
 * Assist\ServiceManagement\Models\ServiceRequestUpdate
 *
 * @property string $id
 * @property string|null $service_request_id
 * @property string $update
 * @property bool $internal
 * @property \Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection $direction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\ServiceManagement\Models\ServiceRequest|null $serviceRequest
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestUpdateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperServiceRequestUpdate {}
}

namespace Assist\Task\Models{
/**
 * Assist\Task\Models\Task
 *
 * @property-read Student|Prospect $concern
 * @property string $id
 * @property string $description
 * @property \Assist\Task\Enums\TaskStatus $status
 * @property \Illuminate\Support\Carbon|null $due
 * @property string|null $assigned_to
 * @property string|null $created_by
 * @property string|null $concern_type
 * @property string|null $concern_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|Task byNextDue()
 * @method static \Assist\Task\Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereConcernId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereConcernType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperTask {}
}

namespace Assist\Team\Models{
/**
 * Assist\Team\Models\Team
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Assist\Team\Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperTeam {}
}

namespace Assist\Team\Models{
/**
 * Assist\Team\Models\TeamUser
 *
 * @property-read \Assist\Team\Models\Team|null $team
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperTeamUser {}
}

namespace Assist\Webhook\Models{
/**
 * Assist\Webhook\Models\InboundWebhook
 *
 * @property string $id
 * @property \Assist\Webhook\Enums\InboundWebhookSource $source
 * @property string $event
 * @property string $url
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereUrl($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInboundWebhook {}
}

