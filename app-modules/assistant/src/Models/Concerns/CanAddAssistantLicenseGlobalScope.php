<?php

namespace AdvisingApp\Assistant\Models\Concerns;

use App\Models\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Authorization\Enums\LicenseType;

trait CanAddAssistantLicenseGlobalScope
{
    protected static function addAssistantLicenseGlobalScope(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            if (! auth()->check()) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->user();

            if (! $user->hasLicense(LicenseType::ConversationalAi)) {
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
