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
 * @mixin IdeHelperProspect
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
