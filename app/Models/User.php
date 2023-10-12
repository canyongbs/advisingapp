<?php

namespace App\Models;

use Filament\Panel;
use DateTimeInterface;
use Assist\Task\Models\Task;
use Assist\Team\Models\Team;
use Assist\Team\Models\TeamUser;
use App\Models\Concerns\CanOrElse;
use App\Support\HasAdvancedFilter;
use Assist\Authorization\Models\Role;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Assistant\Models\AssistantChat;
use Lab404\Impersonate\Models\Impersonate;
use Filament\Models\Contracts\FilamentUser;
use Assist\Notifications\Models\Subscription;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Consent\Models\Concerns\CanConsent;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Assistant\Models\AssistantChatMessageLog;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\HasRoleGroups;
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
class User extends Authenticatable implements HasLocalePreference, FilamentUser, Auditable
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

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    protected $fillable = [
        'emplid',
        'name',
        'email',
        'password',
        'locale',
        'type',
        'calendar_type',
        'calendar_id',
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
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

    public function teams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id')
            ->using(TeamUser::class)
            //TODO: remove this if we support multiple teams
            ->limit(1)
            ->withTimestamps();
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

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
