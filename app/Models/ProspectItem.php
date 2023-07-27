<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ProspectItem
 *
 * @property int $id
 * @property string $first
 * @property string $last
 * @property string $full
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
 * @property string|null $birthdate
 * @property string|null $hsgrad
 * @property string|null $hsdate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $status_id
 * @property int|null $source_id
 * @property int|null $assigned_to_id
 * @property int|null $created_by_id
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \App\Models\User|null $createdBy
 * @property-read mixed $email_bounce_label
 * @property-read mixed $sms_opt_out_label
 * @property-read \App\Models\ProspectSource|null $source
 * @property-read \App\Models\ProspectStatus|null $status
 *
 * @method static Builder|ProspectItem advancedFilter($data)
 * @method static Builder|ProspectItem newModelQuery()
 * @method static Builder|ProspectItem newQuery()
 * @method static Builder|ProspectItem onlyTrashed()
 * @method static Builder|ProspectItem query()
 * @method static Builder|ProspectItem whereAddress($value)
 * @method static Builder|ProspectItem whereAddress2($value)
 * @method static Builder|ProspectItem whereAssignedToId($value)
 * @method static Builder|ProspectItem whereBirthdate($value)
 * @method static Builder|ProspectItem whereCreatedAt($value)
 * @method static Builder|ProspectItem whereCreatedById($value)
 * @method static Builder|ProspectItem whereDeletedAt($value)
 * @method static Builder|ProspectItem whereDescription($value)
 * @method static Builder|ProspectItem whereEmail($value)
 * @method static Builder|ProspectItem whereEmail2($value)
 * @method static Builder|ProspectItem whereEmailBounce($value)
 * @method static Builder|ProspectItem whereFirst($value)
 * @method static Builder|ProspectItem whereFull($value)
 * @method static Builder|ProspectItem whereHsdate($value)
 * @method static Builder|ProspectItem whereHsgrad($value)
 * @method static Builder|ProspectItem whereId($value)
 * @method static Builder|ProspectItem whereLast($value)
 * @method static Builder|ProspectItem whereMobile($value)
 * @method static Builder|ProspectItem wherePhone($value)
 * @method static Builder|ProspectItem wherePreferred($value)
 * @method static Builder|ProspectItem whereSmsOptOut($value)
 * @method static Builder|ProspectItem whereSourceId($value)
 * @method static Builder|ProspectItem whereStatusId($value)
 * @method static Builder|ProspectItem whereUpdatedAt($value)
 * @method static Builder|ProspectItem withTrashed()
 * @method static Builder|ProspectItem withoutTrashed()
 *
 * @mixin Eloquent
 */
class ProspectItem extends BaseModel
{
    use HasAdvancedFilter;
    use SoftDeletes;

    public const SMS_OPT_OUT_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public const EMAIL_BOUNCE_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public static $search = [
        'full',
        'mobile',
        'birthdate',
    ];

    public $orderable = [
        'id',
        'full',
        'email',
        'mobile',
        'birthdate',
    ];

    public $filterable = [
        'id',
        'full',
        'email',
        'mobile',
        'birthdate',
    ];

    protected $dates = [
        'birthdate',
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
        'birthdate',
        'hsgrad',
        'hsdate',
        'assigned_to_id',
        'created_by_id',
    ];

    public function getSmsOptOutLabelAttribute($value)
    {
        return static::SMS_OPT_OUT_RADIO[$this->sms_opt_out] ?? null;
    }

    public function getEmailBounceLabelAttribute($value)
    {
        return static::EMAIL_BOUNCE_RADIO[$this->email_bounce] ?? null;
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProspectStatus::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ProspectSource::class);
    }

    public function getBirthdateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('project.date_format')) : null;
    }

    public function setBirthdateAttribute($value)
    {
        $this->attributes['birthdate'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getHsdateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('project.date_format')) : null;
    }

    public function setHsdateAttribute($value)
    {
        $this->attributes['hsdate'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
