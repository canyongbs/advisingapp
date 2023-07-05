<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProspectItem extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes, Auditable;

    public $table = 'prospect_items';

    public static $search = [
        'full',
        'mobile',
        'birthdate',
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getSmsOptOutLabelAttribute($value)
    {
        return static::SMS_OPT_OUT_RADIO[$this->sms_opt_out] ?? null;
    }

    public function getEmailBounceLabelAttribute($value)
    {
        return static::EMAIL_BOUNCE_RADIO[$this->email_bounce] ?? null;
    }

    public function status()
    {
        return $this->belongsTo(ProspectStatus::class);
    }

    public function source()
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

    public function assignedTo()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
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
}
