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
 * App\Models\EngagementEmailItem
 *
 * @property int $id
 * @property string $email
 * @property string $subject
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static Builder|EngagementEmailItem advancedFilter($data)
 * @method static Builder|EngagementEmailItem newModelQuery()
 * @method static Builder|EngagementEmailItem newQuery()
 * @method static Builder|EngagementEmailItem onlyTrashed()
 * @method static Builder|EngagementEmailItem query()
 * @method static Builder|EngagementEmailItem whereBody($value)
 * @method static Builder|EngagementEmailItem whereCreatedAt($value)
 * @method static Builder|EngagementEmailItem whereDeletedAt($value)
 * @method static Builder|EngagementEmailItem whereEmail($value)
 * @method static Builder|EngagementEmailItem whereId($value)
 * @method static Builder|EngagementEmailItem whereSubject($value)
 * @method static Builder|EngagementEmailItem whereUpdatedAt($value)
 * @method static Builder|EngagementEmailItem withTrashed()
 * @method static Builder|EngagementEmailItem withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEngagementEmailItem {}
}

namespace App\Models{
/**
 * App\Models\EngagementTextItem
 *
 * @property int $id
 * @property string|null $direction
 * @property int $mobile
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $direction_label
 * @method static Builder|EngagementTextItem advancedFilter($data)
 * @method static Builder|EngagementTextItem newModelQuery()
 * @method static Builder|EngagementTextItem newQuery()
 * @method static Builder|EngagementTextItem onlyTrashed()
 * @method static Builder|EngagementTextItem query()
 * @method static Builder|EngagementTextItem whereCreatedAt($value)
 * @method static Builder|EngagementTextItem whereDeletedAt($value)
 * @method static Builder|EngagementTextItem whereDirection($value)
 * @method static Builder|EngagementTextItem whereId($value)
 * @method static Builder|EngagementTextItem whereMessage($value)
 * @method static Builder|EngagementTextItem whereMobile($value)
 * @method static Builder|EngagementTextItem whereUpdatedAt($value)
 * @method static Builder|EngagementTextItem withTrashed()
 * @method static Builder|EngagementTextItem withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEngagementTextItem {}
}

namespace App\Models{
/**
 * App\Models\Enrollment
 *
 * @method static \Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static Builder|Enrollment newModelQuery()
 * @method static Builder|Enrollment newQuery()
 * @method static Builder|Enrollment query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperEnrollment {}
}

namespace App\Models{
/**
 * App\Models\Institution
 *
 * @property string $id
 * @property string|null $code
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\InstitutionFactory factory($count = null, $state = [])
 * @method static Builder|Institution newModelQuery()
 * @method static Builder|Institution newQuery()
 * @method static Builder|Institution onlyTrashed()
 * @method static Builder|Institution query()
 * @method static Builder|Institution whereCode($value)
 * @method static Builder|Institution whereCreatedAt($value)
 * @method static Builder|Institution whereDeletedAt($value)
 * @method static Builder|Institution whereDescription($value)
 * @method static Builder|Institution whereId($value)
 * @method static Builder|Institution whereName($value)
 * @method static Builder|Institution whereUpdatedAt($value)
 * @method static Builder|Institution withTrashed()
 * @method static Builder|Institution withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperInstitution {}
}

namespace App\Models{
/**
 * App\Models\JourneyEmailItem
 *
 * @property int $id
 * @property string $name
 * @property string $body
 * @property string $start
 * @property string|null $end
 * @property string|null $active
 * @property string $frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $active_label
 * @property-read mixed $frequency_label
 * @method static Builder|JourneyEmailItem advancedFilter($data)
 * @method static Builder|JourneyEmailItem newModelQuery()
 * @method static Builder|JourneyEmailItem newQuery()
 * @method static Builder|JourneyEmailItem onlyTrashed()
 * @method static Builder|JourneyEmailItem query()
 * @method static Builder|JourneyEmailItem whereActive($value)
 * @method static Builder|JourneyEmailItem whereBody($value)
 * @method static Builder|JourneyEmailItem whereCreatedAt($value)
 * @method static Builder|JourneyEmailItem whereDeletedAt($value)
 * @method static Builder|JourneyEmailItem whereEnd($value)
 * @method static Builder|JourneyEmailItem whereFrequency($value)
 * @method static Builder|JourneyEmailItem whereId($value)
 * @method static Builder|JourneyEmailItem whereName($value)
 * @method static Builder|JourneyEmailItem whereStart($value)
 * @method static Builder|JourneyEmailItem whereUpdatedAt($value)
 * @method static Builder|JourneyEmailItem withTrashed()
 * @method static Builder|JourneyEmailItem withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperJourneyEmailItem {}
}

namespace App\Models{
/**
 * App\Models\JourneyItem
 *
 * @property int $id
 * @property string|null $name
 * @property string $body
 * @property string $start
 * @property string $end
 * @property string $frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $frequency_label
 * @method static Builder|JourneyItem advancedFilter($data)
 * @method static Builder|JourneyItem newModelQuery()
 * @method static Builder|JourneyItem newQuery()
 * @method static Builder|JourneyItem onlyTrashed()
 * @method static Builder|JourneyItem query()
 * @method static Builder|JourneyItem whereBody($value)
 * @method static Builder|JourneyItem whereCreatedAt($value)
 * @method static Builder|JourneyItem whereDeletedAt($value)
 * @method static Builder|JourneyItem whereEnd($value)
 * @method static Builder|JourneyItem whereFrequency($value)
 * @method static Builder|JourneyItem whereId($value)
 * @method static Builder|JourneyItem whereName($value)
 * @method static Builder|JourneyItem whereStart($value)
 * @method static Builder|JourneyItem whereUpdatedAt($value)
 * @method static Builder|JourneyItem withTrashed()
 * @method static Builder|JourneyItem withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperJourneyItem {}
}

namespace App\Models{
/**
 * App\Models\JourneyTargetList
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $query
 * @property int|null $population
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static Builder|JourneyTargetList advancedFilter($data)
 * @method static Builder|JourneyTargetList newModelQuery()
 * @method static Builder|JourneyTargetList newQuery()
 * @method static Builder|JourneyTargetList onlyTrashed()
 * @method static Builder|JourneyTargetList query()
 * @method static Builder|JourneyTargetList whereCreatedAt($value)
 * @method static Builder|JourneyTargetList whereDeletedAt($value)
 * @method static Builder|JourneyTargetList whereDescription($value)
 * @method static Builder|JourneyTargetList whereId($value)
 * @method static Builder|JourneyTargetList whereName($value)
 * @method static Builder|JourneyTargetList wherePopulation($value)
 * @method static Builder|JourneyTargetList whereQuery($value)
 * @method static Builder|JourneyTargetList whereUpdatedAt($value)
 * @method static Builder|JourneyTargetList withTrashed()
 * @method static Builder|JourneyTargetList withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperJourneyTargetList {}
}

namespace App\Models{
/**
 * App\Models\JourneyTextItem
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $text
 * @property string|null $start
 * @property string|null $end
 * @property string $active
 * @property string|null $frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $active_label
 * @property-read mixed $frequency_label
 * @method static Builder|JourneyTextItem advancedFilter($data)
 * @method static Builder|JourneyTextItem newModelQuery()
 * @method static Builder|JourneyTextItem newQuery()
 * @method static Builder|JourneyTextItem onlyTrashed()
 * @method static Builder|JourneyTextItem query()
 * @method static Builder|JourneyTextItem whereActive($value)
 * @method static Builder|JourneyTextItem whereCreatedAt($value)
 * @method static Builder|JourneyTextItem whereDeletedAt($value)
 * @method static Builder|JourneyTextItem whereEnd($value)
 * @method static Builder|JourneyTextItem whereFrequency($value)
 * @method static Builder|JourneyTextItem whereId($value)
 * @method static Builder|JourneyTextItem whereName($value)
 * @method static Builder|JourneyTextItem whereStart($value)
 * @method static Builder|JourneyTextItem whereText($value)
 * @method static Builder|JourneyTextItem whereUpdatedAt($value)
 * @method static Builder|JourneyTextItem withTrashed()
 * @method static Builder|JourneyTextItem withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperJourneyTextItem {}
}

namespace App\Models{
/**
 * App\Models\Program
 *
 * @method static \Database\Factories\ProgramFactory factory($count = null, $state = [])
 * @method static Builder|Program newModelQuery()
 * @method static Builder|Program newQuery()
 * @method static Builder|Program query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperProgram {}
}

namespace App\Models{
/**
 * App\Models\ReportProspect
 *
 * @method static Builder|ReportProspect newModelQuery()
 * @method static Builder|ReportProspect newQuery()
 * @method static Builder|ReportProspect query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperReportProspect {}
}

namespace App\Models{
/**
 * App\Models\ReportStudent
 *
 * @method static Builder|ReportStudent newModelQuery()
 * @method static Builder|ReportStudent newQuery()
 * @method static Builder|ReportStudent query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperReportStudent {}
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
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
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
 * @property-read Collection<int, \App\Models\UserAlert> $alerts
 * @property-read int|null $alerts_count
 * @property-read Collection<int, AssistantChat> $assistantChats
 * @property-read int|null $assistant_chats_count
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\Engagement\Models\EngagementBatch> $engagementBatches
 * @property-read int|null $engagement_batches_count
 * @property-read Collection<int, \Assist\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read mixed $is_admin
 * @property-read mixed $type_label
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, RoleGroup> $roleGroups
 * @property-read int|null $role_groups_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, RoleGroup> $traitRoleGroups
 * @property-read int|null $trait_role_groups_count
 * @method static Builder|User admins()
 * @method static Builder|User advancedFilter($data)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereEmplid($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsExternal($value)
 * @method static Builder|User whereLocale($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereType($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperUser {}
}

namespace App\Models{
/**
 * App\Models\UserAlert
 *
 * @property int $id
 * @property string|null $message
 * @property string|null $link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static Builder|UserAlert advancedFilter($data)
 * @method static Builder|UserAlert newModelQuery()
 * @method static Builder|UserAlert newQuery()
 * @method static Builder|UserAlert onlyTrashed()
 * @method static Builder|UserAlert query()
 * @method static Builder|UserAlert whereCreatedAt($value)
 * @method static Builder|UserAlert whereDeletedAt($value)
 * @method static Builder|UserAlert whereId($value)
 * @method static Builder|UserAlert whereLink($value)
 * @method static Builder|UserAlert whereMessage($value)
 * @method static Builder|UserAlert whereUpdatedAt($value)
 * @method static Builder|UserAlert withTrashed()
 * @method static Builder|UserAlert withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperUserAlert {}
}

namespace Assist\Alert\Models{
/**
 * Assist\Alert\Models\Alert
 *
 * @property string $id
 * @property string $concern_type
 * @property string $concern_id
 * @property string $description
 * @property AlertSeverity $severity
 * @property string $suggested_intervention
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Model|\Eloquent $concern
 * @method static \Assist\Alert\Database\Factories\AlertFactory factory($count = null, $state = [])
 * @method static Builder|Alert newModelQuery()
 * @method static Builder|Alert newQuery()
 * @method static Builder|Alert onlyTrashed()
 * @method static Builder|Alert query()
 * @method static Builder|Alert whereConcernId($value)
 * @method static Builder|Alert whereConcernType($value)
 * @method static Builder|Alert whereCreatedAt($value)
 * @method static Builder|Alert whereDeletedAt($value)
 * @method static Builder|Alert whereDescription($value)
 * @method static Builder|Alert whereId($value)
 * @method static Builder|Alert whereSeverity($value)
 * @method static Builder|Alert whereSuggestedIntervention($value)
 * @method static Builder|Alert whereUpdatedAt($value)
 * @method static Builder|Alert withTrashed()
 * @method static Builder|Alert withoutTrashed()
 * @mixin Eloquent
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
 * @property-read Collection<int, Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read Collection<int, EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read Collection<int, Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read Collection<int, \Assist\AssistDataModel\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, \Assist\AssistDataModel\Models\Performance> $performances
 * @property-read int|null $performances_count
 * @property-read Collection<int, \Assist\AssistDataModel\Models\Program> $programs
 * @property-read int|null $programs_count
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Assist\AssistDataModel\Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static Builder|Student newModelQuery()
 * @method static Builder|Student newQuery()
 * @method static Builder|Student query()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \Assist\Assistant\Models\AssistantChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read User $user
 * @method static Builder|AssistantChat newModelQuery()
 * @method static Builder|AssistantChat newQuery()
 * @method static Builder|AssistantChat query()
 * @method static Builder|AssistantChat whereCreatedAt($value)
 * @method static Builder|AssistantChat whereId($value)
 * @method static Builder|AssistantChat whereUpdatedAt($value)
 * @method static Builder|AssistantChat whereUserId($value)
 * @mixin Eloquent
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
 * @property AIChatMessageFrom $from
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Assist\Assistant\Models\AssistantChat|null $chat
 * @method static Builder|AssistantChatMessage newModelQuery()
 * @method static Builder|AssistantChatMessage newQuery()
 * @method static Builder|AssistantChatMessage query()
 * @method static Builder|AssistantChatMessage whereAssistantChatId($value)
 * @method static Builder|AssistantChatMessage whereCreatedAt($value)
 * @method static Builder|AssistantChatMessage whereFrom($value)
 * @method static Builder|AssistantChatMessage whereId($value)
 * @method static Builder|AssistantChatMessage whereMessage($value)
 * @method static Builder|AssistantChatMessage whereUpdatedAt($value)
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperAssistantChatMessage {}
}

namespace Assist\Audit\Models{
/**
 * Assist\Audit\Models\Audit
 *
 * @property int $id
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
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
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $roleGroups
 * @property-read int|null $role_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $traitRoleGroups
 * @property-read int|null $trait_role_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static Builder|Role api()
 * @method static \Assist\Authorization\Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static Builder|Role query()
 * @method static Builder|Role superAdmin()
 * @method static Builder|Role web()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereGuardName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static \Assist\Authorization\Database\Factories\RoleGroupFactory factory($count = null, $state = [])
 * @method static Builder|RoleGroup newModelQuery()
 * @method static Builder|RoleGroup newQuery()
 * @method static Builder|RoleGroup onlyTrashed()
 * @method static Builder|RoleGroup query()
 * @method static Builder|RoleGroup whereCreatedAt($value)
 * @method static Builder|RoleGroup whereDeletedAt($value)
 * @method static Builder|RoleGroup whereId($value)
 * @method static Builder|RoleGroup whereName($value)
 * @method static Builder|RoleGroup whereSlug($value)
 * @method static Builder|RoleGroup whereUpdatedAt($value)
 * @method static Builder|RoleGroup withTrashed()
 * @method static Builder|RoleGroup withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperRoleGroup {}
}

namespace Assist\Engagement\Models{
/**
 * Assist\Engagement\Models\Engagement
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $engagement_batch_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property string|null $subject
 * @property string|null $body
 * @property string $deliver_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\Engagement\Models\EngagementBatch|null $batch
 * @property-read User|null $createdBy
 * @property-read Collection<int, \Assist\Engagement\Models\EngagementDeliverable> $deliverables
 * @property-read int|null $deliverables_count
 * @property-read \Assist\Engagement\Models\EngagementBatch|null $engagementBatch
 * @property-read Collection<int, \Assist\Engagement\Models\EngagementDeliverable> $engagementDeliverables
 * @property-read int|null $engagement_deliverables_count
 * @property-read Model|\Eloquent $recipient
 * @property-read User|null $user
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
 * @mixin Eloquent
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
 * @property-read User $user
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
 * @property EngagementDeliveryMethod $channel
 * @property EngagementDeliveryStatus $delivery_status
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, Prospect> $prospects
 * @property-read int|null $prospects_count
 * @property-read Collection<int, Student> $students
 * @property-read int|null $students_count
 * @method static \Assist\Engagement\Database\Factories\EngagementFileFactory factory($count = null, $state = [])
 * @method static Builder|EngagementFile newModelQuery()
 * @method static Builder|EngagementFile newQuery()
 * @method static Builder|EngagementFile query()
 * @method static Builder|EngagementFile whereCreatedAt($value)
 * @method static Builder|EngagementFile whereDescription($value)
 * @method static Builder|EngagementFile whereId($value)
 * @method static Builder|EngagementFile whereUpdatedAt($value)
 * @mixin Eloquent
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
 * @property string|null $sent_at
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
 * @property InteractionStatusColorOptions $color
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseCategoryFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseCategory newModelQuery()
 * @method static Builder|KnowledgeBaseCategory newQuery()
 * @method static Builder|KnowledgeBaseCategory onlyTrashed()
 * @method static Builder|KnowledgeBaseCategory query()
 * @method static Builder|KnowledgeBaseCategory whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseCategory whereDeletedAt($value)
 * @method static Builder|KnowledgeBaseCategory whereId($value)
 * @method static Builder|KnowledgeBaseCategory whereName($value)
 * @method static Builder|KnowledgeBaseCategory whereUpdatedAt($value)
 * @method static Builder|KnowledgeBaseCategory withTrashed()
 * @method static Builder|KnowledgeBaseCategory withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseCategory|null $category
 * @property-read Collection<int, Institution> $institution
 * @property-read int|null $institution_count
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseQuality|null $quality
 * @property-read \Assist\KnowledgeBase\Models\KnowledgeBaseStatus|null $status
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseItemFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseItem newModelQuery()
 * @method static Builder|KnowledgeBaseItem newQuery()
 * @method static Builder|KnowledgeBaseItem onlyTrashed()
 * @method static Builder|KnowledgeBaseItem query()
 * @method static Builder|KnowledgeBaseItem whereCategoryId($value)
 * @method static Builder|KnowledgeBaseItem whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseItem whereDeletedAt($value)
 * @method static Builder|KnowledgeBaseItem whereId($value)
 * @method static Builder|KnowledgeBaseItem whereNotes($value)
 * @method static Builder|KnowledgeBaseItem wherePublic($value)
 * @method static Builder|KnowledgeBaseItem whereQualityId($value)
 * @method static Builder|KnowledgeBaseItem whereQuestion($value)
 * @method static Builder|KnowledgeBaseItem whereSolution($value)
 * @method static Builder|KnowledgeBaseItem whereStatusId($value)
 * @method static Builder|KnowledgeBaseItem whereUpdatedAt($value)
 * @method static Builder|KnowledgeBaseItem withTrashed()
 * @method static Builder|KnowledgeBaseItem withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseQualityFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseQuality newModelQuery()
 * @method static Builder|KnowledgeBaseQuality newQuery()
 * @method static Builder|KnowledgeBaseQuality onlyTrashed()
 * @method static Builder|KnowledgeBaseQuality query()
 * @method static Builder|KnowledgeBaseQuality whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseQuality whereDeletedAt($value)
 * @method static Builder|KnowledgeBaseQuality whereId($value)
 * @method static Builder|KnowledgeBaseQuality whereName($value)
 * @method static Builder|KnowledgeBaseQuality whereUpdatedAt($value)
 * @method static Builder|KnowledgeBaseQuality withTrashed()
 * @method static Builder|KnowledgeBaseQuality withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static \Assist\KnowledgeBase\Database\Factories\KnowledgeBaseStatusFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseStatus newModelQuery()
 * @method static Builder|KnowledgeBaseStatus newQuery()
 * @method static Builder|KnowledgeBaseStatus onlyTrashed()
 * @method static Builder|KnowledgeBaseStatus query()
 * @method static Builder|KnowledgeBaseStatus whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseStatus whereDeletedAt($value)
 * @method static Builder|KnowledgeBaseStatus whereId($value)
 * @method static Builder|KnowledgeBaseStatus whereName($value)
 * @method static Builder|KnowledgeBaseStatus whereUpdatedAt($value)
 * @method static Builder|KnowledgeBaseStatus withTrashed()
 * @method static Builder|KnowledgeBaseStatus withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Model|\Eloquent $subscribable
 * @property-read User $user
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription onlyTrashed()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereDeletedAt($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereSubscribableId($value)
 * @method static Builder|Subscription whereSubscribableType($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @method static Builder|Subscription whereUserId($value)
 * @method static Builder|Subscription withTrashed()
 * @method static Builder|Subscription withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read User|null $assignedTo
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read Collection<int, EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read Collection<int, Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read \Assist\Prospect\Models\ProspectSource $source
 * @property-read \Assist\Prospect\Models\ProspectStatus $status
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Assist\Prospect\Database\Factories\ProspectFactory factory($count = null, $state = [])
 * @method static Builder|Prospect newModelQuery()
 * @method static Builder|Prospect newQuery()
 * @method static Builder|Prospect onlyTrashed()
 * @method static Builder|Prospect query()
 * @method static Builder|Prospect whereAddress($value)
 * @method static Builder|Prospect whereAddress2($value)
 * @method static Builder|Prospect whereAssignedToId($value)
 * @method static Builder|Prospect whereBirthdate($value)
 * @method static Builder|Prospect whereCreatedAt($value)
 * @method static Builder|Prospect whereCreatedById($value)
 * @method static Builder|Prospect whereDeletedAt($value)
 * @method static Builder|Prospect whereDescription($value)
 * @method static Builder|Prospect whereEmail($value)
 * @method static Builder|Prospect whereEmail2($value)
 * @method static Builder|Prospect whereEmailBounce($value)
 * @method static Builder|Prospect whereFirstName($value)
 * @method static Builder|Prospect whereFullName($value)
 * @method static Builder|Prospect whereHsgrad($value)
 * @method static Builder|Prospect whereId($value)
 * @method static Builder|Prospect whereLastName($value)
 * @method static Builder|Prospect whereMobile($value)
 * @method static Builder|Prospect wherePhone($value)
 * @method static Builder|Prospect wherePreferred($value)
 * @method static Builder|Prospect whereSmsOptOut($value)
 * @method static Builder|Prospect whereSourceId($value)
 * @method static Builder|Prospect whereStatusId($value)
 * @method static Builder|Prospect whereUpdatedAt($value)
 * @method static Builder|Prospect withTrashed()
 * @method static Builder|Prospect withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static \Assist\Prospect\Database\Factories\ProspectSourceFactory factory($count = null, $state = [])
 * @method static Builder|ProspectSource newModelQuery()
 * @method static Builder|ProspectSource newQuery()
 * @method static Builder|ProspectSource onlyTrashed()
 * @method static Builder|ProspectSource query()
 * @method static Builder|ProspectSource whereCreatedAt($value)
 * @method static Builder|ProspectSource whereDeletedAt($value)
 * @method static Builder|ProspectSource whereId($value)
 * @method static Builder|ProspectSource whereName($value)
 * @method static Builder|ProspectSource whereUpdatedAt($value)
 * @method static Builder|ProspectSource withTrashed()
 * @method static Builder|ProspectSource withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\Prospect\Models\Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static \Assist\Prospect\Database\Factories\ProspectStatusFactory factory($count = null, $state = [])
 * @method static Builder|ProspectStatus newModelQuery()
 * @method static Builder|ProspectStatus newQuery()
 * @method static Builder|ProspectStatus onlyTrashed()
 * @method static Builder|ProspectStatus query()
 * @method static Builder|ProspectStatus whereColor($value)
 * @method static Builder|ProspectStatus whereCreatedAt($value)
 * @method static Builder|ProspectStatus whereDeletedAt($value)
 * @method static Builder|ProspectStatus whereId($value)
 * @method static Builder|ProspectStatus whereName($value)
 * @method static Builder|ProspectStatus whereUpdatedAt($value)
 * @method static Builder|ProspectStatus withTrashed()
 * @method static Builder|ProspectStatus withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperProspectStatus {}
}

namespace Assist\ServiceManagement\Models{
/**
 * Assist\ServiceManagement\Models\ServiceRequest
 *
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
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $respondent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\ServiceManagement\Models\ServiceRequestUpdate> $serviceRequestUpdates
 * @property-read int|null $service_request_updates_count
 * @property-read \Assist\ServiceManagement\Models\ServiceRequestStatus|null $status
 * @property-read \Assist\ServiceManagement\Models\ServiceRequestType|null $type
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestPriorityFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequestPriority newModelQuery()
 * @method static Builder|ServiceRequestPriority newQuery()
 * @method static Builder|ServiceRequestPriority onlyTrashed()
 * @method static Builder|ServiceRequestPriority query()
 * @method static Builder|ServiceRequestPriority whereCreatedAt($value)
 * @method static Builder|ServiceRequestPriority whereDeletedAt($value)
 * @method static Builder|ServiceRequestPriority whereId($value)
 * @method static Builder|ServiceRequestPriority whereName($value)
 * @method static Builder|ServiceRequestPriority whereOrder($value)
 * @method static Builder|ServiceRequestPriority whereUpdatedAt($value)
 * @method static Builder|ServiceRequestPriority withTrashed()
 * @method static Builder|ServiceRequestPriority withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestStatusFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequestStatus newModelQuery()
 * @method static Builder|ServiceRequestStatus newQuery()
 * @method static Builder|ServiceRequestStatus onlyTrashed()
 * @method static Builder|ServiceRequestStatus query()
 * @method static Builder|ServiceRequestStatus whereColor($value)
 * @method static Builder|ServiceRequestStatus whereCreatedAt($value)
 * @method static Builder|ServiceRequestStatus whereDeletedAt($value)
 * @method static Builder|ServiceRequestStatus whereId($value)
 * @method static Builder|ServiceRequestStatus whereName($value)
 * @method static Builder|ServiceRequestStatus whereUpdatedAt($value)
 * @method static Builder|ServiceRequestStatus withTrashed()
 * @method static Builder|ServiceRequestStatus withoutTrashed()
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, \Assist\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestTypeFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequestType newModelQuery()
 * @method static Builder|ServiceRequestType newQuery()
 * @method static Builder|ServiceRequestType onlyTrashed()
 * @method static Builder|ServiceRequestType query()
 * @method static Builder|ServiceRequestType whereCreatedAt($value)
 * @method static Builder|ServiceRequestType whereDeletedAt($value)
 * @method static Builder|ServiceRequestType whereId($value)
 * @method static Builder|ServiceRequestType whereName($value)
 * @method static Builder|ServiceRequestType whereUpdatedAt($value)
 * @method static Builder|ServiceRequestType withTrashed()
 * @method static Builder|ServiceRequestType withoutTrashed()
 * @mixin Eloquent
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
 * @property ServiceRequestUpdateDirection $direction
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Assist\ServiceManagement\Models\ServiceRequest|null $serviceRequest
 * @method static \Assist\ServiceManagement\Database\Factories\ServiceRequestUpdateFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequestUpdate newModelQuery()
 * @method static Builder|ServiceRequestUpdate newQuery()
 * @method static Builder|ServiceRequestUpdate onlyTrashed()
 * @method static Builder|ServiceRequestUpdate query()
 * @method static Builder|ServiceRequestUpdate whereCreatedAt($value)
 * @method static Builder|ServiceRequestUpdate whereDeletedAt($value)
 * @method static Builder|ServiceRequestUpdate whereDirection($value)
 * @method static Builder|ServiceRequestUpdate whereId($value)
 * @method static Builder|ServiceRequestUpdate whereInternal($value)
 * @method static Builder|ServiceRequestUpdate whereServiceRequestId($value)
 * @method static Builder|ServiceRequestUpdate whereUpdate($value)
 * @method static Builder|ServiceRequestUpdate whereUpdatedAt($value)
 * @method static Builder|ServiceRequestUpdate withTrashed()
 * @method static Builder|ServiceRequestUpdate withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperServiceRequestUpdate {}
}

namespace Assist\Task\Models{
/**
 * Assist\Task\Models\Task
 *
 * @property string $id
 * @property string $description
 * @property TaskStatus $status
 * @property Carbon|null $due
 * @property string|null $assigned_to
 * @property string|null $created_by
 * @property string|null $concern_type
 * @property string|null $concern_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $assignedTo
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $concern
 * @property-read User|null $createdBy
 * @method static \Assist\Task\Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static Builder|Task newModelQuery()
 * @method static Builder|Task newQuery()
 * @method static Builder|Task onlyTrashed()
 * @method static Builder|Task query()
 * @method static Builder|Task whereAssignedTo($value)
 * @method static Builder|Task whereConcernId($value)
 * @method static Builder|Task whereConcernType($value)
 * @method static Builder|Task whereCreatedAt($value)
 * @method static Builder|Task whereCreatedBy($value)
 * @method static Builder|Task whereDeletedAt($value)
 * @method static Builder|Task whereDescription($value)
 * @method static Builder|Task whereDue($value)
 * @method static Builder|Task whereId($value)
 * @method static Builder|Task whereStatus($value)
 * @method static Builder|Task whereUpdatedAt($value)
 * @method static Builder|Task withTrashed()
 * @method static Builder|Task withoutTrashed()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
 class IdeHelperTask {}
}

namespace Assist\Webhook\Models{
/**
 * Assist\Webhook\Models\InboundWebhook
 *
 * @property string $id
 * @property InboundWebhookSource $source
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

