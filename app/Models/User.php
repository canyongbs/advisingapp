<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use Filament\Panel;
use DateTimeInterface;
use Assist\Audit\Models\Audit;
use App\Models\Concerns\CanOrElse;
use App\Support\HasAdvancedFilter;
use Illuminate\Support\Facades\Hash;
use Assist\Authorization\Models\Role;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\Assistant\Models\AssistantChat;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Permission;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Collection;
use Assist\Notifications\Models\Subscription;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Consent\Models\Concerns\CanConsent;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Notifications\DatabaseNotification;
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
use Illuminate\Notifications\DatabaseNotificationCollection;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Engagement\Models\Concerns\HasManyEngagementBatches;

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
 * @property-read Collection<int, AssistantChatMessageLog> $assistantChatMessageLogs
 * @property-read int|null $assistant_chat_message_logs_count
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
 *
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
 *
 * @mixin Eloquent
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

    public const TYPE_RADIO = [
        'local' => 'Local',
        'sso' => 'SSO',
    ];

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

    public function alerts()
    {
        return $this->belongsToMany(UserAlert::class)->withPivot('seen_at');
    }

    public function preferredLocale()
    {
        return $this->locale;
    }

    public function assistantChats(): HasMany
    {
        return $this->hasMany(AssistantChat::class);
    }

    public function assistantChatMessageLogs(): HasMany
    {
        return $this->hasMany(AssistantChatMessageLog::class);
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = Hash::needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function getTypeLabelAttribute($value)
    {
        return static::TYPE_RADIO[$this->type] ?? null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
