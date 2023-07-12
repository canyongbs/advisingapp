<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperRecordStudentItem
 */
class RecordStudentItem extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    public const DUAL_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public const FERPA_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public const FIRSTGEN_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public const SMS_OPT_OUT_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public const EMAIL_BOUNCE_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    public $table = 'record_student_items';

    public $orderable = [
        'sisid',
        'otherid',
        'full',
        'preferred',
        'mobile',
    ];

    public $filterable = [
        'sisid',
        'otherid',
        'full',
        'preferred',
        'mobile',
    ];

    public static $search = [
        'sisid',
        'otherid',
        'full',
        'mobile',
        'birthdate',
    ];

    protected $dates = [
        'birthdate',
        'lastlmslogin',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'sisid',
        'otherid',
        'first',
        'last',
        'full',
        'preferred',
        'email',
        'email_2',
        'mobile',
        'sms_opt_out',
        'email_bounce',
        'phone',
        'address',
        'address_2',
        'birthdate',
        'hsgrad',
        'dual',
        'ferpa',
        'gpa',
        'dfw',
        'firstgen',
        'ethnicity',
        'lastlmslogin',
    ];

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
        $this->attributes['birthdate'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getDualLabelAttribute($value)
    {
        return static::DUAL_RADIO[$this->dual] ?? null;
    }

    public function getFerpaLabelAttribute($value)
    {
        return static::FERPA_RADIO[$this->ferpa] ?? null;
    }

    public function getFirstgenLabelAttribute($value)
    {
        return static::FIRSTGEN_RADIO[$this->firstgen] ?? null;
    }

    public function getLastlmsloginAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function setLastlmsloginAttribute($value)
    {
        $this->attributes['lastlmslogin'] = $value ? Carbon::createFromFormat(config('project.datetime_format'), $value)->format('Y-m-d H:i:s') : null;
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
