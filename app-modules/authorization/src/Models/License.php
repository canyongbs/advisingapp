<?php

namespace AdvisingApp\Authorization\Models;

use App\Models\User;
use App\Models\BaseModel;
use AdvisingApp\Authorization\Enums\LicenseType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class License extends BaseModel
{
    protected $fillable = [
        'type',
    ];

    protected $casts = [
        'type' => LicenseType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
