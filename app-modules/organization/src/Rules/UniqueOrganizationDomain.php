<?php

namespace AdvisingApp\Organization\Rules;

use AdvisingApp\Organization\Models\Organization;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueOrganizationDomain implements ValidationRule
{
    public function __construct(
        protected ?string $ignoreId = null,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $isDomainInUse = Organization::query()
            ->whereRaw(
                "exists (select 1 from jsonb_array_elements(domains) as element where lower(element->>'domain') = ?)",
                [Str::lower($value)],
            )
            ->when(filled($this->ignoreId), fn (Builder $query) => $query->whereKeyNot($this->ignoreId))
            ->exists();

        if ($isDomainInUse) {
            $fail('This domain is already in use and may not be used a second time.');
        }
    }
}
