<?php

namespace Assist\Prospect\Models;

use Eloquent;
use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Prospect\Models\Prospect
 *
 * @property int $id
 * @property int $status_id
 * @property int $source_id
 * @property string $first_name
 * @property string $last_name
 * @property string $full
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
 * @property int $assigned_to_id
 * @property int|null $created_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $assignedTo
 * @property-read User|null $createdBy
 * @property-read \Assist\Prospect\Models\ProspectSource $source
 * @property-read \Assist\Prospect\Models\ProspectStatus $status
 *
 * @method static Builder|Prospect advancedFilter($data)
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
 * @method static Builder|Prospect whereFull($value)
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
 *
 * @mixin Eloquent
 */
class Prospect extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public static $search = [
        'full',
        'mobile',
        'date_of_birth',
    ];

    public $orderable = [
        'id',
        'full',
        'email',
        'mobile',
        'date_of_birth',
    ];

    public $filterable = [
        'id',
        'full',
        'email',
        'mobile',
        'date_of_birth',
    ];

    protected $fillable = [
        'first',
        'last',
        'full',
        'preferred',
        'description',
        'email',
        'email_2',
        'mobile',
        'sms_opt_out',
        'email_bounce',
        'status_id',
        'source_id',
        'phone',
        'address',
        'address_2',
        'date_of_birth',
        'hsgrad',
        'assigned_to_id',
        'created_by_id',
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProspectStatus::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ProspectSource::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
