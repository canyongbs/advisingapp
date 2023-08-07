<?php

namespace Assist\Prospect\Models;

use Carbon\Carbon;
use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Prospect\Models\Prospect
 *
 * @property int $id
 * @property int $status_id
 * @property int $source_id
 * @property int $assigned_to_id
 * @property int $created_by_id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string|null $preferred
 * @property string|null $description
 * @property string|null $email
 * @property string|null $email_2
 * @property int|null $mobile
 * @property string|null $sms_opt_out
 * @property string|null $email_bounce
 * @property int|null $phone
 * @property string|null $address
 * @property string|null $address_2
 * @property string|null $date_of_birth
 * @property string|null $highschool_grad
 * @property string|null $highschool_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User $assignedTo
 * @property-read User $createdBy
 * @property mixed $birthdate
 * @property-read mixed $email_bounce_label
 * @property mixed $hsdate
 * @property-read mixed $sms_opt_out_label
 * @property-read \Assist\Prospect\Models\ProspectSource $source
 * @property-read \Assist\Prospect\Models\ProspectStatus $status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect advancedFilter($data)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect query()
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereAssignedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereEmail2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereEmailBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereHighschoolDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prospect whereHighschoolGrad($value)
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
 *
 * @mixin \Eloquent
 */
class Prospect extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    // TODO If we need this enum, this should exist as an enum
    public const SMS_OPT_OUT_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    // TODO If we need this enum, this should exist as an enum
    public const EMAIL_BOUNCE_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

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

    protected $dates = [
        'date_of_birth',
        'hsdate',
        'created_at',
        'updated_at',
        'deleted_at',
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
        'highschool_grad',
        'highschool_date',
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

    public function getSmsOptOutLabelAttribute($value)
    {
        return static::SMS_OPT_OUT_RADIO[$this->sms_opt_out] ?? null;
    }

    public function getEmailBounceLabelAttribute($value)
    {
        return static::EMAIL_BOUNCE_RADIO[$this->email_bounce] ?? null;
    }

    public function getBirthdateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('project.date_format')) : null;
    }

    public function setBirthdateAttribute($value)
    {
        $this->attributes['date_of_birth'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getHsdateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('project.date_format')) : null;
    }

    public function setHsdateAttribute($value)
    {
        $this->attributes['hsdate'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
