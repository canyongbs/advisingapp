<?php

namespace Assist\KnowledgeBase\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeBaseQuality extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public $orderable = [
        'id',
        'name',
    ];

    public $filterable = [
        'id',
        'name',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
