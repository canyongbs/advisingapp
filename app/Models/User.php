<?php

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
use OwenIt\Auditing\Contracts\Auditable;
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
use Assist\ServiceManagement\Models\ServiceRequest;
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
use Assist\Engagement\Models\Concerns\HasManyEngagements;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Authorization\Models\Pivots\RoleGroupUserPivot;
use Assist\Authorization\Models\Concerns\HasRolesWithPivot;
use Assist\Authorization\Models\Concerns\DefinesPermissions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Engagement\Models\Concerns\HasManyEngagementBatches;

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
        'is_division_visible_on_profile' => 'boolean',
        'email_verified_at' => 'datetime',
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
        'are_teams_visible_on_profile',
        'timezone',
        'is_division_visible_on_profile',
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

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(
            related: ServiceRequest::class,
            foreignKey: 'assigned_to_id',
        );
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
        return $this->getFirstTemporaryUrl(now()->addMinutes(5), 'avatar', 'avatar-height-250px');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
