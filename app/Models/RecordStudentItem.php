<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\RecordStudentItem
 *
 * @property int $id
 * @property string $sisid
 * @property string|null $otherid
 * @property string|null $first
 * @property string|null $last
 * @property string|null $full
 * @property string|null $preferred
 * @property string|null $email
 * @property string|null $email_2
 * @property int|null $mobile
 * @property string|null $sms_opt_out
 * @property string|null $email_bounce
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $address_2
 * @property string|null $birthdate
 * @property int|null $hsgrad
 * @property string|null $dual
 * @property string|null $ferpa
 * @property float|null $gpa
 * @property string|null $dfw
 * @property string|null $firstgen
 * @property string|null $ethnicity
 * @property string|null $lastlmslogin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $deleted_at
 * @property-read mixed $dual_label
 * @property-read mixed $email_bounce_label
 * @property-read mixed $ferpa_label
 * @property-read mixed $firstgen_label
 * @property-read mixed $sms_opt_out_label
 *
 * @method static Builder|RecordStudentItem advancedFilter($data)
 * @method static Builder|RecordStudentItem newModelQuery()
 * @method static Builder|RecordStudentItem newQuery()
 * @method static Builder|RecordStudentItem query()
 * @method static Builder|RecordStudentItem whereAddress($value)
 * @method static Builder|RecordStudentItem whereAddress2($value)
 * @method static Builder|RecordStudentItem whereBirthdate($value)
 * @method static Builder|RecordStudentItem whereCreatedAt($value)
 * @method static Builder|RecordStudentItem whereDfw($value)
 * @method static Builder|RecordStudentItem whereDual($value)
 * @method static Builder|RecordStudentItem whereEmail($value)
 * @method static Builder|RecordStudentItem whereEmail2($value)
 * @method static Builder|RecordStudentItem whereEmailBounce($value)
 * @method static Builder|RecordStudentItem whereEthnicity($value)
 * @method static Builder|RecordStudentItem whereFerpa($value)
 * @method static Builder|RecordStudentItem whereFirst($value)
 * @method static Builder|RecordStudentItem whereFirstgen($value)
 * @method static Builder|RecordStudentItem whereFull($value)
 * @method static Builder|RecordStudentItem whereGpa($value)
 * @method static Builder|RecordStudentItem whereHsgrad($value)
 * @method static Builder|RecordStudentItem whereId($value)
 * @method static Builder|RecordStudentItem whereLast($value)
 * @method static Builder|RecordStudentItem whereLastlmslogin($value)
 * @method static Builder|RecordStudentItem whereMobile($value)
 * @method static Builder|RecordStudentItem whereOtherid($value)
 * @method static Builder|RecordStudentItem wherePhone($value)
 * @method static Builder|RecordStudentItem wherePreferred($value)
 * @method static Builder|RecordStudentItem whereSisid($value)
 * @method static Builder|RecordStudentItem whereSmsOptOut($value)
 * @method static Builder|RecordStudentItem whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class RecordStudentItem extends BaseModel
{
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
