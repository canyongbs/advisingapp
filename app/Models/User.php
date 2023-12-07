<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace App\Models;

use Filament\Panel;
use DateTimeInterface;
use Assist\Task\Models\Task;
use Assist\Team\Models\Team;
use Assist\Team\Models\TeamUser;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Concerns\CanOrElse;
use App\Support\HasAdvancedFilter;
use Assist\CareTeam\Models\CareTeam;
use Assist\Prospect\Models\Prospect;
use Assist\Authorization\Models\Role;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Assist\Auditing\Contracts\Auditable;
use Assist\MeetingCenter\Models\Calendar;
use Assist\Assistant\Models\AssistantChat;
use Assist\AssistDataModel\Models\Student;
use Lab404\Impersonate\Models\Impersonate;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use Assist\Notifications\Models\Subscription;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\Consent\Models\Concerns\CanConsent;
use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\Assistant\Models\AssistantChatFolder;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Assistant\Models\AssistantChatMessageLog;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Assist\Authorization\Models\Concerns\HasRoleGroups;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Assist\InAppCommunication\Models\TwilioConversation;
use Assist\Engagement\Models\Concerns\HasManyEngagements;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Authorization\Models\Pivots\RoleGroupUserPivot;
use Assist\Authorization\Models\Concerns\HasRolesWithPivot;
use Assist\Authorization\Models\Concerns\DefinesPermissions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\ServiceManagement\Models\ServiceRequestAssignment;
use Assist\Engagement\Models\Concerns\HasManyEngagementBatches;
use Assist\ServiceManagement\Enums\ServiceRequestAssignmentStatus;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements HasLocalePreference, FilamentUser, Auditable, HasMedia, HasAvatar
{
    use HasFactory;
    use HasAdvancedFilter;
    use Notifiable;
    use SoftDeletes;
    use HasRoleGroups {
        HasRoleGroups::roleGroups as traitRoleGroups;
    }
    use HasRolesWithPivot;
    use DefinesPermissions;
    use HasRelationships;
    use HasUuids;
    use AuditableTrait;
    use HasManyEngagements;
    use HasManyEngagementBatches;
    use CanOrElse;
    use CanConsent;
    use Impersonate;
    use InteractsWithMedia;

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'is_bio_visible_on_profile' => 'boolean',
        'are_pronouns_visible_on_profile' => 'boolean',
        'are_teams_visible_on_profile' => 'boolean',
        'default_assistant_chat_folders_created' => 'boolean',
        'is_division_visible_on_profile' => 'boolean',
        'email_verified_at' => 'datetime',
        'has_enabled_public_profile' => 'boolean',
        'office_hours_are_enabled' => 'boolean',
        'appointments_are_restricted_to_existing_students' => 'boolean',
        'office_hours' => 'array',
        'out_of_office_is_enabled' => 'boolean',
        'out_of_office_starts_at' => 'datetime',
        'out_of_office_ends_at' => 'datetime',
    ];

    protected $fillable = [
        'emplid',
        'name',
        'email',
        'password',
        'locale',
        'type',
        'is_external',
        'bio',
        'is_bio_visible_on_profile',
        'are_pronouns_visible_on_profile',
        'default_assistant_chat_folders_created',
        'avatar_url',
        'are_teams_visible_on_profile',
        'timezone',
        'is_division_visible_on_profile',
        'has_enabled_public_profile',
        'public_profile_slug',
        'office_hours_are_enabled',
        'appointments_are_restricted_to_existing_students',
        'office_hours',
        'out_of_office_is_enabled',
        'out_of_office_starts_at',
        'out_of_office_ends_at',
    ];

    public $orderable = [
        'id',
        'emplid',
        'name',
        'email',
        'email_verified_at',
        'locale',
    ];

    public $filterable = [
        'id',
        'emplid',
        'name',
        'email',
        'email_verified_at',
        'roles.title',
        'locale',
    ];

    public function defaultAssistantChatFoldersHaveBeenCreated(): bool
    {
        return $this->default_assistant_chat_folders_created;
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(TwilioConversation::class, 'twilio_conversation_user', 'user_id', 'conversation_sid')
            ->withPivot('participant_sid')
            ->withTimestamps()
            ->as('participant');
    }

    public function caseloads(): HasMany
    {
        return $this->hasMany(Caseload::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function prospectSubscriptions(): MorphToMany
    {
        return $this->morphedByMany(
            related: Prospect::class,
            name: 'subscribable',
            table: 'subscriptions'
        )
            ->using(Subscription::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function studentSubscriptions(): MorphToMany
    {
        return $this->morphedByMany(
            related: Student::class,
            name: 'subscribable',
            table: 'subscriptions'
        )
            ->using(Subscription::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function prospectCareTeams(): MorphToMany
    {
        return $this->morphedByMany(
            related: Prospect::class,
            name: 'educatable',
            table: 'care_teams'
        )
            ->using(CareTeam::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function studentCareTeams(): MorphToMany
    {
        return $this->morphedByMany(
            related: Student::class,
            name: 'educatable',
            table: 'care_teams'
        )
            ->using(CareTeam::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function careTeams(): HasMany
    {
        return $this->hasMany(CareTeam::class);
    }

    public function roleGroups(): BelongsToMany
    {
        return $this->traitRoleGroups()
            ->using(RoleGroupUserPivot::class);
    }

    public function permissionsFromRoles(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->roles(), (new Role())->permissions());
    }

    public function serviceRequestAssignments(): HasMany
    {
        return $this->hasMany(ServiceRequestAssignment::class)
            ->where('status', ServiceRequestAssignmentStatus::Active);
    }

    public function serviceRequests(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->serviceRequestAssignments(), (new ServiceRequestAssignment())->serviceRequest());
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('title', 'Admin')->exists();
    }

    public function scopeAdmins()
    {
        return $this->whereHas('roles', fn ($q) => $q->where('title', 'Admin'));
    }

    public function pronouns(): BelongsTo
    {
        return $this->belongsTo(Pronouns::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function preferredLocale()
    {
        return $this->locale;
    }

    public function assistantChats(): HasMany
    {
        return $this->hasMany(AssistantChat::class);
    }

    public function assistantChatFolders(): HasMany
    {
        return $this->hasMany(AssistantChatFolder::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function teams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id')
            ->using(TeamUser::class)
            //TODO: remove this if we support multiple teams
            ->limit(1)
            ->withTimestamps();
    }

    public function calendar(): HasOne
    {
        return $this->hasOne(Calendar::class);
    }

    public function assistantChatMessageLogs(): HasMany
    {
        return $this->hasMany(AssistantChatMessageLog::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function canImpersonate(): bool
    {
        return $this->can('authorization.impersonate');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('authorization.super_admin');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('avatar-height-250px')
            ->performOnCollections('avatar')
            ->height(250);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ?: $this->getFirstTemporaryUrl(now()->addMinutes(5), 'avatar', 'avatar-height-250px');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
