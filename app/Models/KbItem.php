<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KbItem extends Model
{
    use HasFactory, HasAdvancedFilter, SoftDeletes, Auditable;

    public $table = 'kb_items';

    public static $search = [
        'question',
    ];

    public const PUBLIC_RADIO = [
        'N' => 'No',
        'Y' => 'Yes',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $orderable = [
        'id',
        'question',
        'quality.rating',
        'status.status',
        'public',
        'category.category',
    ];

    public $filterable = [
        'id',
        'question',
        'quality.rating',
        'status.status',
        'public',
        'category.category',
    ];

    protected $fillable = [
        'question',
        'quality_id',
        'status_id',
        'public',
        'category_id',
        'solution',
        'notes',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function quality()
    {
        return $this->belongsTo(KbItemQuality::class);
    }

    public function status()
    {
        return $this->belongsTo(KbItemStatus::class);
    }

    public function getPublicLabelAttribute($value)
    {
        return static::PUBLIC_RADIO[$this->public] ?? null;
    }

    public function category()
    {
        return $this->belongsTo(KbItemCategory::class);
    }

    public function institution()
    {
        return $this->belongsToMany(Institution::class);
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
