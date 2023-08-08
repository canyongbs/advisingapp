<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Panel;
use DateTimeInterface;
use App\Traits\Auditable;
use Assist\Case\Models\CaseItem;
use App\Support\HasAdvancedFilter;
use Illuminate\Support\Facades\Hash;
use Assist\Authorization\Models\Role;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\HasRoleGroups;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Authorization\Models\Pivots\RoleGroupUserPivot;
use Assist\Authorization\Models\Concerns\HasRolesWithPivot;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $emplid
 * @property string|null $name
 * @property string|null $email
 * @property string|null $email_verified_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property string|null $locale
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAlert> $alerts
 * @property-read int|null $alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CaseItem> $caseItems
 * @property-read int|null $case_items_count
 * @property-read mixed $is_admin
 * @property-read mixed $type_label
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $roleGroups
 * @property-read int|null $role_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $traitRoleGroups
 * @property-read int|null $trait_role_groups_count
 *
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
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements HasLocalePreference, FilamentUser
{
    use HasFactory;
    use HasAdvancedFilter;
    use Notifiable;
    use SoftDeletes;
    use Auditable;
    use HasRoleGroups {
        HasRoleGroups::roleGroups as traitRoleGroups;
    }
    use HasRolesWithPivot;
    use DefinesPermissions;
    use HasRelationships;

    public const TYPE_RADIO = [
        'local' => 'Local',
        'sso' => 'SSO',
    ];

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function roleGroups(): BelongsToMany
    {
        return $this->traitRoleGroups()
            ->using(RoleGroupUserPivot::class);
    }

    // TODO: Fix this to not conflict with Spatie method
    //public function permissions(): HasManyDeep
    //{
    //    return $this->hasManyDeepFromRelations($this->roles(), (new Role())->permissions());
    //}

    public function caseItems(): HasMany
    {
        return $this->hasMany(
            related: CaseItem::class,
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
