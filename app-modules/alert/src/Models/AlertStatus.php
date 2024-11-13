<?php

namespace AdvisingApp\Alert\Models;

use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlertStatus extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'classification',
        'name',
    ];

    protected $casts = [
        'classification' => SystemAlertStatusClassification::class,
    ];

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'status');
    }
}
