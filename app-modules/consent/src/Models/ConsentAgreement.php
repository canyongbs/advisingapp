<?php

namespace Assist\Consent\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Audit\Overrides\BelongsToMany;
use Assist\Consent\Enums\ConsentAgreementType;

class ConsentAgreement extends BaseModel
{
    protected $casts = [
        'type' => ConsentAgreementType::class,
    ];

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('ip_address')
            ->withTimestamps();
    }
}
