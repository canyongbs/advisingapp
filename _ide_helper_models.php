<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;
/**
 * App\Models\FailedImportRow
 *
 * @property string $id
 * @property array $data
 * @property string $import_id
 * @property string|null $validation_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Import $import
 * @method static Builder|FailedImportRow newModelQuery()
 * @method static Builder|FailedImportRow newQuery()
 * @method static Builder|FailedImportRow query()
 * @method static Builder|FailedImportRow whereCreatedAt($value)
 * @method static Builder|FailedImportRow whereData($value)
 * @method static Builder|FailedImportRow whereId($value)
 * @method static Builder|FailedImportRow whereImportId($value)
 * @method static Builder|FailedImportRow whereUpdatedAt($value)
 * @method static Builder|FailedImportRow whereValidationError($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperFailedImportRow {}
}

namespace App\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, FailedImportRow> $failedRows
 * @property-read int|null $failed_rows_count
 * @property-read User $user
 * @method static Builder|Import newModelQuery()
 * @method static Builder|Import newQuery()
 * @method static Builder|Import query()
 * @method static Builder|Import whereCompletedAt($value)
 * @method static Builder|Import whereCreatedAt($value)
 * @method static Builder|Import whereFileName($value)
 * @method static Builder|Import whereFilePath($value)
 * @method static Builder|Import whereId($value)
 * @method static Builder|Import whereImporter($value)
 * @method static Builder|Import whereProcessedRows($value)
 * @method static Builder|Import whereSuccessfulRows($value)
 * @method static Builder|Import whereTotalRows($value)
 * @method static Builder|Import whereUpdatedAt($value)
 * @method static Builder|Import whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperImport {}
}

namespace App\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Assist\Division\Models\Division;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Spatie\MediaLibrary\MediaCollections\Models\Media;use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
/**
 * App\Models\NotificationSetting
 *
 * @property string $id
 * @property string $name
 * @property string|null $primary_color
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Division> $divisions
 * @property-read int|null $divisions_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, NotificationSettingPivot> $settings
 * @property-read int|null $settings_count
 * @method static Builder|NotificationSetting newModelQuery()
 * @method static Builder|NotificationSetting newQuery()
 * @method static Builder|NotificationSetting query()
 * @method static Builder|NotificationSetting whereCreatedAt($value)
 * @method static Builder|NotificationSetting whereDescription($value)
 * @method static Builder|NotificationSetting whereId($value)
 * @method static Builder|NotificationSetting whereName($value)
 * @method static Builder|NotificationSetting wherePrimaryColor($value)
 * @method static Builder|NotificationSetting whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperNotificationSetting {}
}

namespace App\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;
/**
 * App\Models\NotificationSettingPivot
 *
 * @property string $id
 * @property string $notification_setting_id
 * @property string $related_to_type
 * @property string $related_to_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $relatedTo
 * @property-read NotificationSetting $setting
 * @method static Builder|NotificationSettingPivot newModelQuery()
 * @method static Builder|NotificationSettingPivot newQuery()
 * @method static Builder|NotificationSettingPivot query()
 * @method static Builder|NotificationSettingPivot whereCreatedAt($value)
 * @method static Builder|NotificationSettingPivot whereId($value)
 * @method static Builder|NotificationSettingPivot whereNotificationSettingId($value)
 * @method static Builder|NotificationSettingPivot whereRelatedToId($value)
 * @method static Builder|NotificationSettingPivot whereRelatedToType($value)
 * @method static Builder|NotificationSettingPivot whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperNotificationSettingPivot {}
}

namespace App\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Database\Factories\PronounsFactory;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
/**
 * App\Models\Pronouns
 *
 * @property string $id
 * @property string $label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @method static PronounsFactory factory($count = null, $state = [])
 * @method static Builder|Pronouns newModelQuery()
 * @method static Builder|Pronouns newQuery()
 * @method static Builder|Pronouns onlyTrashed()
 * @method static Builder|Pronouns query()
 * @method static Builder|Pronouns whereCreatedAt($value)
 * @method static Builder|Pronouns whereDeletedAt($value)
 * @method static Builder|Pronouns whereId($value)
 * @method static Builder|Pronouns whereLabel($value)
 * @method static Builder|Pronouns whereUpdatedAt($value)
 * @method static Builder|Pronouns withTrashed()
 * @method static Builder|Pronouns withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperPronouns {}
}

namespace App\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Spatie\MediaLibrary\MediaCollections\Models\Media;use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
/**
 * App\Models\SettingsProperty
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property mixed $payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @method static Builder|SettingsProperty newModelQuery()
 * @method static Builder|SettingsProperty newQuery()
 * @method static Builder|SettingsProperty query()
 * @method static Builder|SettingsProperty whereCreatedAt($value)
 * @method static Builder|SettingsProperty whereGroup($value)
 * @method static Builder|SettingsProperty whereId($value)
 * @method static Builder|SettingsProperty whereLocked($value)
 * @method static Builder|SettingsProperty whereName($value)
 * @method static Builder|SettingsProperty wherePayload($value)
 * @method static Builder|SettingsProperty whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperSettingsProperty {}
}

namespace App\Models{use Eloquent;use AllowDynamicProperties;use Assist\Team\Models\Team;use Assist\Task\Models\Task;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Database\Factories\UserFactory;use Assist\Prospect\Models\Prospect;use Assist\CareTeam\Models\CareTeam;use Assist\Authorization\Models\Role;use Assist\Engagement\Models\Engagement;use Illuminate\Database\Eloquent\Builder;use Assist\MeetingCenter\Models\Calendar;use Assist\Authorization\Models\RoleGroup;use Assist\AssistDataModel\Models\Student;use Assist\Assistant\Models\AssistantChat;use Assist\Authorization\Models\Permission;use Assist\Consent\Models\ConsentAgreement;use Illuminate\Database\Eloquent\Collection;use Assist\Notifications\Models\Subscription;use Assist\Engagement\Models\EngagementBatch;use Assist\MeetingCenter\Models\CalendarEvent;use Assist\CaseloadManagement\Models\Caseload;use Assist\Assistant\Models\AssistantChatFolder;use Illuminate\Notifications\DatabaseNotification;use Assist\ServiceManagement\Models\ServiceRequest;use Assist\Assistant\Models\AssistantChatMessageLog;use Spatie\MediaLibrary\MediaCollections\Models\Media;use Assist\InAppCommunication\Models\TwilioConversation;use Illuminate\Notifications\DatabaseNotificationCollection;use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
/**
 * App\Models\User
 *
 * @property string $id
 * @property string|null $emplid
 * @property string|null $name
 * @property string|null $email
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
 * @property string|null $pronouns_id
 * @property bool $are_pronouns_visible_on_profile
 * @property bool $default_assistant_chat_folders_created
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Task> $assignedTasks
 * @property-read int|null $assigned_tasks_count
 * @property-read Collection<int, AssistantChatFolder> $assistantChatFolders
 * @property-read int|null $assistant_chat_folders_count
 * @property-read Collection<int, AssistantChatMessageLog> $assistantChatMessageLogs
 * @property-read int|null $assistant_chat_message_logs_count
 * @property-read Collection<int, AssistantChat> $assistantChats
 * @property-read int|null $assistant_chats_count
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Calendar|null $calendar
 * @property-read Collection<int, CareTeam> $careTeams
 * @property-read int|null $care_teams_count
 * @property-read Collection<int, Caseload> $caseloads
 * @property-read int|null $caseloads_count
 * @property-read Collection<int, ConsentAgreement> $consentAgreements
 * @property-read int|null $consent_agreements_count
 * @property-read Collection<int, TwilioConversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read Collection<int, EngagementBatch> $engagementBatches
 * @property-read int|null $engagement_batches_count
 * @property-read Collection<int, Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read Collection<int, CalendarEvent> $events
 * @property-read int|null $events_count
 * @property-read mixed $is_admin
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Pronouns|null $pronouns
 * @property-read Collection<int, Prospect> $prospectCareTeams
 * @property-read int|null $prospect_care_teams_count
 * @property-read Collection<int, Prospect> $prospectSubscriptions
 * @property-read int|null $prospect_subscriptions_count
 * @property-read Collection<int, RoleGroup> $roleGroups
 * @property-read int|null $role_groups_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read Collection<int, Student> $studentCareTeams
 * @property-read int|null $student_care_teams_count
 * @property-read Collection<int, Student> $studentSubscriptions
 * @property-read int|null $student_subscriptions_count
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, Team> $teams
 * @property-read int|null $teams_count
 * @property-read Collection<int, RoleGroup> $traitRoleGroups
 * @property-read int|null $trait_role_groups_count
 * @method static Builder|User admins()
 * @method static Builder|User advancedFilter($data)
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereArePronounsVisibleOnProfile($value)
 * @method static Builder|User whereAreTeamsVisibleOnProfile($value)
 * @method static Builder|User whereAvatarUrl($value)
 * @method static Builder|User whereBio($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDefaultAssistantChatFoldersCreated($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereEmplid($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsBioVisibleOnProfile($value)
 * @method static Builder|User whereIsDivisionVisibleOnProfile($value)
 * @method static Builder|User whereIsExternal($value)
 * @method static Builder|User whereLocale($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePronounsId($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereTimezone($value)
 * @method static Builder|User whereType($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperUser {}
}

namespace Assist\Alert\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Assist\Alert\Enums\AlertStatus;use Assist\Alert\Enums\AlertSeverity;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Alert\Database\Factories\AlertFactory;
/**
 * Assist\Alert\Models\Alert
 *
 * @property-read Student|Prospect $concern
 * @property string $id
 * @property string $concern_type
 * @property string $concern_id
 * @property string $description
 * @property AlertSeverity $severity
 * @property AlertStatus $status
 * @property string $suggested_intervention
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @method static AlertFactory factory($count = null, $state = [])
 * @method static Builder|Alert newModelQuery()
 * @method static Builder|Alert newQuery()
 * @method static Builder|Alert onlyTrashed()
 * @method static Builder|Alert query()
 * @method static Builder|Alert status(AlertStatus $status)
 * @method static Builder|Alert whereConcernId($value)
 * @method static Builder|Alert whereConcernType($value)
 * @method static Builder|Alert whereCreatedAt($value)
 * @method static Builder|Alert whereDeletedAt($value)
 * @method static Builder|Alert whereDescription($value)
 * @method static Builder|Alert whereId($value)
 * @method static Builder|Alert whereSeverity($value)
 * @method static Builder|Alert whereStatus($value)
 * @method static Builder|Alert whereSuggestedIntervention($value)
 * @method static Builder|Alert whereUpdatedAt($value)
 * @method static Builder|Alert withTrashed()
 * @method static Builder|Alert withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperAlert {}
}

namespace Assist\Application\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Assist\Form\Enums\Rounding;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
/**
 * Assist\Application\Models\Application
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $embed_enabled
 * @property array|null $allowed_domains
 * @property string|null $primary_color
 * @property Rounding|null $rounding
 * @property bool $is_wizard
 * @property array|null $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read Collection<int, ApplicationStep> $steps
 * @property-read int|null $steps_count
 * @property-read Collection<int, ApplicationSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static Builder|Application newModelQuery()
 * @method static Builder|Application newQuery()
 * @method static Builder|Application query()
 * @method static Builder|Application whereAllowedDomains($value)
 * @method static Builder|Application whereContent($value)
 * @method static Builder|Application whereCreatedAt($value)
 * @method static Builder|Application whereDeletedAt($value)
 * @method static Builder|Application whereDescription($value)
 * @method static Builder|Application whereEmbedEnabled($value)
 * @method static Builder|Application whereId($value)
 * @method static Builder|Application whereIsWizard($value)
 * @method static Builder|Application whereName($value)
 * @method static Builder|Application wherePrimaryColor($value)
 * @method static Builder|Application whereRounding($value)
 * @method static Builder|Application whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperApplication {}
}

namespace Assist\Application\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Application\Models\ApplicationAuthentication
 *
 * @property string $id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $code
 * @property string $application_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $author
 * @property-read Application $submissible
 * @method static Builder|ApplicationAuthentication newModelQuery()
 * @method static Builder|ApplicationAuthentication newQuery()
 * @method static Builder|ApplicationAuthentication query()
 * @method static Builder|ApplicationAuthentication whereApplicationId($value)
 * @method static Builder|ApplicationAuthentication whereAuthorId($value)
 * @method static Builder|ApplicationAuthentication whereAuthorType($value)
 * @method static Builder|ApplicationAuthentication whereCode($value)
 * @method static Builder|ApplicationAuthentication whereCreatedAt($value)
 * @method static Builder|ApplicationAuthentication whereId($value)
 * @method static Builder|ApplicationAuthentication whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperApplicationAuthentication {}
}

namespace Assist\Application\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Application\Models\ApplicationField
 *
 * @property string $id
 * @property string $label
 * @property string $type
 * @property bool $is_required
 * @property array $config
 * @property string $application_id
 * @property string|null $step_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ApplicationStep|null $step
 * @property-read Application $submissible
 * @method static Builder|ApplicationField newModelQuery()
 * @method static Builder|ApplicationField newQuery()
 * @method static Builder|ApplicationField query()
 * @method static Builder|ApplicationField whereApplicationId($value)
 * @method static Builder|ApplicationField whereConfig($value)
 * @method static Builder|ApplicationField whereCreatedAt($value)
 * @method static Builder|ApplicationField whereId($value)
 * @method static Builder|ApplicationField whereIsRequired($value)
 * @method static Builder|ApplicationField whereLabel($value)
 * @method static Builder|ApplicationField whereStepId($value)
 * @method static Builder|ApplicationField whereType($value)
 * @method static Builder|ApplicationField whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperApplicationField {}
}

namespace Assist\Application\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
/**
 * Assist\Application\Models\ApplicationStep
 *
 * @property string $id
 * @property string $label
 * @property array|null $content
 * @property string $application_id
 * @property int $sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read Application $submissible
 * @method static Builder|ApplicationStep newModelQuery()
 * @method static Builder|ApplicationStep newQuery()
 * @method static Builder|ApplicationStep query()
 * @method static Builder|ApplicationStep whereApplicationId($value)
 * @method static Builder|ApplicationStep whereContent($value)
 * @method static Builder|ApplicationStep whereCreatedAt($value)
 * @method static Builder|ApplicationStep whereId($value)
 * @method static Builder|ApplicationStep whereLabel($value)
 * @method static Builder|ApplicationStep whereSort($value)
 * @method static Builder|ApplicationStep whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperApplicationStep {}
}

namespace Assist\Application\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
/**
 * Assist\Application\Models\ApplicationSubmission
 *
 * @property string $id
 * @property string $application_id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Model|Eloquent $author
 * @property-read Collection<int, ApplicationField> $fields
 * @property-read int|null $fields_count
 * @property-read Application $submissible
 * @method static Builder|ApplicationSubmission newModelQuery()
 * @method static Builder|ApplicationSubmission newQuery()
 * @method static Builder|ApplicationSubmission query()
 * @method static Builder|ApplicationSubmission whereApplicationId($value)
 * @method static Builder|ApplicationSubmission whereAuthorId($value)
 * @method static Builder|ApplicationSubmission whereAuthorType($value)
 * @method static Builder|ApplicationSubmission whereCreatedAt($value)
 * @method static Builder|ApplicationSubmission whereDeletedAt($value)
 * @method static Builder|ApplicationSubmission whereId($value)
 * @method static Builder|ApplicationSubmission whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperApplicationSubmission {}
}

namespace Assist\AssistDataModel\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Database\Eloquent\Builder;use Assist\AssistDataModel\Database\Factories\EnrollmentFactory;
/**
 * Assist\AssistDataModel\Models\Enrollment
 *
 * @method static EnrollmentFactory factory($count = null, $state = [])
 * @method static Builder|Enrollment newModelQuery()
 * @method static Builder|Enrollment newQuery()
 * @method static Builder|Enrollment query()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperEnrollment {}
}

namespace Assist\AssistDataModel\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Database\Eloquent\Builder;use Assist\AssistDataModel\Database\Factories\PerformanceFactory;
/**
 * Assist\AssistDataModel\Models\Performance
 *
 * @property-read Student|null $student
 * @method static PerformanceFactory factory($count = null, $state = [])
 * @method static Builder|Performance newModelQuery()
 * @method static Builder|Performance newQuery()
 * @method static Builder|Performance query()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperPerformance {}
}

namespace Assist\AssistDataModel\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Database\Eloquent\Builder;use Assist\AssistDataModel\Database\Factories\ProgramFactory;
/**
 * Assist\AssistDataModel\Models\Program
 *
 * @method static ProgramFactory factory($count = null, $state = [])
 * @method static Builder|Program newModelQuery()
 * @method static Builder|Program newQuery()
 * @method static Builder|Program query()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperProgram {}
}

namespace Assist\AssistDataModel\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Task\Models\Task;use Assist\Audit\Models\Audit;use Assist\Alert\Models\Alert;use Assist\Form\Models\FormSubmission;use Assist\Engagement\Models\Engagement;use Illuminate\Database\Eloquent\Builder;use Assist\Interaction\Models\Interaction;use Illuminate\Database\Eloquent\Collection;use Assist\Engagement\Models\EngagementFile;use Assist\Notifications\Models\Subscription;use Assist\Engagement\Models\EngagementResponse;use Illuminate\Notifications\DatabaseNotification;use Assist\ServiceManagement\Models\ServiceRequest;use Illuminate\Notifications\DatabaseNotificationCollection;use Assist\AssistDataModel\Database\Factories\StudentFactory;
/**
 * Assist\AssistDataModel\Models\Student
 *
 * @property string $display_name
 * @property-read Collection<int, Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, User> $careTeam
 * @property-read int|null $care_team_count
 * @property-read Collection<int, EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read Collection<int, EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read Collection<int, Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read Collection<int, Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read Collection<int, FormSubmission> $formSubmissions
 * @property-read int|null $form_submissions_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, EngagementResponse> $orderedEngagementResponses
 * @property-read int|null $ordered_engagement_responses_count
 * @property-read Collection<int, Engagement> $orderedEngagements
 * @property-read int|null $ordered_engagements_count
 * @property-read Collection<int, Performance> $performances
 * @property-read int|null $performances_count
 * @property-read Collection<int, Program> $programs
 * @property-read int|null $programs_count
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read Collection<int, User> $subscribedUsers
 * @property-read int|null $subscribed_users_count
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, Task> $tasks
 * @property-read int|null $tasks_count
 * @method static StudentFactory factory($count = null, $state = [])
 * @method static Builder|Student newModelQuery()
 * @method static Builder|Student newQuery()
 * @method static Builder|Student query()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperStudent {}
}

namespace Assist\Assistant\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
/**
 * Assist\Assistant\Models\AssistantChat
 *
 * @property string $id
 * @property string $name
 * @property string $user_id
 * @property string|null $assistant_chat_folder_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AssistantChatFolder|null $folder
 * @property-read Collection<int, AssistantChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read User $user
 * @method static Builder|AssistantChat newModelQuery()
 * @method static Builder|AssistantChat newQuery()
 * @method static Builder|AssistantChat query()
 * @method static Builder|AssistantChat whereAssistantChatFolderId($value)
 * @method static Builder|AssistantChat whereCreatedAt($value)
 * @method static Builder|AssistantChat whereId($value)
 * @method static Builder|AssistantChat whereName($value)
 * @method static Builder|AssistantChat whereUpdatedAt($value)
 * @method static Builder|AssistantChat whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperAssistantChat {}
}

namespace Assist\Assistant\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
/**
 * Assist\Assistant\Models\AssistantChatFolder
 *
 * @property string $id
 * @property string $name
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AssistantChat> $chats
 * @property-read int|null $chats_count
 * @property-read User $user
 * @method static Builder|AssistantChatFolder newModelQuery()
 * @method static Builder|AssistantChatFolder newQuery()
 * @method static Builder|AssistantChatFolder query()
 * @method static Builder|AssistantChatFolder whereCreatedAt($value)
 * @method static Builder|AssistantChatFolder whereId($value)
 * @method static Builder|AssistantChatFolder whereName($value)
 * @method static Builder|AssistantChatFolder whereUpdatedAt($value)
 * @method static Builder|AssistantChatFolder whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperAssistantChatFolder {}
}

namespace Assist\Assistant\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
/**
 * Assist\Assistant\Models\AssistantChatMessage
 *
 * @property string $id
 * @property string $assistant_chat_id
 * @property string $message
 * @property AIChatMessageFrom $from
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AssistantChat $chat
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
 */
	#[AllowDynamicProperties]
 class IdeHelperAssistantChatMessage {}
}

namespace Assist\Assistant\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Assistant\Models\AssistantChatMessageLog
 *
 * @property string $id
 * @property string $message
 * @property array $metadata
 * @property string $user_id
 * @property array $request
 * @property int $sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|AssistantChatMessageLog newModelQuery()
 * @method static Builder|AssistantChatMessageLog newQuery()
 * @method static Builder|AssistantChatMessageLog query()
 * @method static Builder|AssistantChatMessageLog whereCreatedAt($value)
 * @method static Builder|AssistantChatMessageLog whereId($value)
 * @method static Builder|AssistantChatMessageLog whereMessage($value)
 * @method static Builder|AssistantChatMessageLog whereMetadata($value)
 * @method static Builder|AssistantChatMessageLog whereRequest($value)
 * @method static Builder|AssistantChatMessageLog whereSentAt($value)
 * @method static Builder|AssistantChatMessageLog whereUpdatedAt($value)
 * @method static Builder|AssistantChatMessageLog whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperAssistantChatMessageLog {}
}

namespace Assist\Audit\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;use Assist\Audit\Database\Factories\AuditFactory;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $auditable
 * @property-read Model|Eloquent $user
 * @method static AuditFactory factory($count = null, $state = [])
 * @method static Builder|Audit newModelQuery()
 * @method static Builder|Audit newQuery()
 * @method static Builder|Audit query()
 * @method static Builder|Audit whereAuditableId($value)
 * @method static Builder|Audit whereAuditableType($value)
 * @method static Builder|Audit whereChangeAgentId($value)
 * @method static Builder|Audit whereChangeAgentType($value)
 * @method static Builder|Audit whereCreatedAt($value)
 * @method static Builder|Audit whereEvent($value)
 * @method static Builder|Audit whereId($value)
 * @method static Builder|Audit whereIpAddress($value)
 * @method static Builder|Audit whereNewValues($value)
 * @method static Builder|Audit whereOldValues($value)
 * @method static Builder|Audit whereTags($value)
 * @method static Builder|Audit whereUpdatedAt($value)
 * @method static Builder|Audit whereUrl($value)
 * @method static Builder|Audit whereUserAgent($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperAudit {}
}

namespace Assist\Authorization\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Authorization\Database\Factories\PermissionFactory;
/**
 * Assist\Authorization\Models\Permission
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static Builder|Permission api()
 * @method static PermissionFactory factory($count = null, $state = [])
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission permission($permissions)
 * @method static Builder|Permission query()
 * @method static Builder|Permission role($roles, $guard = null)
 * @method static Builder|Permission web()
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereGuardName($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereName($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperPermission {}
}

namespace Assist\Authorization\Models\Pivots{use Eloquent;use AllowDynamicProperties;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Authorization\Models\Pivots\RoleGroupRolePivot
 *
 * @method static Builder|RoleGroupRolePivot newModelQuery()
 * @method static Builder|RoleGroupRolePivot newQuery()
 * @method static Builder|RoleGroupRolePivot query()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperRoleGroupRolePivot {}
}

namespace Assist\Authorization\Models\Pivots{use Eloquent;use AllowDynamicProperties;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Authorization\Models\Pivots\RoleGroupUserPivot
 *
 * @method static Builder|RoleGroupUserPivot newModelQuery()
 * @method static Builder|RoleGroupUserPivot newQuery()
 * @method static Builder|RoleGroupUserPivot query()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperRoleGroupUserPivot {}
}

namespace Assist\Authorization\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Authorization\Database\Factories\RoleFactory;
/**
 * Assist\Authorization\Models\Role
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, RoleGroup> $roleGroups
 * @property-read int|null $role_groups_count
 * @property-read Collection<int, RoleGroup> $traitRoleGroups
 * @property-read int|null $trait_role_groups_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static Builder|Role api()
 * @method static RoleFactory factory($count = null, $state = [])
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role permission($permissions)
 * @method static Builder|Role query()
 * @method static Builder|Role superAdmin()
 * @method static Builder|Role web()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereGuardName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperRole {}
}

namespace Assist\Authorization\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Authorization\Database\Factories\RoleGroupFactory;
/**
 * Assist\Authorization\Models\RoleGroup
 *
 * @property string $id
 * @property string $name
 * @property string|null $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static RoleGroupFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperRoleGroup {}
}

namespace Assist\Campaign\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\CaseloadManagement\Models\Caseload;use Assist\Campaign\Database\Factories\CampaignFactory;
/**
 * Assist\Campaign\Models\Campaign
 *
 * @property string $id
 * @property string $user_id
 * @property string $caseload_id
 * @property string $name
 * @property bool $enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, CampaignAction> $actions
 * @property-read int|null $actions_count
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Caseload $caseload
 * @property-read User $user
 * @method static CampaignFactory factory($count = null, $state = [])
 * @method static Builder|Campaign hasNotBeenExecuted()
 * @method static Builder|Campaign newModelQuery()
 * @method static Builder|Campaign newQuery()
 * @method static Builder|Campaign onlyTrashed()
 * @method static Builder|Campaign query()
 * @method static Builder|Campaign whereCaseloadId($value)
 * @method static Builder|Campaign whereCreatedAt($value)
 * @method static Builder|Campaign whereDeletedAt($value)
 * @method static Builder|Campaign whereEnabled($value)
 * @method static Builder|Campaign whereId($value)
 * @method static Builder|Campaign whereName($value)
 * @method static Builder|Campaign whereUpdatedAt($value)
 * @method static Builder|Campaign whereUserId($value)
 * @method static Builder|Campaign withTrashed()
 * @method static Builder|Campaign withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperCampaign {}
}

namespace Assist\Campaign\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Campaign\Enums\CampaignActionType;use Assist\Campaign\Database\Factories\CampaignActionFactory;
/**
 * Assist\Campaign\Models\CampaignAction
 *
 * @property string $id
 * @property string $campaign_id
 * @property CampaignActionType $type
 * @property array $data
 * @property string $execute_at
 * @property string|null $last_execution_attempt_at
 * @property string|null $last_execution_attempt_error
 * @property string|null $successfully_executed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Campaign $campaign
 * @method static Builder|CampaignAction campaignEnabled()
 * @method static CampaignActionFactory factory($count = null, $state = [])
 * @method static Builder|CampaignAction hasNotBeenExecuted()
 * @method static Builder|CampaignAction newModelQuery()
 * @method static Builder|CampaignAction newQuery()
 * @method static Builder|CampaignAction onlyTrashed()
 * @method static Builder|CampaignAction query()
 * @method static Builder|CampaignAction whereCampaignId($value)
 * @method static Builder|CampaignAction whereCreatedAt($value)
 * @method static Builder|CampaignAction whereData($value)
 * @method static Builder|CampaignAction whereDeletedAt($value)
 * @method static Builder|CampaignAction whereExecuteAt($value)
 * @method static Builder|CampaignAction whereId($value)
 * @method static Builder|CampaignAction whereLastExecutionAttemptAt($value)
 * @method static Builder|CampaignAction whereLastExecutionAttemptError($value)
 * @method static Builder|CampaignAction whereSuccessfullyExecutedAt($value)
 * @method static Builder|CampaignAction whereType($value)
 * @method static Builder|CampaignAction whereUpdatedAt($value)
 * @method static Builder|CampaignAction withTrashed()
 * @method static Builder|CampaignAction withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperCampaignAction {}
}

namespace Assist\CareTeam\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\CareTeam\Models\CareTeam
 *
 * @property string $id
 * @property string $user_id
 * @property string $educatable_id
 * @property string $educatable_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $educatable
 * @method static Builder|CareTeam newModelQuery()
 * @method static Builder|CareTeam newQuery()
 * @method static Builder|CareTeam query()
 * @method static Builder|CareTeam whereCreatedAt($value)
 * @method static Builder|CareTeam whereEducatableId($value)
 * @method static Builder|CareTeam whereEducatableType($value)
 * @method static Builder|CareTeam whereId($value)
 * @method static Builder|CareTeam whereUpdatedAt($value)
 * @method static Builder|CareTeam whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperCareTeam {}
}

namespace Assist\CaseloadManagement\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\CaseloadManagement\Enums\CaseloadType;use Assist\CaseloadManagement\Enums\CaseloadModel;use Assist\CaseloadManagement\Database\Factories\CaseloadFactory;
/**
 * Assist\CaseloadManagement\Models\Caseload
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property array|null $filters
 * @property CaseloadModel $model
 * @property CaseloadType $type
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, CaseloadSubject> $subjects
 * @property-read int|null $subjects_count
 * @property-read User $user
 * @method static CaseloadFactory factory($count = null, $state = [])
 * @method static Builder|Caseload newModelQuery()
 * @method static Builder|Caseload newQuery()
 * @method static Builder|Caseload query()
 * @method static Builder|Caseload whereCreatedAt($value)
 * @method static Builder|Caseload whereDeletedAt($value)
 * @method static Builder|Caseload whereDescription($value)
 * @method static Builder|Caseload whereFilters($value)
 * @method static Builder|Caseload whereId($value)
 * @method static Builder|Caseload whereModel($value)
 * @method static Builder|Caseload whereName($value)
 * @method static Builder|Caseload whereType($value)
 * @method static Builder|Caseload whereUpdatedAt($value)
 * @method static Builder|Caseload whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperCaseload {}
}

namespace Assist\CaseloadManagement\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\CaseloadManagement\Models\CaseloadSubject
 *
 * @property string $id
 * @property string $subject_id
 * @property string $subject_type
 * @property string $caseload_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Caseload $caseload
 * @property-read Model|Eloquent $subject
 * @method static Builder|CaseloadSubject newModelQuery()
 * @method static Builder|CaseloadSubject newQuery()
 * @method static Builder|CaseloadSubject query()
 * @method static Builder|CaseloadSubject whereCaseloadId($value)
 * @method static Builder|CaseloadSubject whereCreatedAt($value)
 * @method static Builder|CaseloadSubject whereId($value)
 * @method static Builder|CaseloadSubject whereSubjectId($value)
 * @method static Builder|CaseloadSubject whereSubjectType($value)
 * @method static Builder|CaseloadSubject whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperCaseloadSubject {}
}

namespace Assist\Consent\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Consent\Enums\ConsentAgreementType;use Assist\Consent\Database\Factories\ConsentAgreementFactory;
/**
 * Assist\Consent\Models\ConsentAgreement
 *
 * @property string $id
 * @property ConsentAgreementType $type
 * @property string $title
 * @property string $description
 * @property string $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, UserConsentAgreement> $userConsentAgreements
 * @property-read int|null $user_consent_agreements_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static ConsentAgreementFactory factory($count = null, $state = [])
 * @method static Builder|ConsentAgreement newModelQuery()
 * @method static Builder|ConsentAgreement newQuery()
 * @method static Builder|ConsentAgreement query()
 * @method static Builder|ConsentAgreement whereBody($value)
 * @method static Builder|ConsentAgreement whereCreatedAt($value)
 * @method static Builder|ConsentAgreement whereDeletedAt($value)
 * @method static Builder|ConsentAgreement whereDescription($value)
 * @method static Builder|ConsentAgreement whereId($value)
 * @method static Builder|ConsentAgreement whereTitle($value)
 * @method static Builder|ConsentAgreement whereType($value)
 * @method static Builder|ConsentAgreement whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperConsentAgreement {}
}

namespace Assist\Consent\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
/**
 * Assist\Consent\Models\UserConsentAgreement
 *
 * @property string $id
 * @property string $user_id
 * @property string $consent_agreement_id
 * @property string $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @method static Builder|UserConsentAgreement newModelQuery()
 * @method static Builder|UserConsentAgreement newQuery()
 * @method static Builder|UserConsentAgreement onlyTrashed()
 * @method static Builder|UserConsentAgreement query()
 * @method static Builder|UserConsentAgreement whereConsentAgreementId($value)
 * @method static Builder|UserConsentAgreement whereCreatedAt($value)
 * @method static Builder|UserConsentAgreement whereDeletedAt($value)
 * @method static Builder|UserConsentAgreement whereId($value)
 * @method static Builder|UserConsentAgreement whereIpAddress($value)
 * @method static Builder|UserConsentAgreement whereUpdatedAt($value)
 * @method static Builder|UserConsentAgreement whereUserId($value)
 * @method static Builder|UserConsentAgreement withTrashed()
 * @method static Builder|UserConsentAgreement withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperUserConsentAgreement {}
}

namespace Assist\Division\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Team\Models\Team;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use App\Models\NotificationSettingPivot;use Illuminate\Database\Eloquent\Builder;use Assist\Interaction\Models\Interaction;use Illuminate\Database\Eloquent\Collection;use Assist\Division\Database\Factories\DivisionFactory;
/**
 * Assist\Division\Models\Division
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $code
 * @property string|null $header
 * @property string|null $footer
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read User|null $lastUpdatedBy
 * @property-read NotificationSettingPivot|null $notificationSetting
 * @property-read Collection<int, Team> $teams
 * @property-read int|null $teams_count
 * @method static DivisionFactory factory($count = null, $state = [])
 * @method static Builder|Division newModelQuery()
 * @method static Builder|Division newQuery()
 * @method static Builder|Division onlyTrashed()
 * @method static Builder|Division query()
 * @method static Builder|Division whereCode($value)
 * @method static Builder|Division whereCreatedAt($value)
 * @method static Builder|Division whereCreatedById($value)
 * @method static Builder|Division whereDeletedAt($value)
 * @method static Builder|Division whereDescription($value)
 * @method static Builder|Division whereFooter($value)
 * @method static Builder|Division whereHeader($value)
 * @method static Builder|Division whereId($value)
 * @method static Builder|Division whereLastUpdatedById($value)
 * @method static Builder|Division whereName($value)
 * @method static Builder|Division whereUpdatedAt($value)
 * @method static Builder|Division withTrashed()
 * @method static Builder|Division withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperDivision {}
}

namespace Assist\Engagement\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Assist\Engagement\Database\Factories\EmailTemplateFactory;
/**
 * Assist\Engagement\Models\EmailTemplate
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property array $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static EmailTemplateFactory factory($count = null, $state = [])
 * @method static Builder|EmailTemplate newModelQuery()
 * @method static Builder|EmailTemplate newQuery()
 * @method static Builder|EmailTemplate query()
 * @method static Builder|EmailTemplate whereContent($value)
 * @method static Builder|EmailTemplate whereCreatedAt($value)
 * @method static Builder|EmailTemplate whereDescription($value)
 * @method static Builder|EmailTemplate whereId($value)
 * @method static Builder|EmailTemplate whereName($value)
 * @method static Builder|EmailTemplate whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperEmailTemplate {}
}

namespace Assist\Engagement\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Assist\Timeline\Models\Timeline;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Engagement\Database\Factories\EngagementFactory;
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
 * @property bool $scheduled
 * @property Carbon $deliver_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read EngagementBatch|null $batch
 * @property-read User|null $createdBy
 * @property-read Collection<int, EngagementDeliverable> $deliverables
 * @property-read int|null $deliverables_count
 * @property-read EngagementBatch|null $engagementBatch
 * @property-read Collection<int, EngagementDeliverable> $engagementDeliverables
 * @property-read int|null $engagement_deliverables_count
 * @property-read Timeline|null $timelineRecord
 * @property-read User|null $user
 * @method static EngagementFactory factory($count = null, $state = [])
 * @method static Builder|Engagement hasBeenDelivered()
 * @method static Builder|Engagement hasNotBeenDelivered()
 * @method static Builder|Engagement isNotPartOfABatch()
 * @method static Builder|Engagement isScheduled()
 * @method static Builder|Engagement newModelQuery()
 * @method static Builder|Engagement newQuery()
 * @method static Builder|Engagement query()
 * @method static Builder|Engagement sentToProspect()
 * @method static Builder|Engagement sentToStudent()
 * @method static Builder|Engagement whereBody($value)
 * @method static Builder|Engagement whereCreatedAt($value)
 * @method static Builder|Engagement whereDeliverAt($value)
 * @method static Builder|Engagement whereEngagementBatchId($value)
 * @method static Builder|Engagement whereId($value)
 * @method static Builder|Engagement whereRecipientId($value)
 * @method static Builder|Engagement whereRecipientType($value)
 * @method static Builder|Engagement whereScheduled($value)
 * @method static Builder|Engagement whereSubject($value)
 * @method static Builder|Engagement whereUpdatedAt($value)
 * @method static Builder|Engagement whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperEngagement {}
}

namespace Assist\Engagement\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Engagement\Database\Factories\EngagementBatchFactory;
/**
 * Assist\Engagement\Models\EngagementBatch
 *
 * @property string $id
 * @property string|null $identifier
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read User $user
 * @method static EngagementBatchFactory factory($count = null, $state = [])
 * @method static Builder|EngagementBatch newModelQuery()
 * @method static Builder|EngagementBatch newQuery()
 * @method static Builder|EngagementBatch query()
 * @method static Builder|EngagementBatch whereCreatedAt($value)
 * @method static Builder|EngagementBatch whereId($value)
 * @method static Builder|EngagementBatch whereIdentifier($value)
 * @method static Builder|EngagementBatch whereUpdatedAt($value)
 * @method static Builder|EngagementBatch whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperEngagementBatch {}
}

namespace Assist\Engagement\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Engagement\Enums\EngagementDeliveryStatus;use Assist\Engagement\Enums\EngagementDeliveryMethod;use Assist\Engagement\Database\Factories\EngagementDeliverableFactory;
/**
 * Assist\Engagement\Models\EngagementDeliverable
 *
 * @property string $id
 * @property string $engagement_id
 * @property EngagementDeliveryMethod $channel
 * @property EngagementDeliveryStatus $delivery_status
 * @property Carbon|null $delivered_at
 * @property Carbon|null $last_delivery_attempt
 * @property string|null $delivery_response
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Engagement $engagement
 * @method static EngagementDeliverableFactory factory($count = null, $state = [])
 * @method static Builder|EngagementDeliverable newModelQuery()
 * @method static Builder|EngagementDeliverable newQuery()
 * @method static Builder|EngagementDeliverable query()
 * @method static Builder|EngagementDeliverable whereChannel($value)
 * @method static Builder|EngagementDeliverable whereCreatedAt($value)
 * @method static Builder|EngagementDeliverable whereDeletedAt($value)
 * @method static Builder|EngagementDeliverable whereDeliveredAt($value)
 * @method static Builder|EngagementDeliverable whereDeliveryResponse($value)
 * @method static Builder|EngagementDeliverable whereDeliveryStatus($value)
 * @method static Builder|EngagementDeliverable whereEngagementId($value)
 * @method static Builder|EngagementDeliverable whereId($value)
 * @method static Builder|EngagementDeliverable whereLastDeliveryAttempt($value)
 * @method static Builder|EngagementDeliverable whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperEngagementDeliverable {}
}

namespace Assist\Engagement\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Assist\Prospect\Models\Prospect;use Illuminate\Database\Eloquent\Builder;use Assist\AssistDataModel\Models\Student;use Illuminate\Database\Eloquent\Collection;use Spatie\MediaLibrary\MediaCollections\Models\Media;use Assist\Engagement\Database\Factories\EngagementFileFactory;use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
/**
 * Assist\Engagement\Models\EngagementFile
 *
 * @property string $id
 * @property string $description
 * @property string|null $retention_date
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
 * @method static EngagementFileFactory factory($count = null, $state = [])
 * @method static Builder|EngagementFile newModelQuery()
 * @method static Builder|EngagementFile newQuery()
 * @method static Builder|EngagementFile query()
 * @method static Builder|EngagementFile whereCreatedAt($value)
 * @method static Builder|EngagementFile whereDescription($value)
 * @method static Builder|EngagementFile whereId($value)
 * @method static Builder|EngagementFile whereRetentionDate($value)
 * @method static Builder|EngagementFile whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperEngagementFile {}
}

namespace Assist\Engagement\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Engagement\Models\EngagementFileEntities
 *
 * @property string $engagement_file_id
 * @property string $entity_id
 * @property string $entity_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EngagementFile $engagementFile
 * @property-read Model|Eloquent $entity
 * @method static Builder|EngagementFileEntities newModelQuery()
 * @method static Builder|EngagementFileEntities newQuery()
 * @method static Builder|EngagementFileEntities query()
 * @method static Builder|EngagementFileEntities whereCreatedAt($value)
 * @method static Builder|EngagementFileEntities whereEngagementFileId($value)
 * @method static Builder|EngagementFileEntities whereEntityId($value)
 * @method static Builder|EngagementFileEntities whereEntityType($value)
 * @method static Builder|EngagementFileEntities whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperEngagementFileEntities {}
}

namespace Assist\Engagement\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Assist\Timeline\Models\Timeline;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Engagement\Database\Factories\EngagementResponseFactory;
/**
 * Assist\Engagement\Models\EngagementResponse
 *
 * @property string $id
 * @property string|null $sender_id
 * @property string|null $sender_type
 * @property string|null $content
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Model|Eloquent $sender
 * @property-read Timeline|null $timelineRecord
 * @method static EngagementResponseFactory factory($count = null, $state = [])
 * @method static Builder|EngagementResponse newModelQuery()
 * @method static Builder|EngagementResponse newQuery()
 * @method static Builder|EngagementResponse query()
 * @method static Builder|EngagementResponse sentByProspect()
 * @method static Builder|EngagementResponse sentByStudent()
 * @method static Builder|EngagementResponse whereContent($value)
 * @method static Builder|EngagementResponse whereCreatedAt($value)
 * @method static Builder|EngagementResponse whereId($value)
 * @method static Builder|EngagementResponse whereSenderId($value)
 * @method static Builder|EngagementResponse whereSenderType($value)
 * @method static Builder|EngagementResponse whereSentAt($value)
 * @method static Builder|EngagementResponse whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperEngagementResponse {}
}

namespace Assist\Form\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Assist\Form\Enums\Rounding;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Form\Database\Factories\ApplicationFactory;
/**
 * Assist\Form\Models\Form
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $embed_enabled
 * @property array|null $allowed_domains
 * @property string|null $primary_color
 * @property Rounding|null $rounding
 * @property bool $is_authenticated
 * @property bool $is_wizard
 * @property array|null $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, FormField> $fields
 * @property-read int|null $fields_count
 * @property-read Collection<int, FormStep> $steps
 * @property-read int|null $steps_count
 * @property-read Collection<int, FormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static ApplicationFactory factory($count = null, $state = [])
 * @method static Builder|Form newModelQuery()
 * @method static Builder|Form newQuery()
 * @method static Builder|Form query()
 * @method static Builder|Form whereAllowedDomains($value)
 * @method static Builder|Form whereContent($value)
 * @method static Builder|Form whereCreatedAt($value)
 * @method static Builder|Form whereDeletedAt($value)
 * @method static Builder|Form whereDescription($value)
 * @method static Builder|Form whereEmbedEnabled($value)
 * @method static Builder|Form whereId($value)
 * @method static Builder|Form whereIsAuthenticated($value)
 * @method static Builder|Form whereIsWizard($value)
 * @method static Builder|Form whereName($value)
 * @method static Builder|Form wherePrimaryColor($value)
 * @method static Builder|Form whereRounding($value)
 * @method static Builder|Form whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperForm {}
}

namespace Assist\Form\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Form\Models\FormAuthentication
 *
 * @property string $id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $code
 * @property string $form_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $author
 * @property-read Form $submissible
 * @method static Builder|FormAuthentication newModelQuery()
 * @method static Builder|FormAuthentication newQuery()
 * @method static Builder|FormAuthentication query()
 * @method static Builder|FormAuthentication whereAuthorId($value)
 * @method static Builder|FormAuthentication whereAuthorType($value)
 * @method static Builder|FormAuthentication whereCode($value)
 * @method static Builder|FormAuthentication whereCreatedAt($value)
 * @method static Builder|FormAuthentication whereFormId($value)
 * @method static Builder|FormAuthentication whereId($value)
 * @method static Builder|FormAuthentication whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperFormAuthentication {}
}

namespace Assist\Form\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Assist\Form\Database\Factories\ApplicationFieldFactory;
/**
 * Assist\Form\Models\FormField
 *
 * @property string $id
 * @property string $label
 * @property string $type
 * @property bool $is_required
 * @property array $config
 * @property string $form_id
 * @property string|null $step_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read FormStep|null $step
 * @property-read Form $submissible
 * @method static ApplicationFieldFactory factory($count = null, $state = [])
 * @method static Builder|FormField newModelQuery()
 * @method static Builder|FormField newQuery()
 * @method static Builder|FormField query()
 * @method static Builder|FormField whereConfig($value)
 * @method static Builder|FormField whereCreatedAt($value)
 * @method static Builder|FormField whereFormId($value)
 * @method static Builder|FormField whereId($value)
 * @method static Builder|FormField whereIsRequired($value)
 * @method static Builder|FormField whereLabel($value)
 * @method static Builder|FormField whereStepId($value)
 * @method static Builder|FormField whereType($value)
 * @method static Builder|FormField whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperFormField {}
}

namespace Assist\Form\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;
/**
 * Assist\Form\Models\FormStep
 *
 * @property string $id
 * @property string $label
 * @property array|null $content
 * @property string $form_id
 * @property int $sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, FormField> $fields
 * @property-read int|null $fields_count
 * @property-read Form|null $submissible
 * @method static Builder|FormStep newModelQuery()
 * @method static Builder|FormStep newQuery()
 * @method static Builder|FormStep query()
 * @method static Builder|FormStep whereContent($value)
 * @method static Builder|FormStep whereCreatedAt($value)
 * @method static Builder|FormStep whereFormId($value)
 * @method static Builder|FormStep whereId($value)
 * @method static Builder|FormStep whereLabel($value)
 * @method static Builder|FormStep whereSort($value)
 * @method static Builder|FormStep whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperFormStep {}
}

namespace Assist\Form\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Form\Database\Factories\ApplicationSubmissionFactory;
/**
 * Assist\Form\Models\FormSubmission
 *
 * @property Student|Prospect|null $author
 * @property string $id
 * @property string $form_id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, FormField> $fields
 * @property-read int|null $fields_count
 * @property-read Form $submissible
 * @method static ApplicationSubmissionFactory factory($count = null, $state = [])
 * @method static Builder|FormSubmission newModelQuery()
 * @method static Builder|FormSubmission newQuery()
 * @method static Builder|FormSubmission query()
 * @method static Builder|FormSubmission whereAuthorId($value)
 * @method static Builder|FormSubmission whereAuthorType($value)
 * @method static Builder|FormSubmission whereCreatedAt($value)
 * @method static Builder|FormSubmission whereDeletedAt($value)
 * @method static Builder|FormSubmission whereFormId($value)
 * @method static Builder|FormSubmission whereId($value)
 * @method static Builder|FormSubmission whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperFormSubmission {}
}

namespace Assist\InAppCommunication\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\InAppCommunication\Enums\ConversationType;
/**
 * Assist\InAppCommunication\Models\TwilioConversation
 *
 * @property string $sid
 * @property string|null $friendly_name
 * @property ConversationType $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, User> $participants
 * @property-read int|null $participants_count
 * @method static Builder|TwilioConversation newModelQuery()
 * @method static Builder|TwilioConversation newQuery()
 * @method static Builder|TwilioConversation query()
 * @method static Builder|TwilioConversation whereCreatedAt($value)
 * @method static Builder|TwilioConversation whereFriendlyName($value)
 * @method static Builder|TwilioConversation whereSid($value)
 * @method static Builder|TwilioConversation whereType($value)
 * @method static Builder|TwilioConversation whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperTwilioConversation {}
}

namespace Assist\Interaction\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Assist\Division\Models\Division;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Interaction\Database\Factories\InteractionFactory;
/**
 * Assist\Interaction\Models\Interaction
 *
 * @property string $id
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $user_id
 * @property string|null $interactable_id
 * @property string|null $interactable_type
 * @property string|null $interaction_type_id
 * @property string|null $interaction_relation_id
 * @property string|null $interaction_campaign_id
 * @property string|null $interaction_driver_id
 * @property string|null $interaction_status_id
 * @property string|null $interaction_outcome_id
 * @property string|null $division_id
 * @property Carbon $start_datetime
 * @property Carbon|null $end_datetime
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read InteractionCampaign|null $campaign
 * @property-read Division|null $division
 * @property-read InteractionDriver|null $driver
 * @property-read Model|Eloquent $interactable
 * @property-read InteractionOutcome|null $outcome
 * @property-read InteractionRelation|null $relation
 * @property-read InteractionStatus|null $status
 * @property-read InteractionType|null $type
 * @method static InteractionFactory factory($count = null, $state = [])
 * @method static Builder|Interaction newModelQuery()
 * @method static Builder|Interaction newQuery()
 * @method static Builder|Interaction query()
 * @method static Builder|Interaction whereCreatedAt($value)
 * @method static Builder|Interaction whereDescription($value)
 * @method static Builder|Interaction whereDivisionId($value)
 * @method static Builder|Interaction whereEndDatetime($value)
 * @method static Builder|Interaction whereId($value)
 * @method static Builder|Interaction whereInteractableId($value)
 * @method static Builder|Interaction whereInteractableType($value)
 * @method static Builder|Interaction whereInteractionCampaignId($value)
 * @method static Builder|Interaction whereInteractionDriverId($value)
 * @method static Builder|Interaction whereInteractionOutcomeId($value)
 * @method static Builder|Interaction whereInteractionRelationId($value)
 * @method static Builder|Interaction whereInteractionStatusId($value)
 * @method static Builder|Interaction whereInteractionTypeId($value)
 * @method static Builder|Interaction whereStartDatetime($value)
 * @method static Builder|Interaction whereSubject($value)
 * @method static Builder|Interaction whereUpdatedAt($value)
 * @method static Builder|Interaction whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperInteraction {}
}

namespace Assist\Interaction\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Interaction\Database\Factories\InteractionCampaignFactory;
/**
 * Assist\Interaction\Models\InteractionCampaign
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static InteractionCampaignFactory factory($count = null, $state = [])
 * @method static Builder|InteractionCampaign newModelQuery()
 * @method static Builder|InteractionCampaign newQuery()
 * @method static Builder|InteractionCampaign onlyTrashed()
 * @method static Builder|InteractionCampaign query()
 * @method static Builder|InteractionCampaign whereCreatedAt($value)
 * @method static Builder|InteractionCampaign whereDeletedAt($value)
 * @method static Builder|InteractionCampaign whereId($value)
 * @method static Builder|InteractionCampaign whereName($value)
 * @method static Builder|InteractionCampaign whereUpdatedAt($value)
 * @method static Builder|InteractionCampaign withTrashed()
 * @method static Builder|InteractionCampaign withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperInteractionCampaign {}
}

namespace Assist\Interaction\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Interaction\Database\Factories\InteractionDriverFactory;
/**
 * Assist\Interaction\Models\InteractionDriver
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static InteractionDriverFactory factory($count = null, $state = [])
 * @method static Builder|InteractionDriver newModelQuery()
 * @method static Builder|InteractionDriver newQuery()
 * @method static Builder|InteractionDriver onlyTrashed()
 * @method static Builder|InteractionDriver query()
 * @method static Builder|InteractionDriver whereCreatedAt($value)
 * @method static Builder|InteractionDriver whereDeletedAt($value)
 * @method static Builder|InteractionDriver whereId($value)
 * @method static Builder|InteractionDriver whereName($value)
 * @method static Builder|InteractionDriver whereUpdatedAt($value)
 * @method static Builder|InteractionDriver withTrashed()
 * @method static Builder|InteractionDriver withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperInteractionDriver {}
}

namespace Assist\Interaction\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Interaction\Database\Factories\InteractionOutcomeFactory;
/**
 * Assist\Interaction\Models\InteractionOutcome
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static InteractionOutcomeFactory factory($count = null, $state = [])
 * @method static Builder|InteractionOutcome newModelQuery()
 * @method static Builder|InteractionOutcome newQuery()
 * @method static Builder|InteractionOutcome onlyTrashed()
 * @method static Builder|InteractionOutcome query()
 * @method static Builder|InteractionOutcome whereCreatedAt($value)
 * @method static Builder|InteractionOutcome whereDeletedAt($value)
 * @method static Builder|InteractionOutcome whereId($value)
 * @method static Builder|InteractionOutcome whereName($value)
 * @method static Builder|InteractionOutcome whereUpdatedAt($value)
 * @method static Builder|InteractionOutcome withTrashed()
 * @method static Builder|InteractionOutcome withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperInteractionOutcome {}
}

namespace Assist\Interaction\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Interaction\Database\Factories\InteractionRelationFactory;
/**
 * Assist\Interaction\Models\InteractionRelation
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static InteractionRelationFactory factory($count = null, $state = [])
 * @method static Builder|InteractionRelation newModelQuery()
 * @method static Builder|InteractionRelation newQuery()
 * @method static Builder|InteractionRelation onlyTrashed()
 * @method static Builder|InteractionRelation query()
 * @method static Builder|InteractionRelation whereCreatedAt($value)
 * @method static Builder|InteractionRelation whereDeletedAt($value)
 * @method static Builder|InteractionRelation whereId($value)
 * @method static Builder|InteractionRelation whereName($value)
 * @method static Builder|InteractionRelation whereUpdatedAt($value)
 * @method static Builder|InteractionRelation withTrashed()
 * @method static Builder|InteractionRelation withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperInteractionRelation {}
}

namespace Assist\Interaction\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Interaction\Enums\InteractionStatusColorOptions;use Assist\Interaction\Database\Factories\InteractionStatusFactory;
/**
 * Assist\Interaction\Models\InteractionStatus
 *
 * @property string $id
 * @property string $name
 * @property InteractionStatusColorOptions $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static InteractionStatusFactory factory($count = null, $state = [])
 * @method static Builder|InteractionStatus newModelQuery()
 * @method static Builder|InteractionStatus newQuery()
 * @method static Builder|InteractionStatus onlyTrashed()
 * @method static Builder|InteractionStatus query()
 * @method static Builder|InteractionStatus whereColor($value)
 * @method static Builder|InteractionStatus whereCreatedAt($value)
 * @method static Builder|InteractionStatus whereDeletedAt($value)
 * @method static Builder|InteractionStatus whereId($value)
 * @method static Builder|InteractionStatus whereName($value)
 * @method static Builder|InteractionStatus whereUpdatedAt($value)
 * @method static Builder|InteractionStatus withTrashed()
 * @method static Builder|InteractionStatus withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperInteractionStatus {}
}

namespace Assist\Interaction\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Interaction\Database\Factories\InteractionTypeFactory;
/**
 * Assist\Interaction\Models\InteractionType
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @method static InteractionTypeFactory factory($count = null, $state = [])
 * @method static Builder|InteractionType newModelQuery()
 * @method static Builder|InteractionType newQuery()
 * @method static Builder|InteractionType onlyTrashed()
 * @method static Builder|InteractionType query()
 * @method static Builder|InteractionType whereCreatedAt($value)
 * @method static Builder|InteractionType whereDeletedAt($value)
 * @method static Builder|InteractionType whereId($value)
 * @method static Builder|InteractionType whereName($value)
 * @method static Builder|InteractionType whereUpdatedAt($value)
 * @method static Builder|InteractionType withTrashed()
 * @method static Builder|InteractionType withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperInteractionType {}
}

namespace Assist\KnowledgeBase\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseCategoryFactory;
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
 * @property-read Collection<int, KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static KnowledgeBaseCategoryFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperKnowledgeBaseCategory {}
}

namespace Assist\KnowledgeBase\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Assist\Division\Models\Division;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Spatie\MediaLibrary\MediaCollections\Models\Media;use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseItemFactory;use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
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
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read KnowledgeBaseCategory|null $category
 * @property-read Collection<int, Division> $division
 * @property-read int|null $division_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read KnowledgeBaseQuality|null $quality
 * @property-read KnowledgeBaseStatus|null $status
 * @method static KnowledgeBaseItemFactory factory($count = null, $state = [])
 * @method static Builder|KnowledgeBaseItem newModelQuery()
 * @method static Builder|KnowledgeBaseItem newQuery()
 * @method static Builder|KnowledgeBaseItem query()
 * @method static Builder|KnowledgeBaseItem whereCategoryId($value)
 * @method static Builder|KnowledgeBaseItem whereCreatedAt($value)
 * @method static Builder|KnowledgeBaseItem whereId($value)
 * @method static Builder|KnowledgeBaseItem whereNotes($value)
 * @method static Builder|KnowledgeBaseItem wherePublic($value)
 * @method static Builder|KnowledgeBaseItem whereQualityId($value)
 * @method static Builder|KnowledgeBaseItem whereQuestion($value)
 * @method static Builder|KnowledgeBaseItem whereSolution($value)
 * @method static Builder|KnowledgeBaseItem whereStatusId($value)
 * @method static Builder|KnowledgeBaseItem whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperKnowledgeBaseItem {}
}

namespace Assist\KnowledgeBase\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseQualityFactory;
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
 * @property-read Collection<int, KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static KnowledgeBaseQualityFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperKnowledgeBaseQuality {}
}

namespace Assist\KnowledgeBase\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\KnowledgeBase\Database\Factories\KnowledgeBaseStatusFactory;
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
 * @property-read Collection<int, KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static KnowledgeBaseStatusFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperKnowledgeBaseStatus {}
}

namespace Assist\MeetingCenter\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\MeetingCenter\Enums\CalendarProvider;use Assist\MeetingCenter\Database\Factories\CalendarFactory;
/**
 * Assist\MeetingCenter\Models\Calendar
 *
 * @property string $id
 * @property string|null $name
 * @property CalendarProvider $provider_type
 * @property mixed|null $provider_id
 * @property mixed $provider_email
 * @property mixed $oauth_token
 * @property mixed $oauth_refresh_token
 * @property string $user_id
 * @property Carbon $oauth_token_expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, CalendarEvent> $events
 * @property-read int|null $events_count
 * @property-read User $user
 * @method static CalendarFactory factory($count = null, $state = [])
 * @method static Builder|Calendar newModelQuery()
 * @method static Builder|Calendar newQuery()
 * @method static Builder|Calendar query()
 * @method static Builder|Calendar whereCreatedAt($value)
 * @method static Builder|Calendar whereId($value)
 * @method static Builder|Calendar whereName($value)
 * @method static Builder|Calendar whereOauthRefreshToken($value)
 * @method static Builder|Calendar whereOauthToken($value)
 * @method static Builder|Calendar whereOauthTokenExpiresAt($value)
 * @method static Builder|Calendar whereProviderEmail($value)
 * @method static Builder|Calendar whereProviderId($value)
 * @method static Builder|Calendar whereProviderType($value)
 * @method static Builder|Calendar whereUpdatedAt($value)
 * @method static Builder|Calendar whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperCalendar {}
}

namespace Assist\MeetingCenter\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Assist\MeetingCenter\Database\Factories\CalendarEventFactory;
/**
 * Assist\MeetingCenter\Models\CalendarEvent
 *
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property array|null $attendees
 * @property string|null $provider_id
 * @property string $calendar_id
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Calendar $calendar
 * @method static CalendarEventFactory factory($count = null, $state = [])
 * @method static Builder|CalendarEvent newModelQuery()
 * @method static Builder|CalendarEvent newQuery()
 * @method static Builder|CalendarEvent query()
 * @method static Builder|CalendarEvent whereAttendees($value)
 * @method static Builder|CalendarEvent whereCalendarId($value)
 * @method static Builder|CalendarEvent whereCreatedAt($value)
 * @method static Builder|CalendarEvent whereDescription($value)
 * @method static Builder|CalendarEvent whereEndsAt($value)
 * @method static Builder|CalendarEvent whereId($value)
 * @method static Builder|CalendarEvent whereProviderId($value)
 * @method static Builder|CalendarEvent whereStartsAt($value)
 * @method static Builder|CalendarEvent whereTitle($value)
 * @method static Builder|CalendarEvent whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperCalendarEvent {}
}

namespace Assist\Notifications\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Notifications\Models\Subscription
 *
 * @property string $id
 * @property string $user_id
 * @property string $subscribable_id
 * @property string $subscribable_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $subscribable
 * @property-read User $user
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereSubscribableId($value)
 * @method static Builder|Subscription whereSubscribableType($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @method static Builder|Subscription whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperSubscription {}
}

namespace Assist\Prospect\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Task\Models\Task;use Assist\Audit\Models\Audit;use Assist\Alert\Models\Alert;use Illuminate\Support\Carbon;use Assist\Form\Models\FormSubmission;use Assist\Engagement\Models\Engagement;use Illuminate\Database\Eloquent\Builder;use Assist\Interaction\Models\Interaction;use Illuminate\Database\Eloquent\Collection;use Assist\Engagement\Models\EngagementFile;use Assist\Notifications\Models\Subscription;use Assist\Engagement\Models\EngagementResponse;use Illuminate\Notifications\DatabaseNotification;use Assist\ServiceManagement\Models\ServiceRequest;use Assist\Prospect\Database\Factories\ProspectFactory;use Illuminate\Notifications\DatabaseNotificationCollection;
/**
 * Assist\Prospect\Models\Prospect
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
 * @property-read Collection<int, User> $careTeam
 * @property-read int|null $care_team_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read Collection<int, EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read Collection<int, Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read Collection<int, FormSubmission> $formSubmissions
 * @property-read int|null $form_submissions_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, EngagementResponse> $orderedEngagementResponses
 * @property-read int|null $ordered_engagement_responses_count
 * @property-read Collection<int, Engagement> $orderedEngagements
 * @property-read int|null $ordered_engagements_count
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read ProspectSource $source
 * @property-read ProspectStatus $status
 * @property-read Collection<int, User> $subscribedUsers
 * @property-read int|null $subscribed_users_count
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, Task> $tasks
 * @property-read int|null $tasks_count
 * @method static ProspectFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperProspect {}
}

namespace Assist\Prospect\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Prospect\Database\Factories\ProspectSourceFactory;
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
 * @property-read Collection<int, Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static ProspectSourceFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperProspectSource {}
}

namespace Assist\Prospect\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Prospect\Enums\ProspectStatusColorOptions;use Assist\Prospect\Enums\SystemProspectClassification;use Assist\Prospect\Database\Factories\ProspectStatusFactory;
/**
 * Assist\Prospect\Models\ProspectStatus
 *
 * @property string $id
 * @property SystemProspectClassification $classification
 * @property string $name
 * @property ProspectStatusColorOptions $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Prospect> $prospects
 * @property-read int|null $prospects_count
 * @method static ProspectStatusFactory factory($count = null, $state = [])
 * @method static Builder|ProspectStatus newModelQuery()
 * @method static Builder|ProspectStatus newQuery()
 * @method static Builder|ProspectStatus onlyTrashed()
 * @method static Builder|ProspectStatus query()
 * @method static Builder|ProspectStatus whereClassification($value)
 * @method static Builder|ProspectStatus whereColor($value)
 * @method static Builder|ProspectStatus whereCreatedAt($value)
 * @method static Builder|ProspectStatus whereDeletedAt($value)
 * @method static Builder|ProspectStatus whereId($value)
 * @method static Builder|ProspectStatus whereName($value)
 * @method static Builder|ProspectStatus whereUpdatedAt($value)
 * @method static Builder|ProspectStatus withTrashed()
 * @method static Builder|ProspectStatus withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperProspectStatus {}
}

namespace Assist\ServiceManagement\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Assist\Division\Models\Division;use Illuminate\Database\Eloquent\Builder;use Assist\Interaction\Models\Interaction;use Illuminate\Database\Eloquent\Collection;use Assist\ServiceManagement\Database\Factories\ServiceRequestFactory;
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
 * @property string|null $division_id
 * @property string|null $status_id
 * @property string|null $type_id
 * @property string|null $priority_id
 * @property string|null $assigned_to_id
 * @property string|null $created_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $assignedTo
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read User|null $createdBy
 * @property-read Division|null $division
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read ServiceRequestPriority|null $priority
 * @property-read Collection<int, ServiceRequestUpdate> $serviceRequestUpdates
 * @property-read int|null $service_request_updates_count
 * @property-read ServiceRequestStatus|null $status
 * @property-read ServiceRequestType|null $type
 * @method static ServiceRequestFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequest newModelQuery()
 * @method static Builder|ServiceRequest newQuery()
 * @method static Builder|ServiceRequest onlyTrashed()
 * @method static Builder|ServiceRequest open()
 * @method static Builder|ServiceRequest query()
 * @method static Builder|ServiceRequest whereAssignedToId($value)
 * @method static Builder|ServiceRequest whereCloseDetails($value)
 * @method static Builder|ServiceRequest whereCreatedAt($value)
 * @method static Builder|ServiceRequest whereCreatedById($value)
 * @method static Builder|ServiceRequest whereDeletedAt($value)
 * @method static Builder|ServiceRequest whereDivisionId($value)
 * @method static Builder|ServiceRequest whereId($value)
 * @method static Builder|ServiceRequest wherePriorityId($value)
 * @method static Builder|ServiceRequest whereResDetails($value)
 * @method static Builder|ServiceRequest whereRespondentId($value)
 * @method static Builder|ServiceRequest whereRespondentType($value)
 * @method static Builder|ServiceRequest whereServiceRequestNumber($value)
 * @method static Builder|ServiceRequest whereStatusId($value)
 * @method static Builder|ServiceRequest whereTypeId($value)
 * @method static Builder|ServiceRequest whereUpdatedAt($value)
 * @method static Builder|ServiceRequest withTrashed()
 * @method static Builder|ServiceRequest withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperServiceRequest {}
}

namespace Assist\ServiceManagement\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\ServiceManagement\Database\Factories\ServiceRequestPriorityFactory;
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
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static ServiceRequestPriorityFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperServiceRequestPriority {}
}

namespace Assist\ServiceManagement\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\ServiceManagement\Enums\ColumnColorOptions;use Assist\ServiceManagement\Enums\SystemServiceRequestClassification;use Assist\ServiceManagement\Database\Factories\ServiceRequestStatusFactory;
/**
 * Assist\ServiceManagement\Models\ServiceRequestStatus
 *
 * @property string $id
 * @property SystemServiceRequestClassification $classification
 * @property string $name
 * @property ColumnColorOptions $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static ServiceRequestStatusFactory factory($count = null, $state = [])
 * @method static Builder|ServiceRequestStatus newModelQuery()
 * @method static Builder|ServiceRequestStatus newQuery()
 * @method static Builder|ServiceRequestStatus onlyTrashed()
 * @method static Builder|ServiceRequestStatus query()
 * @method static Builder|ServiceRequestStatus whereClassification($value)
 * @method static Builder|ServiceRequestStatus whereColor($value)
 * @method static Builder|ServiceRequestStatus whereCreatedAt($value)
 * @method static Builder|ServiceRequestStatus whereDeletedAt($value)
 * @method static Builder|ServiceRequestStatus whereId($value)
 * @method static Builder|ServiceRequestStatus whereName($value)
 * @method static Builder|ServiceRequestStatus whereUpdatedAt($value)
 * @method static Builder|ServiceRequestStatus withTrashed()
 * @method static Builder|ServiceRequestStatus withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperServiceRequestStatus {}
}

namespace Assist\ServiceManagement\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\ServiceManagement\Database\Factories\ServiceRequestTypeFactory;
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
 * @property-read Collection<int, ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static ServiceRequestTypeFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperServiceRequestType {}
}

namespace Assist\ServiceManagement\Models{use Eloquent;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;use Assist\ServiceManagement\Database\Factories\ServiceRequestUpdateFactory;
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
 * @property-read ServiceRequest|null $serviceRequest
 * @method static ServiceRequestUpdateFactory factory($count = null, $state = [])
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
 */
	#[AllowDynamicProperties]
 class IdeHelperServiceRequestUpdate {}
}

namespace Assist\Task\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Assist\Audit\Models\Audit;use Illuminate\Support\Carbon;use Assist\Task\Enums\TaskStatus;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Task\Database\Factories\TaskFactory;
/**
 * Assist\Task\Models\Task
 *
 * @property-read Student|Prospect $concern
 * @property string $id
 * @property string $title
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
 * @property-read User|null $createdBy
 * @method static Builder|Task byNextDue()
 * @method static TaskFactory factory($count = null, $state = [])
 * @method static Builder|Task newModelQuery()
 * @method static Builder|Task newQuery()
 * @method static Builder|Task onlyTrashed()
 * @method static Builder|Task open()
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
 * @method static Builder|Task whereTitle($value)
 * @method static Builder|Task whereUpdatedAt($value)
 * @method static Builder|Task withTrashed()
 * @method static Builder|Task withoutTrashed()
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperTask {}
}

namespace Assist\Team\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Assist\Division\Models\Division;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Assist\Team\Database\Factories\TeamFactory;
/**
 * Assist\Team\Models\Team
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $division_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Division|null $division
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static TeamFactory factory($count = null, $state = [])
 * @method static Builder|Team newModelQuery()
 * @method static Builder|Team newQuery()
 * @method static Builder|Team query()
 * @method static Builder|Team whereCreatedAt($value)
 * @method static Builder|Team whereDeletedAt($value)
 * @method static Builder|Team whereDescription($value)
 * @method static Builder|Team whereDivisionId($value)
 * @method static Builder|Team whereId($value)
 * @method static Builder|Team whereName($value)
 * @method static Builder|Team whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperTeam {}
}

namespace Assist\Team\Models{use Eloquent;use App\Models\User;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Team\Models\TeamUser
 *
 * @property string $id
 * @property string $team_id
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team $team
 * @property-read User $user
 * @method static Builder|TeamUser newModelQuery()
 * @method static Builder|TeamUser newQuery()
 * @method static Builder|TeamUser query()
 * @method static Builder|TeamUser whereCreatedAt($value)
 * @method static Builder|TeamUser whereId($value)
 * @method static Builder|TeamUser whereTeamId($value)
 * @method static Builder|TeamUser whereUpdatedAt($value)
 * @method static Builder|TeamUser whereUserId($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperTeamUser {}
}

namespace Assist\Timeline\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Builder;
/**
 * Assist\Timeline\Models\Timeline
 *
 * @property string $id
 * @property string $entity_type
 * @property string $entity_id
 * @property string $timelineable_type
 * @property string $timelineable_id
 * @property string $record_sortable_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $timelineable
 * @method static Builder|Timeline forEntity(Model $entity)
 * @method static Builder|Timeline newModelQuery()
 * @method static Builder|Timeline newQuery()
 * @method static Builder|Timeline query()
 * @method static Builder|Timeline whereCreatedAt($value)
 * @method static Builder|Timeline whereEntityId($value)
 * @method static Builder|Timeline whereEntityType($value)
 * @method static Builder|Timeline whereId($value)
 * @method static Builder|Timeline whereRecordSortableDate($value)
 * @method static Builder|Timeline whereTimelineableId($value)
 * @method static Builder|Timeline whereTimelineableType($value)
 * @method static Builder|Timeline whereUpdatedAt($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperTimeline {}
}

namespace Assist\Webhook\Models{use Eloquent;use AllowDynamicProperties;use Illuminate\Support\Carbon;use Illuminate\Database\Eloquent\Builder;use Assist\Webhook\Enums\InboundWebhookSource;
/**
 * Assist\Webhook\Models\InboundWebhook
 *
 * @property string $id
 * @property InboundWebhookSource $source
 * @property string $event
 * @property string $url
 * @property string $payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|InboundWebhook newModelQuery()
 * @method static Builder|InboundWebhook newQuery()
 * @method static Builder|InboundWebhook query()
 * @method static Builder|InboundWebhook whereCreatedAt($value)
 * @method static Builder|InboundWebhook whereDeletedAt($value)
 * @method static Builder|InboundWebhook whereEvent($value)
 * @method static Builder|InboundWebhook whereId($value)
 * @method static Builder|InboundWebhook wherePayload($value)
 * @method static Builder|InboundWebhook whereSource($value)
 * @method static Builder|InboundWebhook whereUpdatedAt($value)
 * @method static Builder|InboundWebhook whereUrl($value)
 * @mixin Eloquent
 */
	#[AllowDynamicProperties]
 class IdeHelperInboundWebhook {}
}

